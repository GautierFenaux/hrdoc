const reminderLinks = document.querySelectorAll('.reminder');
const managerSignatureLinks = document.querySelectorAll('.ajax-manual-signature');
const xMarks = document.querySelectorAll('.xmark-fj');
const regex = /(\d+)-/;
const urlOrigin = window.location.origin;
const urlOriginManager = window.location.origin + '/manager/';
const urlOriginRh = window.location.origin + '/rh/';
const urlPath = window.location.pathname;
const replaceButton = (buttonToReplace, faClass1, faClass2) => {
    const newDisabledButton = document.createElement('a');
    const newIconElement = document.createElement('i');
    newDisabledButton.classList.add('btn-table-form', 'btn-disabled', 'link-icon');
    newIconElement.classList.add(faClass1, faClass2);
    newDisabledButton.appendChild(newIconElement);
    buttonToReplace.replaceWith(newDisabledButton);
}
const addBackdropLoader = () => {
    const backdropLoader = document.createElement('div');
    backdropLoader.classList.add('backdrop-loader');
    const loader = document.createElement('div');
    loader.classList.add('loader');
    const paragraph = document.createElement('p');
    paragraph.innerHTML = 'Validation en cours, veuillez patienter'
    loader.appendChild(paragraph);
    backdropLoader.appendChild(loader);
    return backdropLoader;
}
const addNotification = (data, error = null) => {
    const text = data !== null ? data : error;
    Toastify({
        text: text,
        duration: 3000,
        newWindow: true,
        close: true,
        gravity: "top",
        position: "right",
        stopOnFocus: true,
        style: {
            background: "linear-gradient(to right, #1f2b51, #00A0A6)",
        },
        onClick: function () { }
    }).showToast();
    const toast = document.querySelector('.toastify.on.toastify-right.toastify-top')
    if (error) {
        toast.style.background = "red";
    } else {
        toast.style.background = "#1f2b51"
    }
}
reminderLinks.forEach((reminderLink) => {
    let name = reminderLink.getAttribute("data-id");
    const id = regex.exec(reminderLink.getAttribute("data-id"))[1];
    const parts = name.split('-'); // Split the innerHTML by "-"
    const lastName = parts[1].trim(); // Get the second part (after the "-") and remove leading/trailing whitespaces
    name = lastName;
    let status = reminderLink.getAttribute("data-status");
    let urlSegment = window.location.pathname.split('/rh/')[1].split('reminder')[0];
    reminderLink.addEventListener('click', (e) => {
        let text;
        let confirmReminder;
        if (e.target.parentElement.getAttribute('data-status') == 'attente-manager') {
            confirmReminder = confirm(`Etes vous sur de vouloir envoyer un mail de relance au manager de ${e.target.parentElement.getAttribute('data-id').split('-')[1]} ?`);
        } else {
            confirmReminder = confirm(`Etes vous sur de vouloir envoyer un mail de relance à ${e.target.parentElement.getAttribute('data-id').split('-')[1]} ?`);
        };
        if (status === 'attente-manager') {
            text = `Un mail de relance a été envoyé au manager de ${name}`;
        } else {
            text = `Un mail de relance a été envoyé  à ${name} `
        }
        if (confirmReminder) 
        {
            fetch(`/rh/${urlSegment}reminder/${id}/${status}`)
                .then(response => {
                    if (response.ok) {
                        addNotification(text)
                    } else {
                        console.error('Une erreur côté serveur est survenue. Merci de contacter un administrateur.');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la requête fetch:', error);
                });
        }
    });
});
managerSignatureLinks.forEach((signatureLink) => {
    const dataId = signatureLink.getAttribute("data-id");
    signatureLink.addEventListener('click', () => {
        if (urlPath.includes('/manager/cet')) {
            acceptValidation(`${urlOriginManager}cet/${dataId}/edit`, signatureLink)
        } else if ((urlPath).includes('/manager/astreinte')) {
            acceptValidation(`${urlOriginManager}astreinte/${dataId}/edit`, signatureLink)
        } else if ((urlPath).includes('/rh/cet')) {
            acceptValidation(`${urlOriginRh}cet/${dataId}/edit`, signatureLink)
        } else if ((urlPath).includes('rh/retour-sur-site')) {
            acceptValidation(`${urlOriginRh}retour-sur-site/${dataId}/edit`, signatureLink)
        }
    })
})
xMarks.forEach((mark) => {
    mark.addEventListener('click', () => {
        const dataId = mark.getAttribute("data-id");
        if ((urlPath).includes('cet') && !(urlPath).includes('rh')) {
            refuseValidation(`${urlOriginManager}cet/${dataId}/edit`, mark)
        } else if ((urlPath).includes('cet') && (urlPath).includes('rh')) {
            if (mark.classList.contains('open')) {
                refuseValidation(`${urlOriginRh}cet/${dataId}/reopen`, mark)
            } else {
                refuseValidation(`${urlOriginRh}cet/${dataId}/edit`, mark)
            }
        } else if ((urlPath).includes('rh') && (urlPath).includes('teletravailform')) {
            refuseValidation(`${urlOriginRh}/teletravailform/${dataId}/reopen`, mark)
        } else if ((urlPath).includes('rh/retour-sur-site')) {
            refuseValidation(`${urlOriginRh}retour-sur-site/${dataId}/edit`, mark)
        } else if ((urlPath).includes('manager/astreinte')) {
            if (mark.classList.contains('open')) {
                refuseValidation(`${urlOriginManager}astreinte/${dataId}/reopen`, mark)
            } else {
                refuseValidation(`${urlOriginManager}astreinte/${dataId}/edit`, mark)
            }
        }
    })
})
const acceptValidation = (url, signatureLink) => {
    let confirmation;
    let comment;
    const backdropLoader = addBackdropLoader();
    if (url.includes('rh/retour-sur-site/')) {
        confirmation = confirm('Etes vous sur de vouloir valider cette demande ?');
    } else {
        comment = prompt('Etes vous sur de vouloir valider cette demande ? \n\nAjouter un commentaire si besoin :');
    }
    if (confirmation || comment || comment == '') {
        document.body.appendChild(backdropLoader);
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                comment: comment
            })
        }).then(response => {
            if (response.ok) {
                backdropLoader.remove();
                return response.json();
            } else {
                throw new Error('Une erreur côté serveur est survenue. Merci de contacter un administrateur.'); // Throw an error for non-successful responses
            }
        }).then(data => {
            addNotification(data)
            const trElement = document.querySelector(`tr[data-id="${signatureLink.getAttribute("data-id")}"]`);
            const tdStateElement = trElement.querySelector('.state');
            if (url.includes('cet')) tdStateElement.innerHTML = "attente-rh";
            if (url.includes('rh')) tdStateElement.innerHTML = "validé-rh";
            if (url.includes('manager/astreinte')) tdStateElement.innerHTML = "validé-manager";
            replaceButton(trElement.querySelector('.signature-button', 'fas', 'fa-file-signature'), 'fas', 'fa-file-signature')
            trElement.querySelector('.open') != null ? replaceButton(trElement.querySelector('.open'), 'fas', 'fa-undo') : null;
            trElement.querySelector('.refuse') != null ? replaceButton(trElement.querySelector('.refuse'), 'fa-solid', 'fa-xmark') : null;
        })
            .catch(error => {
                backdropLoader.remove();
                addNotification(null, error)
            });
    }
}

const refuseValidation = (url, mark) => {
    let stateContent;
    let comment = prompt('Etes vous sur de vouloir réouvrir cette demande ? \n\nAjouter un commentaire (obligatoire) :');

    const backdropLoader = addBackdropLoader();

    if (url.includes('reopen')) {
        stateContent = 'réouvert';
    } else {
        stateContent = "refus";
    }

    if (comment) {
        document.body.appendChild(backdropLoader);
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                comment: comment,
                isValidated: false,
            })
        })
            .then(response => {
                if (response.ok) {
                    backdropLoader.remove();

                    return response.json();
                } else {
                    throw new Error('Erreur');
                }
            })
            .then(data => {
                addNotification(data);
                const trElement = document.querySelector(`tr[data-id="${mark.getAttribute("data-id")}"]`);
                const tdStateElement = trElement.querySelector('.state');
                tdStateElement.innerHTML = stateContent;
                replaceButton(trElement.querySelector('.signature-button', 'fas', 'fa-file-signature'), 'fas', 'fa-file-signature');
                trElement.querySelector('.open') != null ? replaceButton(trElement.querySelector('.open'), 'fas', 'fa-undo') : null;
                trElement.querySelector('.refuse') != null ? replaceButton(trElement.querySelector('.refuse'), 'fa-solid', 'fa-xmark') : null;
            })
            .catch(error => {
                backdropLoader.remove();
                addNotification(null, error);
            });
    }
}


























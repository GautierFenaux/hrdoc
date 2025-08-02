const btnTT = document.querySelector('.process > div a');
const accordions = document.querySelectorAll('.accordion');
const profilAccess = document.querySelector('.profil-access');


if (document.querySelector('.alert.alter-success') != null) {
  document.querySelector('.header').style.margin = 0;
}

const cetLinks = document.querySelectorAll('.cet-links');

Array.prototype.slice.call(cetLinks).map(cetLink => {
  cetLink.setAttribute('href', cetLink.getAttribute('href').replace(/%09/g, ""));
});


if (btnTT) {
  if (btnTT.innerHTML == "Signer") {
    btnTT.style.backgroundColor = '#F5DA59';
    btnTT.style.color = '#1f2b51';
    btnTT.style.border = '1px solid #1f2b51'
  }
}

Array.prototype.slice.call(accordions).map(accordion => {
  accordion.addEventListener('click', (e) => {
    console
    const iconElements = document.querySelectorAll('.cet-links-dashboard i');
    let isOutside = true;

    iconElements.forEach(iconElement => {
      if (iconElement.contains(e.target)) {
        isOutside = false;
      }
    });

    if (isOutside) {
      e.preventDefault();
    }

    accordion.classList.toggle('active');

    Array.from(accordions).find(accordionToFound => {
      // Si l'accordéon à trouver n'est pas l'accordéon cliqué et qu'il contient la classe active alors on lui enlève la classe active
      if (accordionToFound.classList.contains('active') && accordion != accordionToFound) {
        accordionToFound.classList.remove('active');
      }
    });
  });
});


export const startIntroManagerFirstConnection = () => {

  window.addEventListener('DOMContentLoaded', () => {
    profilAccess.classList.add('active');
    introJs().setOptions({

      steps: [
        {
          element: document.querySelector('.profil-access ul li'),
          title: 'Bienvenue sur Hrdoc 👋',
          intro: "Afin d\'utiliser la plateforme Hrdoc de manière fluide il est recommandé d\'enregistrer votre signature."
        }, {
          element: document.querySelector('.profil-access ul li'),
          intro: 'Pour les manager, il est ⚠️ INDISPENSABLE ⚠️ d\'enregistrer votre signature. Merci.'
        },
      ],
      nextLabel: 'Suivant',
      prevLabel: 'Précédent',
      doneLabel: 'Fermer',
      exitOnOverlayClick: false,
      hidePrev: true
    }).onbeforechange(function () {

      window.addEventListener('click', (e) => {
        profilAccess.classList.add('active');
      })
    })
      .onexit(function () {
        console.log('on exit');
        window.addEventListener('click', (e) => {
          profilAccess.classList.add('active');
        });

        profilAccess.firstElementChild.firstElementChild.style.backgroundColor = '#acd7c3';
        profilAccess.firstElementChild.firstElementChild.classList.add('shining');

      })
      .start();

  })

};

export const startIntroUserFirstConnection = () => {

  window.addEventListener('DOMContentLoaded', () => {
    profilAccess.classList.add('active');

    introJs().setOptions({

      steps: [
        {
          element: document.querySelector('.profil-access ul li'),
          title: 'Bienvenue sur Hrdoc 👋',
          intro: "Afin d\'utiliser la plateforme Hrdoc de manière fluide il est recommandé d\'enregistrer votre signature."
        },
        // {
        //   element: document.querySelector('.profil-access ul li'),
        //   title: 'Kit d\'intégration',
        //   intro: 'Télécharger votre kit d\'intégration en vous rendant sur votre profil.'
        // },
      ],
      nextLabel: 'Suivant',
      prevLabel: 'Précédent',
      doneLabel: 'Fermer',
      exitOnOverlayClick: false,
      hidePrev: true
    }).onbeforechange(function () {
      window.addEventListener('click', (e) => {
        profilAccess.classList.add('active');
      })
    }).onexit(function () {

      window.addEventListener('click', (e) => {
        profilAccess.classList.add('active');
      });

      profilAccess.firstElementChild.firstElementChild.style.backgroundColor = '#acd7c3';
      profilAccess.firstElementChild.firstElementChild.classList.add('shining');

    }).start();

  })
  fetch(`/update-connection-status`)
    .then(response => {
      if (response.ok) {
        console.log("ajax ok")
      } else {
        console.log(response);
        console.error('Erreur lors de l\'envoi de l\'email');
      }
    })
    .catch(error => {
      console.error('Erreur lors de la requête fetch:', error);
    });

};


export const reminderManagerSignature = () => {

  window.addEventListener('DOMContentLoaded', () => {
    profilAccess.classList.add('active');

    introJs().setOptions({

      steps: [
        {
          element: document.querySelector('.profil-access ul li'),
          title: '⚠️ Rappel ⚠️',
          intro: 'Pour les manager, il est INDISPENSABLE d\'enregistrer votre signature. Merci.'
        },
      ],
      nextLabel: 'Suivant',
      prevLabel: 'Précédent',
      doneLabel: 'Fermer',
      exitOnOverlayClick: false,
      hidePrev: true
    }).onbeforechange(function () {
      window.addEventListener('click', (e) => {
        profilAccess.classList.add('active');
      })
    }).onexit(function () {

      window.addEventListener('click', (e) => {
        profilAccess.classList.add('active');
      });

      profilAccess.firstElementChild.firstElementChild.style.backgroundColor = '#acd7c3';
      profilAccess.firstElementChild.firstElementChild.classList.add('shining');

    }).start();

  })

};


export const reminderDownloadKit = () => {

  window.addEventListener('DOMContentLoaded', () => {
    profilAccess.classList.add('active');

    introJs().setOptions({

      steps: [
        {
          element: document.querySelector('.profil-access ul li'),
          title: 'Kit d\'intégration',
          intro: 'Télécharger votre kit d\'intégration en vous rendant sur votre profil.'
        },
      ],
      nextLabel: 'Suivant',
      prevLabel: 'Précédent',
      doneLabel: 'Fermer',
      exitOnOverlayClick: false,
      hidePrev: true
    }).onbeforechange(function () {
      window.addEventListener('click', (e) => {
        profilAccess.classList.add('active');
      })
    }).onexit(function () {

      window.addEventListener('click', (e) => {
        profilAccess.classList.add('active');
      });

      profilAccess.firstElementChild.firstElementChild.style.backgroundColor = '#acd7c3';
      profilAccess.firstElementChild.firstElementChild.classList.add('shining');

    }).start();

  })

};




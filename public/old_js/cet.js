const alimentationCheckBox = document.querySelector('#cet_alimentation');
const restitutionCheckbox = document.querySelector('#cet_restitution');
const utilisationCheckbox = document.querySelector('#cet_utilisation');
const formSteps = document.querySelectorAll(".form-step");

window.addEventListener('click', (e) => {
    displayFormPart(e.target);

})

window.addEventListener('DOMContentLoaded', () => {

    if (window.location.pathname.includes('/utilisation')) {
        const checkBoxUtilisation = document.getElementById('cet_utilisation');
        checkBoxUtilisation.checked = true
        displayFormPart(checkBoxUtilisation);
    }
    if (window.location.pathname.includes('/restitution')) {
        const checkBoxRestitution = document.getElementById('cet_restitution');
        checkBoxRestitution.checked = true
        displayFormPart(checkBoxRestitution);
    }
    if (window.location.pathname.includes('/alimentation')) {
        const checkBoxAlimentation = document.getElementById('cet_alimentation');
        checkBoxAlimentation.checked = true
        displayFormPart(checkBoxAlimentation);
    }

})

const displayFormPart = (event) => {
    const checkBoxes = document.querySelectorAll('.cet-choice')

    Array.prototype.slice.call(checkBoxes).map(checkBox => {

        if (checkBox == event) {
            const partId = (checkBox.getAttribute('id').substr(4)).toLowerCase();
            document.querySelector(`#${partId}`).classList.toggle('form-step-active');

            Array.prototype.slice.call(formSteps).map(formStep => {
                if (!formStep.getAttribute('id').includes(partId)) {
                    document.querySelector('#' + formStep.getAttribute('id')).classList.remove('form-step-active');
                }
            })
        } else if (Array.prototype.slice.call(checkBoxes).includes(event)) {
            checkBox.checked = false;
        }
    })

}
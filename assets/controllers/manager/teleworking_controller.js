import { Controller } from '@hotwired/stimulus';

let counter = 0;
export default class extends Controller {
    static targets = ['formStep', 'navButton', 'signatureButton', 'form','avisUnused', 'managerComment'];
    connect() {
        this.avisUnusedTarget.parentElement.remove();
        this.navButtonTargets[0].style.display = 'none';
    }
    // TODO : Améliorer code, trop complexe pour ce que ça doit accomplir...
    displayStep(e) {
        e.target.value == 'Suivant' ? counter++ : counter--;
        this.formStepTargets.map((formStep) => {
            formStep.classList.contains('form-step-active') ? formStep.classList.remove("form-step-active") : null;
        })
        this.formStepTargets[counter].classList.add('form-step-active');
        this.navButtonTargets[counter].style.display = 'none';
        if (e.target.value == 'Suivant') {
            this.signatureButtonTarget.style.display = 'block';
            this.navButtonTargets[counter - 1].style.display = 'block';
        } else {
            this.signatureButtonTarget.style.display = 'none';
            this.navButtonTargets[counter + 1].style.display = 'block';
        }
    }
    displayComments(e) {
        if(e.target.value == 1) {
            this.managerCommentTarget.style.opacity = 0;
            this.managerCommentTarget.style.height = 0;
        } else {
            this.managerCommentTarget.style.opacity = 1;
            this.managerCommentTarget.style.height = '55px';
        }
    }
    submitForm(e) {
        e.preventDefault();
        if (!this.formTarget.checkValidity()) {
            alert('Veuillez remplir l\'ensemble des champs.');
        } else {
            this.formTarget.submit()
        }
    }
}

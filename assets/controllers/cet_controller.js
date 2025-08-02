import { Controller } from '@hotwired/stimulus';

let stepToRemove;
export default class extends Controller {
    static targets = ['step', 'alimentation', 'restitution', 'utilisation', 'alimentation_input', 'restitution_input', 'utilisation_input', 'input'];
    connect() {
        // Recupère le fragment d'url et enlève les charactère indésirable autour
        let segment = decodeURIComponent(window.location.pathname.split('/')[4]).replace(/^\t+|\t+$/g, '').trim();
        this.targets.find(segment).classList.add('form-step-active');
        this.targets.find(`${segment}_input`).checked = true;
        stepToRemove = this.targets.find(segment);
    }
    triggerStep(e) {
        const typeOfCet = e.target.getAttribute('id');
        this.inputTargets.map((input) => {
            if (input != e.target) {
                input.checked = false;
            }
        })
        if (typeOfCet != null) {
            if (stepToRemove) {
                stepToRemove.classList.remove('form-step-active');
            }
            const stepToDislay = this.targets.find(typeOfCet.replace('cet_', ''));
            stepToDislay.classList.add('form-step-active');
            stepToRemove = stepToDislay;
        }

    }
}
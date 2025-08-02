import { Controller } from '@hotwired/stimulus';

let counter = 0;
let quotityValue;

export default class extends Controller {
    static targets = ['quotite', 'quotiteUnused', 'teleworkingDay', 'journeesTeletravailleesUnused', 'partialTime', 'form'];
    connect() {
        this.quotiteUnusedTarget.remove();
        this.journeesTeletravailleesUnusedTarget.parentElement.remove();
    }
    addDay(e) {
        const startAlert = (maxDays) => {
            alert('Votre nombre maximal de journée télétravailée est de ' + maxDays);
            e.target.checked = false;
            counter = maxDays;
        }

        if (e.target.checked) {
            counter++
        } else {
            counter--
        }
        if (this.partialTimeTarget.checked && counter > 1) {
            startAlert(1)
        }

        if (counter > 2) {
            startAlert(2)
        }
    }
    resetInputs() {
        this.teleworkingDayTargets.map((teleworkingDay) => {
            teleworkingDay.checked = false;
        })
        counter = 0;
    }
    changeValue(e) {
        const quotity = e.target.value;
        quotityValue = quotity
        if (quotity == "Temps partiel") {
            this.quotiteTarget.classList.add('active');
        } else {
            this.quotiteTarget.classList.remove('active');
            this.partialTimeTarget.checked = false;
        }
    }
    submitForm(e) {
        e.preventDefault();
        if (counter <= 0) {
            alert('Veuillez indiquer vos journées télétravaillées.');
        } else if (!this.formTarget.checkValidity()) {
            alert('Veuillez remplir l\'ensemble des champs.');
        } else {
            this.formTarget.submit()
        }
    }
}

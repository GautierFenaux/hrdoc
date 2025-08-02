import { Controller } from '@hotwired/stimulus';
import Flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.min.css';
import { French } from 'flatpickr/dist/l10n/fr.js';

export default class extends Controller {
    static targets = ['calendar']
    connect() {
        this.calendarTargets.map((input) => {
            Flatpickr(input,
                {
                    disableMobile: true,
                    enableTime: false,
                    dateFormat: "d-m-Y",
                    locale: French,
                }
            )
        })
    }
}

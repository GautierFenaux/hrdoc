import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    connect() {}
    toggleAccordion(e) {
      e.target.parentElement.parentElement.classList.toggle('active');
    }
}

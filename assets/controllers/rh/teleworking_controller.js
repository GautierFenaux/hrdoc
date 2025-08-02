import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['avisUnused', 'rhComment'];
    connect() {
        this.avisUnusedTarget.parentElement.remove();
    }
    displayComments(e) {
        if (e.target.value == 1) {
            this.rhCommentTarget.style.opacity = 0;
            this.rhCommentTarget.style.height = 0;
        } else {
            this.rhCommentTarget.style.opacity = 1;
            this.rhCommentTarget.style.height = '55px';
        }
    }
}

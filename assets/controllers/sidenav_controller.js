

import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    static targets = ['collabMenu', 'managerMenu', 'rhMenu', 'profil']
    connect() {
        const rhPattern = /^https?:\/\/[^\/]+\/.*rh.*/;
        const managerPattern = /manager/;
        const href = window.location.href
        if (rhPattern.test(href)) {
            this.addStyle(this.rhMenuTarget);
        } else if (managerPattern.test(href)) {
            this.addStyle(this.managerMenuTarget);
        } else if (!managerPattern.test(href) && !rhPattern.test(href)) {
            this.collabMenuTarget.classList.add('active');
        }
    }
    // TODO : A améliorer au niveau syntaxique ?
    addStyle(link) {
        link.classList.add('active');
        const slideStyle = link.querySelector('.slide').style;
        slideStyle.maxHeight = '200px';
        slideStyle.animation = 'none';
        slideStyle.backgroundColor = '#ffffff82';
    }
    toggleProfilAccess(e) {
        this.profilTarget.classList.toggle('active');
    }
}

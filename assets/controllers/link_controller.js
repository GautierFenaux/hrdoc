import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static values = {
        id: Number,
        route: String
    }

    editWithComment(event) {
        event.preventDefault();
        const typeOfValidation = event.target.parentElement.getAttribute('data-request-type');
        const promptMessage = typeOfValidation === 'refuse' || typeOfValidation === 'reopen' ? 'Entrez votre commentaire (obligatoire)' : "Si nécessaire, entrez votre commentaire :";
        // Prompt user for a comment
        let comment = window.prompt(promptMessage);
        if (comment === null) {
            return;
        }
        // Build URL with encoded comment
        const url = `${this.idValue}/edit?comment=${encodeURIComponent(comment)}&type_of_validation=${typeOfValidation}`;

        // Redirect using Turbo
        if (comment === '' && (typeOfValidation === 'refuse' || typeOfValidation === 'reopen')) {
            alert('Merci d\'ajouter un commentaire');
            return;
        } else {
            Turbo.visit(url);
        }
    }
}

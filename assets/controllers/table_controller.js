import { Controller } from '@hotwired/stimulus';
import $ from 'jquery';
import DataTable from 'datatables.net';
import 'datatables.net-dt/css/dataTables.dataTables.min.css';
import Toastify from 'toastify-js'
import 'toastify-js/src/toastify.min.css'

// TODO : Créer un controller sur le wrapper du tableau ?
export default class extends Controller {
    connect() {

        if (!this.element.classList.contains('make-idempotent')) {
            this.initDataTable();
        }
        // Permet de faire un traitement sur le HTML retourné par turbo
        if (!window.toastifyInitialized) {
            document.addEventListener("turbo:before-stream-render", (event) => {
                const message = event.detail.newStream.querySelector("template").content.querySelector(`tr`).getAttribute('data-message');
                Toastify({
                    text: message,
                    duration: 3000,
                    newWindow: true,
                    close: true,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
                    backgroundColor: "#1f2b51",
                }).showToast();
            })
            window.toastifyInitialized = true;
        }
    }



    initDataTable() {
        const tableId = this.element.id;
        const tableElement = $(`#${tableId}`);
        this.element.classList.add('make-idempotent');
        if (window.location.pathname === '/dashboard') return;

        if (!$.fn.DataTable.isDataTable(tableElement)) {
            tableElement.DataTable({
                language: {
                    lengthMenu: "Afficher _MENU_ éléments par page",
                    zeroRecords: "Aucun résultat ne correspond à cette recherche",
                    info: "Montre les éléments de la page _PAGE_ sur _PAGES_",
                    infoEmpty: "Aucun élément à afficher",
                    infoFiltered: "(Résultat filtré sur _MAX_ éléments)",
                    search: "Rechercher : "
                }
            });
        }
    }

    comfirm(e) {
        if (!window.confirm('Etes vous sur de vouloir relancer ce collaborateur ?')) {
            e.preventDefault();
        }
    }
}
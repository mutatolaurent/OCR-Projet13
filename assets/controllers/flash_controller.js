// assets/controllers/flash_controller.js
import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    // On définit une variable "duration" typée en nombre, avec 4000ms par défaut
    static values = {
        duration: { type: Number, default: 4000 },
    };

    // La méthode connect() se déclenche AUTOMATIQUEMENT dès que l'élément HTML apparaît sur la page
    connect() {
        // On lance le compte à rebours basé sur la valeur définie dans le HTML
        setTimeout(() => {
            // this.element représente la <div> sur laquelle le contrôleur est attaché
            this.element.classList.add("fade-out");

            // On attend 500ms (le temps de la transition CSS) avant de supprimer l'élément du DOM
            setTimeout(() => {
                this.element.remove();
            }, 500);
        }, this.durationValue);
    }
}

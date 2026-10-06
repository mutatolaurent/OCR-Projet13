// assets/controllers/modal_controller.js
import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    // On définit les "targets" (les éléments HTML qu'on va manipuler)
    static targets = ["dialog", "form", "confirmButton"];

    // Ouvrir la modale
    open() {
        this.dialogTarget.classList.add("show");
    }

    // Fermer la modale
    close() {
        this.dialogTarget.classList.remove("show");
    }

    // Fermer si on clique sur l'arrière-plan sombre
    closeOutside(event) {
        if (event.target === this.dialogTarget) {
            this.close();
        }
    }

    // Confirmer l'action et soumettre le formulaire
    confirm() {
        // Empêcher le double clic et rassurer l'utilisateur
        this.confirmButtonTarget.disabled = true;
        this.confirmButtonTarget.textContent = "Suppression...";

        // Soumission du formulaire lié
        this.formTarget.submit();
    }
}

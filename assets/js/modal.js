// assets/js/modal.js

document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("confirm-modal");
    const btnTrigger = document.getElementById("btn-trigger-modal");
    const btnCancel = document.getElementById("btn-cancel-modal");
    const btnConfirm = document.getElementById("btn-confirm-modal");
    const form = document.getElementById("clear-basket-form");

    // On vérifie que les éléments existent bien sur la page courante
    if (btnTrigger && modal) {
        // Ouvrir la modale
        btnTrigger.addEventListener("click", () => {
            modal.classList.add("show");
        });

        // Fermer la modale (bouton Annuler)
        btnCancel.addEventListener("click", () => {
            modal.classList.remove("show");
        });

        // Fermer la modale si on clique sur l'overlay sombre
        modal.addEventListener("click", (e) => {
            if (e.target === modal) {
                modal.classList.remove("show");
            }
        });

        // Confirmer et soumettre le formulaire
        btnConfirm.addEventListener("click", () => {
            btnConfirm.disabled = true;
            btnConfirm.textContent = "Suppression...";
            form.submit();
        });
    }
});

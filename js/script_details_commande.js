const modal = document.getElementById("editModal");
const closeModal = document.querySelector(".close-btn");
const editButtons = document.querySelectorAll(".edit-button");
const editForm = document.getElementById("editForm");
const spinner = document.getElementById("loadingSpinner");

editButtons.forEach(button => {
    button.addEventListener("click", function() {
        const id = this.getAttribute("data-id");
        const designation = this.getAttribute("data-designation");
        const objet = this.getAttribute("data-objet");
        const piece = this.getAttribute("data-piece");
        const image = this.getAttribute("data-image");
        const nombresArticles = this.getAttribute("data-nombres_articles");
        const urgence = this.getAttribute("data-urgence");
        const typeServices = this.getAttribute("data-type_services");
        const prixUnitaire = this.getAttribute("data-prix_unitaire");

        document.getElementById("editId").value = id;
        document.getElementById("editDesignation").value = designation;
        document.getElementById("editObjet").value = objet;

        // Afficher les fichiers existants
        document.getElementById("currentPiece").innerHTML = piece ? `<img src='../img/${piece}' alt='Current Piece' style='max-width:50px; max-height:50px;'>` : 'Aucune pièce actuelle';
        document.getElementById("currentImage").innerHTML = image ? `<img src='../img/${image}' alt='Current Image' style='max-width:50px; max-height:50px;'>` : 'Aucune image actuelle';


        // Vous ne pouvez pas définir directement des valeurs pour les champs de type file.
        document.getElementById("editNombresArticles").value = nombresArticles;
        document.getElementById("editUrgence").value = urgence;
        document.getElementById("editTypeServices").value = typeServices;
        document.getElementById("editPrixUnitaire").value = prixUnitaire;
        
        modal.style.display = "block";
    });
});

// Fermer la modal
closeModal.addEventListener("click", function() {
    modal.style.display = "none";
});

// Fermer la modal en cliquant en dehors de celle-ci
window.addEventListener("click", function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
});


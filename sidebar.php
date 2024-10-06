<?php
// Assurez-vous que la session est démarrée
if (!isset($_SESSION)) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");  // Redirection si non connecté
    exit();
}
?>

<div class="sidebar position-fixed">
    <img src="../img/LOGO.png" alt="Logo" class="menu-logo">
    <hr>
    <h2 class="h5">Menu principal</h2>
    <hr class="custom-hr">
    <div class="menu-item mb-3">
        <div class="menu-header" onclick="toggleDropdown('personnelDropdown')">
            <span class="arrow">&#9654;</span>
            <span><b>Département Ressources Humaines</b></span>
        </div>
        <div class="collapse" id="personnelDropdown">
            <ul class="list-unstyled">
                <li><a href="../Formulaires/formulaire_personnel.php" class="dropdown-item">Formulaire Personnel</a></li>
                <li><a href="../Formulaires/formulaire_departement.php" class="dropdown-item">Formulaire Département</a></li>
                <li><a href="../Formulaires/formulaire_categorie.php" class="dropdown-item">Formulaire Catégorie</a></li>
                <li><a href="../Formulaires/formulaire_permission.php" class="dropdown-item">Formulaire Permission</a></li>
                <li><a href="../Formulaires/Formulaire_avance.php" class="dropdown-item">Formulaire Avance</a></li>
            </ul>
        </div>
    </div>
    


    
</div>

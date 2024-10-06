<?php
require_once("../serv_projet1.php"); // Inclut le fichier pour la connexion à la base de données

// Vérifie si l'ID de la personne à supprimer est passé en paramètre
if (isset($_GET['id'])) { 
    $id = $_GET['id'];
    $delete = mysqli_query($conn, "delete from personnel where id='$id'"); // Supprime la personne correspondante à l'ID

    if ($delete) {
        header("Location: ../Formulaires/Formulaire_personnel.php?deleted=true"); // Redirige vers 'Formulaire_personnel.php' avec un paramètre pour indiquer que la suppression a été effectuée
        exit(); // Termine le script après la redirection
    } else {
        echo "Erreur lors de la suppression de la personne"; // Affiche un message d'erreur si la suppression échoue
    }
}
?>

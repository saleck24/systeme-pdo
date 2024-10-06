<?php
require_once("../serv_projet1.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Mettre à jour la colonne accord_DAF à 1 (validé) pour l'ID de demande d'avance spécifié
    $query = "UPDATE demande_avance SET accord_DAF = 1 WHERE id = '$id'";
    if (mysqli_query($conn, $query)) {
        echo "Demande validée avec succès.";
    } else {
        echo "Erreur lors de la validation de la demande : " . mysqli_error($conn);
    }
}
?>

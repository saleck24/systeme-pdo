<?php
require_once("../serv_projet1.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $personnel_id = $_POST['personnel_id'];
    $montant = $_POST['montant'];

    // Mise à jour de l'avance dans la base de données
    $query = "UPDATE demande_avance SET personnel_id = '$personnel_id', montant = '$montant' WHERE id = '$id'";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Avance mise à jour avec succès.'); window.location.href = 'formulaire_avance.php';</script>";
    } else {
        echo "<script>alert('Erreur lors de la mise à jour de l\'avance.'); window.location.href = 'formulaire_avance.php';</script>";
    }
}
?>

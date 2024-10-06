<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

include '../serv_projet1.php';

if (isset($_GET['id']) && isset($_GET['commande_id'])) {
    $id = intval($_GET['id']);
    $commande_id = intval($_GET['commande_id']); // Récupérer le commande_id

    // Préparer la requête de suppression
    $sql = "DELETE FROM expression_besoins WHERE id = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Lier les paramètres
        mysqli_stmt_bind_param($stmt, "i", $id);

        // Exécuter la requête
        if (mysqli_stmt_execute($stmt)) {
            // Rediriger vers la page des détails de la commande après la suppression
            header("Location: details_commande.php?commande_id=" . $commande_id);
            exit();
        } else {
            echo "Erreur lors de la suppression : " . mysqli_error($conn);
        }

        // Fermer la déclaration
        mysqli_stmt_close($stmt);
    } else {
        echo "Erreur lors de la préparation de la requête : " . mysqli_error($conn);
    }
} else {
    echo "ID ou commande_id manquant.";
}

// Fermer la connexion
mysqli_close($conn);
?>

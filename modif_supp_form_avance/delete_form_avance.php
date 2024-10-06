<?php
require_once("../serv_projet1.php"); // Inclut le fichier pour la connexion à la base de données
header('Content-Type: application/json'); // Indique que la réponse est au format JSON

// Vérifie si l'ID de la personne à supprimer est passé en paramètre
if (isset($_GET['id'])) { 
    $id = $_GET['id'];
    $delete =  "delete from demande_avance where id ='$id'"; // Supprime l'avance 

    if (mysqli_query($conn,$delete)){
        echo json_encode(['success' => true]); // Retourne un succès sous forme de JSON
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression.']); // Retourne un message d'erreur
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID non spécifié.']); // Si l'ID n'est pas passé
}
?>

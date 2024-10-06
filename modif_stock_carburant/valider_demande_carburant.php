<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] != 51) {
    header("Location: index.php");
    exit();
}

include '../serv_projet1.php';

if (isset($_GET['id'])) {
    $demande_id = $_GET['id'];

    // Récupérer la demande de carburant
    $sql = "SELECT * FROM demande_carburant WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $demande_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $demande = $result->fetch_assoc();

    if ($demande) {
        // Récupérer le carburant correspondant
        $carburant_id = $demande['carburant_id'];
        $quantite_demande = $demande['quantite_demande'];
        
        $sql = "SELECT quantite FROM carburant WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $carburant_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $carburant = $result->fetch_assoc();

        if ($carburant) {
            // Vérifier si la quantité est suffisante
            if ($carburant['quantite'] >= $quantite_demande) {
                // Mettre à jour la quantité de carburant
                $nouvelle_quantite = $carburant['quantite'] - $quantite_demande;
                $sql = "UPDATE carburant SET quantite = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ii', $nouvelle_quantite, $carburant_id);
                $stmt->execute();

                // Mettre à jour le statut de la demande
                $sql = "UPDATE demande_carburant SET status = 'Validée' WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $demande_id);
                $stmt->execute();

                // Redirection après validation
                header("Location: liste_demande_carburant.php");
                exit();
            } else {
                echo "Quantité insuffisante dans le stock.";
            }
        } else {
            echo "Carburant non trouvé.";
        }
    } else {
        echo "Demande non trouvée.";
    }
} else {
    echo "ID de la demande non spécifié.";
}

$stmt->close();
$conn->close();
?>

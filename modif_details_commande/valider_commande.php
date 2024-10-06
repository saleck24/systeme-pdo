<?php
session_start();
include '../serv_projet1.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['role'])) {
    
    $commande_id =  intval($_POST['commande_id']);//Sécurisation de l'entrée
    $role = $_POST['role'];

    $sql = "";
    $redirect_url = "liste_besoins.php";

    // Mettre à jour la validation en fonction du rôle
    if ($role == 'Adjoint_MG' && $_SESSION['role'] == 2) {
        $sql = "UPDATE commandes SET adjoint_mg_valide = 1 WHERE id = ?";
    } elseif ($role == 'Dept_Admin' && $_SESSION['role'] == 21) {
        $sql = "UPDATE commandes SET admin_valide = 1 WHERE id = ?";
    } elseif ($role == 'daf' && $_SESSION['role'] == 4) {
        $sql = "UPDATE commandes SET daf_valide = 1 WHERE id = ?";
    } else {
        // Si l'utilisateur n'a pas le bon rôle, on sort
        header("Location: $redirect_url");
        exit();
    }

     // Préparer et exécuter la requête
     if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $commande_id);
        if (!$stmt->execute()) {
            // Gestion de l'erreur d'exécution
            echo "Erreur lors de la mise à jour : " . $stmt->error;
            exit();
        }
    } else {
        // Gestion de l'erreur de préparation
        echo "Erreur de préparation de la requête : " . $conn->error;
        exit();
    }

    // Vérifier si toutes les validations sont complétées pour changer le statut de la commande à 'validée'
    $sql_check = "SELECT adjoint_mg_valide, admin_valide, daf_valide FROM commandes WHERE id = ?";
    if ($stmt_check = $conn->prepare($sql_check)) {
        $stmt_check->bind_param("i", $commande_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $row = $result->fetch_assoc();

        if ($row['adjoint_mg_valide'] && $row['admin_valide'] && $row['daf_valide']) {
            $sql_update_status = "UPDATE commandes SET status = 'validée' WHERE id = ?";
            if ($stmt_update = $conn->prepare($sql_update_status)) {
                $stmt_update->bind_param("i", $commande_id);
                $stmt_update->execute();
            } else {
                echo "Erreur de mise à jour du statut : " . $conn->error;
                exit();
            }
        }
    } else {
        echo "Erreur de vérification des validations : " . $conn->error;
        exit();
    }

    // Rediriger vers la page appropriée
    header("Location: $redirect_url");
    exit();
}
?>

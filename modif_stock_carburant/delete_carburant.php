<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

include '../serv_projet1.php';

$id = $_GET['id'];
$sql = "DELETE FROM carburant WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    $_SESSION['delete_success'] = true; // Stocke le succès de la suppression
} else {
    $_SESSION['delete_success'] = false; // Stocke l'échec de la suppression
}

$stmt->close();
header("Location: ../Formulaires/stock_carburant.php"); // Redirige vers la page principale
exit();

/*if ($stmt->execute()) {
    header("Location: ../Formulaires/stock_carburant.php");
    exit();
} else {
    echo "Erreur: " . $stmt->error;
}

$stmt->close();
header("Location: stock_carburant.php");
exit();
?>*/
?>



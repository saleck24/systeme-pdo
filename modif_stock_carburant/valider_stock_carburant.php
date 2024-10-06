<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 51) {
    header("Location: unauthorized.php");
    exit();
}

include '../serv_projet1.php';

$id = $_GET['id'];

$sql = "UPDATE carburant SET validated = 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: ../Formulaires/stock_carburant.php");
} else {
    echo "Erreur lors de la validation : " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

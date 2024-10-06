<?php
include 'serv_projet1.php';

$id = $_GET['id'];

$sql = "SELECT * FROM expression_besoins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data);
?>

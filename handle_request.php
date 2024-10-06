<?php
session_start();
require_once("serv_projet1.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];
    $status = ($action == 'accorder') ? 'Approved' : 'Denied';

    $sql = "UPDATE permission_requests SET status = ?, notified = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $request_id);
    $stmt->execute();

    // Message pour confirmer l'action
    echo "Action effectuée avec succès.";

    // Redirection vers la page des notifications après un court délai
    header('refresh:2;url=notification.php');
    exit();
}
?>

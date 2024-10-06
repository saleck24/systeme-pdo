<?php
session_start();
require_once("../serv_projet1.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']);
    exit();
}

// Récupérer l'ID utilisateur et les mots de passe
$user_id = $_SESSION['user_id'];
$currentPassword = $_POST['currentPassword'];
$newPassword = $_POST['newPassword'];

// Vérifier le mot de passe actuel dans la base de données
$sql = "SELECT password FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (password_verify($currentPassword, $user['password'])) {
    // Mettre à jour le nouveau mot de passe
    $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);
    $update_sql = "UPDATE users SET password = ? WHERE id = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("si", $newPasswordHash, $user_id);

    if ($stmt_update->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour du mot de passe.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Mot de passe actuel incorrect.']);
}
?>

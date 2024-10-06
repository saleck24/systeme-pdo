<?php
session_start();
require_once("serv_projet1.php");

//$supervisor_id = $_SESSION['user_id'];
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Marquer les demandes comme notifiées
$sql = "UPDATE permission_requests SET notified = 1 WHERE supervisor_id = ? AND status = 'Pending' AND notified = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $supervisor_id);
$stmt->execute();
 
// Récupérer les demandes pour l'utilisateur connecté (en attente et approuvées)
$sql = "SELECT pr.*, u.email 
        FROM permission_requests pr
        JOIN users u ON pr.user_id = u.id
        WHERE (pr.user_id = ? OR pr.supervisor_id = ?) AND (pr.status = 'Pending' OR pr.status = 'Approved')";
        /*WHERE pr.supervisor_id = ? AND pr.status = 'Pending'";*/
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$requests = $result->fetch_all(MYSQLI_ASSOC);   
?>

<h1>Les notifications</h1>
<table>
    <tr>
        <th>Demandeur</th>
        <th>Date de demande</th>
        <th>Status</th>
        <th>Date de depart</th>
        <th>Date de retour</th>
        <th>Nombre de jours</th>
        <th>Action</th>
    </tr>
    <?php foreach ($requests as $request): ?>
    <tr>
        <td><?php echo $request['email']; ?></td>
        <td><?php echo $request['request_date']; ?></td>
        <td><?php echo $request['status']; ?></td>
        <td><?php echo $request['date_depart']; ?></td>
        <td><?php echo $request['date_retour']; ?></td>
        <td><?php echo $request['nb_jours']; ?></td>
        <td>
            <form action="handle_request.php" method="POST" style="display:inline;">
                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                <input type="hidden" name="action" value="accorder">
                <button type="submit" class="btn-accorder">Accorder</button>
            </form>
            <form action="handle_request.php" method="POST" style="display:inline;">
                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                <input type="hidden" name="action" value="desaccorder">
                <button type="submit" class="btn-desaccorder" >Désaccorder</button>
            </form>
            
            <?php if ($request['status'] == 'Approved'): ?>
                <!-- Bouton pour ouvrir la modal -->
                <button type="button" class="btn-print" onclick="openModal(<?php echo $request['id']; ?>)">Imprimer</button>
            <?php endif; ?>

        </td>
    </tr>
    <?php endforeach; ?>
</table>
<br><br>
<!-- Vérifier le rôle de l'utilisateur pour le bouton "Return Home" -->
<!--<php if (isset($_SESSION['role'])): ?>
        <php if ($_SESSION['role'] == 0): ?>
            <a href="users_dashboard/admin_dashboard.php" class="btn-blue">Return Home</a>
        <php elseif ($_SESSION['role'] == 1): ?>
            <a href="users_dashboard/user_dashboard.php" class="btn-blue">Return Home</a>
        <php elseif ($_SESSION['role'] == -1): ?>
            <a href="users_dashboard/assistant_dashboard.php" class="btn-blue">Return Home</a>
        <php endif; ?>
<php endif; ?>-->


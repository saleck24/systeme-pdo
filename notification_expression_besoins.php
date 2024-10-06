<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}
if ($_SESSION['role'] != 2) { // Vérifie que l'utilisateur a le rôle de site
    header("Location: unauthorized.php");
    exit();
}

require_once("serv_projet1.php");
$role = $_SESSION['role'];

// Requête pour récupérer toutes les commandes fermées
$sql = "SELECT c.id AS commande_id, MIN(eb.date_saisie) AS date_saisie 
        FROM expression_besoins eb 
        JOIN commandes c ON eb.commande_id = c.id 
        WHERE eb.notified = 0 
        GROUP BY c.id 
        ORDER BY c.id ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// Traitement de la demande pour marquer une commande entière comme lue
if (isset($_GET['mark_as_read'])) {
    $commande_id = intval($_GET['mark_as_read']);
    $sql_update_eb = "UPDATE expression_besoins SET notified = 1 WHERE commande_id = ?";
    $stmt_update_eb = $conn->prepare($sql_update_eb);
    $stmt_update_eb->bind_param("i", $commande_id);
    $stmt_update_eb->execute();

    // Mettre à jour la colonne notified dans commandes
    $sql_update_commandes = "UPDATE commandes SET notified = 1 WHERE id = ?";
    $stmt_update_commandes = $conn->prepare($sql_update_commandes);
    $stmt_update_commandes->bind_param("i", $commande_id);
    $stmt_update_commandes->execute();

    header("Location: notification_expression_besoins.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications Expression de Besoins</title>
    <link rel="stylesheet" href="css/styles_userdashboard.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: left;
        }
        table th, table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: blue;
            color: white;
        }
        table tr:hover {
            background-color: #f5f5f5;
        }
        .mark-as-read {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
        }
        .mark-as-read:hover {
            background-color: #45a049;
        }
        .btn-blue {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-blue:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="title">Kenz Mining</div>
            <div class="header-icons">
                <div class="logout">
                    <a href="logout.php">
                        <img src="img/icon_logout.png" alt="Logout">
                    </a>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <div class="notification-container">
            <h1>Notifications Expression de Besoins</h1>
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Commande ID</th>
                            <th>Date de Saisie</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['commande_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['date_saisie']); ?></td>
                            <td>
                                <a href="?mark_as_read=<?php echo htmlspecialchars($row['commande_id']); ?>" class="mark-as-read">Marquer comme lu</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucune notification en attente.</p>
            <?php endif; ?>
        </div>
    </main>
    <br><br>
    <?php if ($role == 2): ?>
        <a href="users_dashboard/user_Adjoint_MG.php" class="btn-blue">Return</a>
    <?php elseif ($role == 21): ?>
        <a href="users_dashboard/user_dept_administratif_dashboard.php" class="btn-blue">Return</a>
    <?php endif; ?>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
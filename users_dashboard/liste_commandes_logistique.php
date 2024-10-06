<?php

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 31) {
    header("Location: ../index.php");
    exit();
}

require_once("../serv_projet1.php");

// Récupérer les commandes en attente de validation logistique
$sql = "SELECT * FROM commandes WHERE notified = 1 AND logistique_valide = 0";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commandes à Valider</title>
    <link rel="stylesheet" href="../css/styles_userdashboard.css">
    <style>
        .notification {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }
        .notification-title {
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="title">Kenz Mining</div>
            <div class="header-icons">
                <div class="logout">
                    <a href="../logout.php">
                        <img src="../img/icon_logout.png" alt="Logout">
                    </a>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <h2>Commandes à Valider</h2>

        <!-- Notification Section -->
        <?php if ($result->num_rows > 0): ?>
            <div class="notification">
                <p class="notification-title">Vous avez une nouvelle commande à valider :</p>
                <p>Veuillez valider la commande suivante :</p>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div>
                        <strong>ID Commande:</strong> <?php echo $row['id']; ?><br>
                        <strong>Description:</strong> <?php echo $row['description']; ?><br>
                        <a href="valider_commande.php?commande_id=<?php echo $row['id']; ?>" class="btn btn-blue">Valider</a>
                    </div>
                    <hr>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>Aucune commande en attente de validation.</p>
        <?php endif; ?>
        
        <table border="1">
            <tr>
                <th>ID Commande</th>
                <th>Date</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['date_commande']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td>
                        <a href="../modif_details_commande/valider_commande.php?commande_id=<?php echo $row['id']; ?>" class="btn btn-blue">Valider</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </main>
</body>
</html>

<?php
$conn->close();
?>
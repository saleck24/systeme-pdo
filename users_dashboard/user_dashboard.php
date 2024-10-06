<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}
if ($_SESSION['role'] != 1) {
    header("Location: unauthorized.php");
    exit();
}

require_once("../serv_projet1.php");

// Compter le nombre de demandes en attente de notification
$sql = "SELECT COUNT(*) AS notification_count FROM permission_requests WHERE supervisor_id = ? AND status = 'Pending' AND notified = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);   
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$notification_count = $row['notification_count'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../css/styles_userdashboard.css">
</head>
<body>
    <header>
        <nav>
            <div class="title">Kenz Mining</div>
            <div class="header-icons">
                <div class="notifications">
                    <a href="../notification.php" class="notification-icon">
                        <img src="../img/icon_notification.png" alt="Notifications">
                        <?php if ($notification_count > 0): ?>
                            <span class="notification-count"><?php echo $notification_count; ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="logout">
                    <a href="../logout.php">
                    <img src="../img/icon_logout.png" alt="Logout">
                    </a>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <div class="form-container">
            <div class="form-item">
                <a href="../Formulaires/Formulaires_RH.php">
                    <img src="../img/GRH.png" alt="RH">
                    <div>Département Ressources Humaines</div>
                </a>
            </div>
            
            <div class="form-item">
                <a href="../Formulaires/Formulaires_MG.php">
                    <img src="../img/pngwing.png" alt="Moyens Géneraux">
                    <div>Département Moyen Géneraux</div>
                </a>
            </div>
                
            <div class="form-item">
                <a href="#">
                    <img src="../img/finance.png" alt="Finance">
                    <div>Département Finance</div>
                </a>
            </div>
        </div>
    </main>
</body>
</html>

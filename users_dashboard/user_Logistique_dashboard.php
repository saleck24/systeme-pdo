<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}
if ($_SESSION['role'] != 31) { // Vérifie que l'utilisateur a le rôle de site
    header("Location: ../unauthorized.php");
    exit();
}

require_once("../serv_projet1.php");

// Requête pour des notifications spécifiques à user_logistique
$sql = "SELECT COUNT(*) AS notification_count FROM commandes WHERE notified = 1 AND logistique_valide = 0";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$notification_count = $row['notification_count'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Code pour insérer une nouvelle demande d'expression de besoins
    $date_saisie = date('Y-m-d H:i:s');
    $item = $_POST['item'];
    $objet = $_POST['objet'];
    $piece = $_POST['piece'];
    $image = $_POST['image'];
    $nombres_articles = $_POST['nombres_articles'];
    $urgence = $_POST['urgence'];
    $type_services = $_POST['type_services']; // Utilisation du type de service depuis le formulaire
    $commande_id = null;
    
    // Insérer une nouvelle demande d'expression de besoins
    $sql = "INSERT INTO expression_besoins (date_saisie, item, objet, piece, image, nombres_articles, urgence, type_services, commande_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssisii", $date_saisie, $item, $objet, $piece, $image, $nombres_articles, $urgence, $type_services, $commande_id);
    $stmt->execute();
    
    header("Location: user_Logistique_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Logistique Dashboard</title>
    <link rel="stylesheet" href="../css/styles_userdashboard.css">
    <style>
        .notification img {
            width: 24px; /* Ajustez la largeur de l'icône */
            height: 24px; /* Ajustez la hauteur de l'icône */
            filter: invert(1); /* Rend l'icône blanche */
        }
        .notification-badge {
            background-color: red; /* Couleur de fond du badge */
            color: white; /* Couleur du texte du badge */
            border-radius: 50%; /* Arrondir le badge */
            padding: 2px 6px; /* Ajuster la taille du badge */
            position: absolute;
            top: -8px;
            right: -8px;
            font-size: 12px; /* Taille du texte du badge */
        }
        .header-icons {
            position: relative;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="title">Kenz Mining</div>
            <div class="header-icons">
                <div class="notification">
                    <a href="liste_commandes_logistique.php">
                        <img src="../img/icon_notification.png" alt="Notifications">
                        <?php if ($notification_count > 0): ?>
                            <span class="notification-badge"><?php echo $notification_count; ?></span>
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
                <a href="../Formulaires/Formulaires_MG.php">
                    <img src="../img/pngwing.png" alt="Moyens Géneraux">
                    <div>Département Moyen Géneraux</div>
                </a>
            </div>
        </div>
    </main>
</body>
</html>

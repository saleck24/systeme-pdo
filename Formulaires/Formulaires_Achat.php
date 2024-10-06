<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}
if ($_SESSION['role'] != 2) { // Vérifie que l'utilisateur a un rôle d'achat
    header("Location: ../unauthorized.php");
    exit();
}

require_once("../serv_projet1.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Code pour insérer une nouvelle demande d'achat
    $date_saisie = date('Y-m-d H:i:s');
    $item = $_POST['item'];
    $description = $_POST['description'];
    $quantite = $_POST['quantite'];
    $prix_unitaire = $_POST['prix_unitaire'];
    $fournisseur = $_POST['fournisseur'];
    $urgence = $_POST['urgence'];
    $status = 'Pending';
    $notified = 0;

    $sql = "INSERT INTO commandes (date_saisie, item, description, quantite, prix_unitaire, fournisseur, urgence, status, notified) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssidsssi", $date_saisie, $item, $description, $quantite, $prix_unitaire, $fournisseur, $urgence, $status, $notified);
    $stmt->execute();

    header("Location: ../dashboards/user_achat_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'Achat</title>
    <link rel="stylesheet" href="../css/styles_formulaire_Achat.css">
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
        <div class="form-container">
            <form method="post" action="">
                <div class="form-item">
                    <label for="item">Item</label>
                    <input type="text" id="item" name="item" required>
                </div>
                <div class="form-item">
                    <label for="description">Description</label>
                    <input type="text" id="description" name="description" required>
                </div>
                <div class="form-item">
                    <label for="quantite">Quantité</label>
                    <input type="number" id="quantite" name="quantite" required>
                </div>
                <div class="form-item">
                    <label for="prix_unitaire">Prix Unitaire</label>
                    <input type="number" id="prix_unitaire" name="prix_unitaire" step="0.01" required>
                </div>
                <div class="form-item">
                    <label for="fournisseur">Fournisseur</label>
                    <input type="text" id="fournisseur" name="fournisseur" required>
                </div>
                <div class="form-item">
                    <label for="urgence">Urgence</label>
                    <select id="urgence" name="urgence" required>
                        <option value="Haute">Haute</option>
                        <option value="Moyenne">Moyenne</option>
                        <option value="Basse">Basse</option>
                    </select>
                </div>
                <div class="form-item">
                    <button type="submit">Soumettre</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

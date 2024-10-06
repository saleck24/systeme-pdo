<?php
session_start();
require_once("../serv_projet1.php");

$id = $raison_social = $adresse = $telephone = $email = "";
$logo = null;
$message = "";

// Traitement du formulaire lors de la soumission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $raison_social = mysqli_real_escape_string($conn, $_POST["raison_social"]);
    $adresse = mysqli_real_escape_string($conn, $_POST["adresse"]);
    $telephone = mysqli_real_escape_string($conn, $_POST["telephone"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);

    // Traitement de l'upload du logo
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $logo = $_FILES['logo']['name'];
        $tlogo = $_FILES['logo']['tmp_name'];// Récupère le chemin temporaire du logo
        move_uploaded_file($tlogo,".././img/$logo");// Déplace le logo vers le dossier 'img'
    }

    // Insertion ou mise à jour dans la base de données
    if (empty($id)) {
        $stmt = $conn->prepare("INSERT INTO parametre_appli (raison_social, logo, adresse, telephone, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $raison_social, $logo, $adresse, $telephone, $email);
    } else {
        if ($logo) {
            $stmt = $conn->prepare("UPDATE parametre_appli SET raison_social=?, logo=?, adresse=?, telephone=?, email=? WHERE id=?");
            $stmt->bind_param("sssssi", $raison_social, $logo, $adresse, $telephone, $email, $id);
        } else {
            $stmt = $conn->prepare("UPDATE parametre_appli SET raison_social=?, adresse=?, telephone=?, email=? WHERE id=?");
            $stmt->bind_param("ssssi", $raison_social, $adresse, $telephone, $email, $id);
        }
    }

    if ($stmt->execute()) {
        $message = "Enregistrement réussi";
    } else {
        $message = "Erreur: " . $stmt->error;
    }

    $stmt->close();
}

// Préparation des données pour l'affichage du formulaire
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM parametre_appli WHERE id = $id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $raison_social = $row['raison_social'];
        $adresse = $row['adresse'];
        $telephone = $row['telephone'];
        $email = $row['email'];
        $logo = $row['logo'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Paramètre Appli</title>
    <link rel="stylesheet" href="../css/styles_formulaire_parametre-appli.css">
</head>
<body>
    <?php if (!empty($message)): ?>
        <div class="message <?php echo strpos($message, 'Erreur') === false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data" action="formulaire_parametre-appli.php">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
        <div class="form-group">
            <label for="raison_social">Raison Sociale:</label>
            <input type="text" id="raison_social" name="raison_social" value="<?php echo htmlspecialchars($raison_social); ?>" required>
        </div>
        <div class="form-group">
            <label for="logo">Logo:</label>
            <input type="file" id="logo" name="logo">
            <?php if ($logo): ?>
                <img src="uploads/<?php echo htmlspecialchars($logo); ?>" alt="Logo actuel" class="logo-preview">
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="adresse">Adresse:</label>
            <textarea id="adresse" name="adresse" required><?php echo htmlspecialchars($adresse); ?></textarea>
        </div>
        <div class="form-group">
            <label for="telephone">Téléphone:</label>
            <input type="text" id="telephone" name="telephone" value="<?php echo htmlspecialchars($telephone); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="form-group">
            <button type="submit">Soumettre</button>
        </div>
    </form>
    <br><br>
    <a href="../tableau_parametre_appli.php" class="btn-blue">Tableau paramètre de l'application</a>
    <a href="../users_dashboard/admin_dashboard.php" class="btn-blue">Retour Home</a>
</body>
</html>

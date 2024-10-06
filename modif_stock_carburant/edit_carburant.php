<?php
// edit_carburant.php

session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

include '../serv_projet1.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $categorie = $_POST['categorie'];
    $libelle = $_POST['libelle'];
    $prix = $_POST['prix'];
    $beneficiaire = $_POST['beneficiaire'];
    $quantite = $_POST['quantite'];
    $n_stock = $_POST['n_stock'];
    
    // Gestion de la pièce justificative
    if (isset($_FILES['piece']) && $_FILES['piece']['error'] == UPLOAD_ERR_OK) {
        $piece = $_FILES['piece']['name'];
        $tpiece = $_FILES['piece']['tmp_name'];
        $chemin_piece = "../img/" . $piece;
        move_uploaded_file($tpiece, $chemin_piece);
    } else {
        $piece = $_POST['existing_piece'];
    }

    $sql = "UPDATE carburant SET categorie = ?, libelle = ?, piece = ?, prix = ?, beneficiaire = ?, quantite = ?, n_stock = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssdsssi', $categorie, $libelle, $piece, $prix, $beneficiaire, $quantite, $n_stock, $id);

    /*if ($stmt->execute()) {
        echo "Données mises à jour avec succès.";
        header("Location: ../Formulaires/stock_carburant.php");
        exit();
    } else {
        echo "Erreur: " . $stmt->error;
    }*/
    if ($stmt->execute()) {
        $_SESSION['update_success'] = true; // Stocke la réussite de l'opération dans une variable de session
    } else {
        $_SESSION['update_success'] = false; // Stocke l'échec
    }

    $stmt->close();
    header("Location: ../Formulaires/stock_carburant.php"); // Redirige vers la page principale
    exit();
} else {
    $id = $_GET['id'];
    $sql = "SELECT * FROM carburant WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $carburant = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Carburant</title>
    <link rel="stylesheet" type="text/css" href="../css/styles_stock_carburant.css">
</head>
<body>
    <div class = 'tête'>
        <b><u>Modifier Carburant</u> :</b>
    </div>
    <form id="edit-carburant-form"  action="../modif_stock_carburant/edit_carburant.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $carburant['id']; ?>">

        <label for="categorie">Catégorie :</label>
        <input type="text" id="categorie" name="categorie" value="<?php echo htmlspecialchars($carburant['categorie']); ?>" required><br>

        <label for="libelle">Libellé :</label>
        <input type="text" id="libelle" name="libelle" value="<?php echo htmlspecialchars($carburant['libelle']); ?>" required><br>

        <label for="piece">Pièce justificative :</label>
        <input type="file" id="piece" name="piece">
        <input type="hidden" name="existing_piece" value="<?php echo htmlspecialchars($carburant['piece']); ?>">
        <?php if ($carburant['piece']) : ?>
            <p><b>Fichier actuel :</b> <a href="../img/<?php echo htmlspecialchars($carburant['piece']); ?>" download><?php echo htmlspecialchars($carburant['piece']); ?></a></p>
        <?php else : ?>
            <p>Aucun fichier téléchargé</p>
        <?php endif; ?><br>

        <label for="prix">Prix :</label>
        <input type="number" id="prix" name="prix" value="<?php echo htmlspecialchars($carburant['prix']); ?>" step="0.01" required><br>

        <label for="beneficiaire">Bénéficiaire :</label>
        <input type="text" id="beneficiaire" name="beneficiaire" value="<?php echo htmlspecialchars($carburant['beneficiaire']); ?>" required><br>

        <label for="quantite">Quantité :</label>
        <input type="number" id="quantite" name="quantite" value="<?php echo htmlspecialchars($carburant['quantite']); ?>" required><br>

        <label for="n_stock">Numéro de Stock :</label>
        <input type="text" id="n_stock" name="n_stock" value="<?php echo htmlspecialchars($carburant['n_stock']); ?>" required><br>
        <div class ='btn-update'>
            <input type="submit" value="Mettre à jour">
        </div>
    </form>
</body>
</html>

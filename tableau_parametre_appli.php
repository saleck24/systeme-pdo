<?php
session_start();
require_once("serv_projet1.php");

// Récupération des données de la table
$result = $conn->query("SELECT * FROM parametre_appli");

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau des Paramètres de l'Application</title>
    <link rel="stylesheet" href="css/styles_tableau_parametre_appli.css">
</head>
<body>
    <h1>Tableau des Paramètres de l'Application</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Raison Sociale</th>
                <th>Logo</th>
                <th>Adresse</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['raison_social']; ?></td>
                        <td>
                            <?php if ($row['logo']): ?>
                                <img src="./img/<?php echo $row['logo']; ?>" alt="Logo" width="50">
                            <?php else: ?>
                                Pas de logo
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['adresse']; ?></td>
                        <td><?php echo $row['telephone']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td>
                            <a href="Formulaires/formulaire_parametre-appli.php?id=<?php echo $row['id']; ?>" class="btn-edit">Modifier</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">Aucun enregistrement trouvé</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <br>
        <!--<a href="Formulaires/formulaire_parametre-appli.php" class="btn-new">Ajouter Nouveau</a>-->
    <br><br>
    <a href="users_dashboard/admin_dashboard.php" class="btn-blue">Return</a>
</body>
</html>

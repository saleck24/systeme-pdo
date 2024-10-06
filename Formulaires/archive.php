<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}
if ($_SESSION['role'] != 0) { 
    header("Location: unauthorized.php");
    exit();
}

require_once("../serv_projet1.php");

// Récupérer les commandes archivées
$sql = "SELECT commande_id, date_saisie, adjoint_mg_valide, admin_valide, daf_valide, date_archive 
        FROM archives 
        ORDER BY date_archive DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archives des Commandes</title>
    <link rel="stylesheet" href="../css/styles_archive.css">

    <header>
        <nav>
            <!-- Titre -->
            <div class="logo-container">
                <img src="../img/LOGO.png" alt="Logo de Kenz Mining SA" class="logo-image">
            </div>
            <!--lien de déconnexion-->
            <a href="../logout.php" class="logout-link">
                <div class="logout"> 
                    <img src="../img/icon_logout.png" alt="icon_logout" class="logout">
                    <p>Logout</p>
                </div>
            </a>

        </nav>
    </header>
</head>
<body>
    <main>
        <h1><b><u>Archives des Commandes</u> :</b></h1>
        <table border="1">
            <tr>
                <th>N° Commande</th>
                <th>Date de Saisie</th>
                <th>Date d'Archivage</th>
                <th>Validation de l'Adjoint des Moyens Géneraux</th>
                <th>Validation du Directeur Administratif</th>
                <th>Validation du DAF</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['commande_id'] . "</td>";
                    echo "<td>" . $row['date_saisie'] . "</td>";
                    echo "<td>" . $row['date_archive'] . "</td>";
                    echo "<td>" . ($row['adjoint_mg_valide'] ? 'Oui' : 'Non') . "</td>";
                    echo "<td>" . ($row['admin_valide'] ? 'Oui' : 'Non') . "</td>";
                    echo "<td>" . ($row['daf_valide'] ? 'Oui' : 'Non') . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Aucune commande archivée trouvée.</td></tr>";
            }
            ?>
        </table>
        <br><br>
        <a href="../users_dashboard/admin_dashboard.php" class="btn-blue">Retour</a>
    </main>
    <br><br>
    <div class="copyright">© Copyright Saleck BAYA 2024</div>
</body>
</html>

<?php
$conn->close();
?>

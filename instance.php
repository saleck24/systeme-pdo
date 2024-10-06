<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

include 'serv_projet1.php';
// Pagination
$limit = 5; // Nombre de résultats par page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Récupérer le numéro de page
$offset = ($page - 1) * $limit; // Calculer l'offset

// Recherche
$search = isset($_POST['search']) ? mysqli_real_escape_string($conn, $_POST['search']) : '';

// Requête pour compter le nombre total de résultats
$total_sql = "SELECT COUNT(*) FROM instances WHERE commande_id LIKE '%$search%'";
$total_result = mysqli_query($conn, $total_sql);
$total_rows = mysqli_fetch_array($total_result)[0];
$total_pages = ceil($total_rows / $limit); // Calculer le nombre total de pages

// Requête principale avec pagination et recherche
$sql = "SELECT * FROM instances WHERE commande_id LIKE '%$search%' ORDER BY date_instance DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);

/*$sql = "SELECT * FROM instances ORDER BY date_instance DESC";
$result = mysqli_query($conn, $sql);*/
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les Instances</title>
    <link rel="stylesheet" href="css/styles_instances.css">

    <header>
        <nav>
            <!-- Titre -->
            <div class="logo-container">
                <img src="img/LOGO.png" alt="Logo de Kenz Mining SA" class="logo-image">
            </div>
            <!--lien de déconnexion-->
            <a href="logout.php" class="logout-link">
                <div class="logout"> 
                    <img src="img/icon_logout.png" alt="icon_logout" class="logout">
                    <p>Logout</p>
                </div>
            </a>

        </nav>
    </header>
</head>
<body>
    <h1><b><u>Les Commandes en Instances</u> :</b></h1>

    <!-- Barre de recherche -->
    <div class="search-container">
        <form method="POST" action="">
            <div style="text-align: right;">
                <input type="text" name="search" placeholder="N° Commande" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Rechercher</button>
            </div>
        
        </form>
    </div>
    <br><br>
    <div class="table-container">
        <table border="1">
            <tr>
                <th>N Commande</th>
                <th>Date de Saisie</th>
                <th>Date Instance</th>
                <th>Validation de l'Adjoint des Moyens Géneraux</th>
                <th>Validation du Directeur Administratif</th>
                <th>Validation du DAF</th>
            </tr>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['commande_id'] . "</td>";
                    echo "<td>" . $row['date_saisie'] . "</td>";
                    echo "<td>" . $row['date_instance'] . "</td>";
                    echo "<td class='" . ($row['adjoint_mg_valide'] ? "validé" : "en-attente") . "'>" . ($row['adjoint_mg_valide'] ? "Validé" : "En attente") . "</td>";
                    echo "<td class='" . ($row['admin_valide'] ? "validé" : "en-attente") . "'>" . ($row['admin_valide'] ? "Validé" : "En attente") . "</td>";
                    echo "<td class='" . ($row['daf_valide'] ? "validé" : "en-attente") . "'>" . ($row['daf_valide'] ? "Validé" : "En attente") . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Aucune commande en instance</td></tr>";
            }
            ?>  
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . htmlspecialchars($search) : ''; ?>">Précédent</a>
        <?php endif; ?>

        <span>Page <?php echo $page; ?> sur <?php echo $total_pages; ?></span>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . htmlspecialchars($search) : ''; ?>">Suivant</a>
        <?php endif; ?>
    </div>
    <br><br>

    <!-- Bouton "Return" -->
    <div class="btn-container">
        <a href="users_dashboard/admin_dashboard.php" class="btn-blue">Retour</a>
    </div>
    <br><br>
    <div class="copyright">© Copyright Saleck BAYA 2024</div>
</body>
</html>

<?php
mysqli_close($conn);
?>

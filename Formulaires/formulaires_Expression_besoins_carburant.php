<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

include '../serv_projet1.php';

// Traitement du formulaire
$message = '';

$search = "";
if (isset($_POST['search'])) {
    $search = htmlspecialchars($_POST['search']); // Échapper les données pour éviter les injections XSS
}

// Vérification que la méthode est bien POST et que les champs nécessaires existent
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['carburant_id']) && isset($_POST['quantite_demande'])) {
    //if (isset($_POST['carburant_id']) && isset($_POST['quantite_demande'])) {
    $carburant_id = $_POST['carburant_id'];
    $quantite_demande = $_POST['quantite_demande'];
    $demandeur = htmlspecialchars($_SESSION['email']);

    // Insertion de la demande dans la table `demande_carburant`
    $sql = "INSERT INTO demande_carburant (carburant_id, quantite_demande, demandeur) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $message = "Erreur de préparation de la requête : " . $conn->error;
    } else {
        $stmt->bind_param('iis', $carburant_id, $quantite_demande, $demandeur);
        if ($stmt->execute()) {
            $message = "Demande soumise avec succès.";
        } else {
            $message = "Erreur: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formulaire Demande Carburant</title>
    <link rel="stylesheet" type="text/css" href="../css/styles_formulaires_expresion_besoins_carburant.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
<body><br><br>
    <fieldset>
        <legend><u><b>Faire une demande de carburant</b></u> :</legend>
            <!-- Barre de recherche -->
            <form method="POST" action="">
                <div style="text-align: right;">
                    <input type="text" name="search" placeholder="Rechercher par ID" value="<?php echo htmlspecialchars($search ?? ''); ?>">
                    <button type="submit">Rechercher</button>
                </div>
            </form>
            <br><br>
            <form action="formulaires_Expression_besoins_carburant.php" method="post">
                <label for="carburant_id">Sélectionnez le carburant:</label>
                <select id="carburant_id" name="carburant_id" required>
                    <?php
                    // Récupérer la liste des carburants disponibles
                    $sql = "SELECT id, libelle, quantite FROM carburant";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['libelle']) . " - Quantité disponible: " . $row['quantite'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>Aucun carburant disponible</option>";
                    }
                    ?>
                </select><br><br>

                <label for="quantite_demande">Quantité demandée:</label>
                <input type="number" id="quantite_demande" name="quantite_demande" required><br><br>

                <div class="submit-container">
                    <button type="submit">Soumettre la demande</button>
                </div>
            </form>
    </fieldset><br>

    <h2><u><b>Historique des demandes de carburant</u></b> :</h2>

    <?php
    // Pagination : définir le nombre de résultats par page
    $results_per_page = 3;

    // Calculer le nombre total de pages
    $sql_count = "SELECT COUNT(*) AS total FROM demande_carburant";
    $result_count = $conn->query($sql_count);
    $row_count = $result_count->fetch_assoc();
    $total_results = $row_count['total'];
    $total_pages = ceil($total_results / $results_per_page);

    // Vérifier si une page spécifique est demandée, sinon, afficher la première page
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

    // Calculer l'offset pour la requête SQL
    $offset = ($page - 1) * $results_per_page;

    // Si la barre de recherche a été utilisée, filtrer les résultats
    if (!empty($search)) {
        $sql = "SELECT dc.id, c.libelle, dc.quantite_demande, dc.demandeur, dc.date_demande, dc.status
                FROM demande_carburant dc
                JOIN carburant c ON dc.carburant_id = c.id
                WHERE dc.id = ? OR c.libelle LIKE ? 
                ORDER BY dc.date_demande DESC";
        $stmt = $conn->prepare($sql);
        $like_search = "%" . $search . "%";
        $stmt->bind_param('is', $search, $like_search);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        // Modifier la requête pour limiter les résultats avec pagination
        $sql = "SELECT dc.id, c.libelle, dc.quantite_demande, dc.demandeur, dc.date_demande, dc.status
                FROM demande_carburant dc
                JOIN carburant c ON dc.carburant_id = c.id
                ORDER BY dc.date_demande DESC
                LIMIT $results_per_page OFFSET $offset";
        $result = $conn->query($sql);
    }
    ?>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Libellé du Carburant</th>
                <th>Quantité Demandée</th>
                <th>Demandeur</th>
                <th>Date de Demande</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['libelle']) . "</td>";
                    echo "<td>" . $row['quantite_demande'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['demandeur']) . "</td>";
                    echo "<td>" . $row['date_demande'] . "</td>";
                    // Statut en gras et avec des couleurs selon la valeur
                    if ($row['status'] === 'Validée') {
                        echo "<td class='valide'>" . htmlspecialchars($row['status']) . "</td>";
                    } elseif ($row['status'] === 'En attente') {
                        echo "<td class='en-attente'>" . htmlspecialchars($row['status']) . "</td>";
                    } else {
                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    }
                    echo "</tr>";
                }
            }else {
                echo "<script>
                    Swal.fire({
                        title: 'Alerte',
                        text: 'Demande non existante !',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    });
                </script>";
                echo "<tr><td colspan='6'>Aucune demande trouvée.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php
        // Affichage des liens de pagination
        if ($total_pages > 1) {
            if ($page > 1) {
                echo '<a href="formulaires_Expression_besoins_carburant.php?page=' . ($page - 1) . '">Précédent</a> ';
            }

            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $page) {
                    echo '<strong>' . $i . '</strong> ';
                } else {
                    echo '<a href="formulaires_Expression_besoins_carburant.php?page=' . $i . '">' . $i . '</a> ';
                }
            }

            if ($page < $total_pages) {
                echo '<a href="formulaires_Expression_besoins_carburant.php?page=' . ($page + 1) . '">Suivant</a>';
            }
        }
        ?>
    </div><br><br>

    <div class="button-container">
        <a href="../Formulaires/Formulaires_MG.php" class="btn-blue">Return</a>
    </div>

    <script>
        // Vérifier si le message PHP est défini et l'afficher
        <?php if (!empty($message)): ?>
            Swal.fire({
                title: '<?php echo strpos($message, 'Erreur') !== false ? 'Erreur' : 'Succès'; ?>',
                text: '<?php echo $message; ?>',
                icon: '<?php echo strpos($message, 'Erreur') !== false ? 'error' : 'success'; ?>',
                confirmButtonText: 'Ok'
            });
        <?php endif; ?>
    </script>
</body>
</html>

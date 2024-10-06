<?php
session_start();

// Vérifier si l'utilisateur connecté a le rôle 'Sup_Carburant' ou 'Agent Carburant'
if (!isset($_SESSION['email']) || $_SESSION['role'] != 51 && $_SESSION['role'] != 5 && $_SESSION['role'] != 0) {
    header("Location: ../unauthorized.php");
    exit();
}

include '../serv_projet1.php';

// Pagination settings
$limit = 4;  // Nombre de demandes par page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Page actuelle
$offset = ($page - 1) * $limit;  // Calcul de l'offset

// Calculer le nombre total de demandes
$sql_count = "SELECT COUNT(*) AS total FROM demande_carburant WHERE status = 'En attente' OR status = 'Validée'";
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_demandes = $row_count['total'];
$total_pages = ceil($total_demandes / $limit);  // Nombre total de pages


// Initialiser la requête par défaut
$sql = "SELECT dc.id, c.libelle, dc.quantite_demande, dc.demandeur, dc.status 
        FROM demande_carburant dc
        JOIN carburant c ON dc.carburant_id = c.id
        WHERE dc.status = 'En attente' OR dc.status = 'Validée'
        LIMIT $limit OFFSET $offset";  // Limiter le nombre d'enregistrements par page

// Vérification si une recherche a été soumise
$search = isset($_GET['search']) ? intval($_GET['search']) : null;

if ($search) {
    // Rechercher par ID dans toute la table sans pagination
    $sql = "SELECT dc.id, c.libelle, dc.quantite_demande, dc.demandeur, dc.status 
            FROM demande_carburant dc
            JOIN carburant c ON dc.carburant_id = c.id
            WHERE dc.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si la demande n'existe pas, afficher un message d'erreur avec Swal.fire
    // À l'intérieur de votre code PHP, définissez une variable
    $error_message = '';
    if ($result->num_rows == 0) {
        $error_message = 'Demande inexistante !';
    }

    } else {
    // Exécuter la requête par défaut avec pagination
        $result = $conn->query($sql);
    }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Demandes de Carburant</title>
    <link rel="stylesheet" type="text/css" href="../css/styles_liste_demande_carburant.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
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
    

    <h1><b><u>Liste des Demandes de Carburant</u> :</b></h1>
    
   <!-- Barre de recherche -->
   <div style="display: flex; justify-content: flex-end; margin-bottom: 10px; padding-right: 20px;">
        <form method="GET" action="">
            <input type="text" id="searchInput" name="search" placeholder="Rechercher par ID" style="padding: 8px; font-size: 16px;">
            <button type="submit" style="margin-left: 10px; padding: 8px 16px; background-color: #007bff; color: white; border: none; border-radius: 5px;">Rechercher</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Carburant</th>
                <th>Quantité Demandée</th>
                <th>Demandeur</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Appliquer le style selon le statut
                    $status = $row['status'];
                    $status_style = '';
                    if ($status == 'Validée') {
                        $status_style = '<span style="font-weight:bold; color:green;">' . $status . '</span>';
                    } elseif ($status == 'En attente') {
                        $status_style = '<span style="font-weight:bold; color:red;">' . $status . '</span>';
                    }
                    echo "<tr>
                            <td>" . $row['id'] . "</td>
                            <td>" . htmlspecialchars($row['libelle']) . "</td>
                            <td>" . $row['quantite_demande'] . "</td>
                            <td>" . htmlspecialchars($row['demandeur']) . "</td>
                            <td>" . $status_style . "</td>";
                           // Afficher le bouton 'Valider' uniquement pour le Sup_Carburant (rôle 5)
                    if ($_SESSION['role'] == 51) {
                        echo "<td><a href='valider_demande_carburant.php?id=". $row['id'] ."'>Valider</a></td>";
                    } else {
                        echo "<td></td>";
                    }

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Aucune demande en attente.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="pagination">
        <?php
        if ($total_pages > 1) {
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $page) {
                    echo "<span><strong>$i</strong></span> ";  // Page active
                } else {
                    echo "<a href='?page=$i'>$i</a> ";  // Lien vers d'autres pages
                }
            }
        }
        ?>
    </div>

    <br>
    <div class = 'btn-return'>
        <a href="../Formulaires/Formulaire_Carburant.php" class="btn-blue">Return</a>
    </div>

    <script>
        // Récupérer le message d'erreur
        var errorMessage = "<?php echo $error_message; ?>";

        if (errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: errorMessage,
                confirmButtonColor: '#007bff'
            });
        }
    </script>

    <?php $conn->close(); ?>
</body>
</html>

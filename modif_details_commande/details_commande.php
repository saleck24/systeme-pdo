<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

include '../serv_projet1.php';


// Vérifiez si 'commande_id' est défini soit dans GET soit dans POST
if (isset($_GET['commande_id'])) {
    $commande_id = $_GET['commande_id'];
} elseif (isset($_POST['commande_id'])) {
    $commande_id = $_POST['commande_id'];
} else {
    die('Erreur : commande_id non spécifié.');
}

//plus sécuriser, évite les attaques par injection sql.
$stmt = $conn->prepare("SELECT * FROM expression_besoins WHERE commande_id = ?");
$stmt->bind_param("i", $commande_id);
$stmt->execute();
$result = $stmt->get_result();

//Récupération du rôle de l'utilisateur grâce à la session.
$role = $_SESSION['role'];


//Mise à jour du prix_unitaire.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_price'])) {
        $id = $_POST['id'];
        $prix_unitaire = $_POST['prix_unitaire'];
        $désignation = $_POST['désignation'];
        $objet = $_POST['objet'];
        $nombres_articles = $_POST['nombres_articles'];
        $urgence = $_POST['urgence'];
        $type_services = $_POST['type_services'];

        // Mettre à jour les champs de la commande.
        $stmt_update = $conn->prepare("UPDATE expression_besoins SET prix_unitaire = ?, désignation = ?, objet = ?, nombres_articles = ?, urgence = ?, type_services = ? WHERE id = ?");
        $stmt_update->bind_param("dssssss", $prix_unitaire,$désignation, $objet, $nombres_articles, $urgence, $type_services, $id);
        
        if ($stmt_update->execute()) {
            
            //Gestion des fichiers uploadés.
            $target_dir = "../img/";

            // Mise à jour du fichier pièce
            if (!empty($_FILES['piece']['name'])) {
                $target_file = $target_dir . basename($_FILES["piece"]["name"]);
                if (move_uploaded_file($_FILES["piece"]["tmp_name"], $target_file)) {
                    $stmt_update_file = $conn->prepare("UPDATE expression_besoins SET piece = ? WHERE id = ?");
                    $pieceName = basename($_FILES["piece"]["name"]);
                    $stmt_update_file->bind_param("si", $pieceName, $id);
                    $stmt_update_file->execute();
                    $stmt_update_file->close();
                } else {
                    $error_message = 'Erreur lors du téléchargement du fichier pièce.';
                }
            }

            // Mise à jour du fichier image
            if (!empty($_FILES['image']['name'])) {
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $stmt_update_file = $conn->prepare("UPDATE expression_besoins SET image = ? WHERE id = ?");
                    $imageName = basename($_FILES["image"]["name"]);
                    $stmt_update_file->bind_param("si",$imageName , $id);
                    $stmt_update_file->execute();
                    $stmt_update_file->close();
                } else {
                    $error_message = 'Erreur lors du téléchargement du fichier image.';
                }
            }

            // Recalculer le prix total de la commande
            $stmt_recalculate = $conn->prepare("
                UPDATE commandes
                SET prix_total = (
                    SELECT IFNULL(SUM(prix_unitaire * nombres_articles), 0)
                    FROM expression_besoins 
                    WHERE commande_id = ?
                )
                WHERE id = ?");
            $stmt_recalculate->bind_param("ii", $commande_id, $commande_id);

            if ($stmt_recalculate->execute()) {
                $success_message = 'Prix unitaire mis à jour avec succès et prix total recalculé.';
            } else {
                $error_message = 'Erreur lors du recalcul du prix total: ' . $stmt_recalculate->error;
                
            }
            $stmt_recalculate->close();

        } else {
           $error_message = 'Erreur lors de la mise à jour.';
        }
        $stmt_update->close();
    }      
}

// Pagination
$limit = 3; // Limite de 3 lignes par page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Requête pour récupérer les commandes avec pagination
$stmt = $conn->prepare("SELECT * FROM expression_besoins WHERE commande_id = ? LIMIT ? OFFSET ?");
$stmt->bind_param("iii", $commande_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Obtenir le nombre total de lignes pour cette commande
$stmt_total = $conn->prepare("SELECT COUNT(*) as total FROM expression_besoins WHERE commande_id = ?");
$stmt_total->bind_param("i", $commande_id);
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$row_total = $total_result->fetch_assoc();
$total_rows = $row_total['total'];

// Calculer le nombre total de pages
$total_pages = ceil($total_rows / $limit);



?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Commande</title>
    <link rel="stylesheet" href="../css/styles_details_commande.css">
    <script src="../js/toaster.js"></script><!--Script qui traite les Toaster -->

    <!-- Script et feuille CSS pour la Swal.Fire du message de confirmation de la suppression -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
    
    <main>
        <h2><u>Détails de la Commande</u> :</h2>

        <!-- Affichage des messages de succès ou d'erreur -->
        <div id="toast-container" class="toast-container"></div>

        <!-- Affichage des messages "Succes" et "Error" grâce à l'appel de ShowToast() -->
        <?php if (isset($success_message)): ?>
            <script>showToast("<?php echo $success_message; ?>", "success");</script>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <script>showToast("<?php echo $error_message; ?>", "error");</script>
        <?php endif; ?>

        <table border="1">
            <tr>
                <th>Désignation</th>
                <th>Objet</th>
                <th>Pièce</th>
                <th>Image</th>
                <th>Nombre d'Articles</th>
                <th>Urgence</th>
                <th>Type de Services</th>
                <?php if ($role == 2 || $role == 21|| $role == 0): // Si l'utilisateur est user_adjoint_mg ou user_dept_administratif ou Admin ?>
                    <th>Prix Unitaire</th>
                <?php endif; ?>
                <th>Action</th>
            </tr>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr id='row-".$row['id']."'>";
                    echo "<td>" . htmlspecialchars($row['désignation']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['objet']) . "</td>";
                    echo "<td><img src='../img/" . htmlspecialchars($row['piece']) . "' alt='Piece' style='max-width:100px; max-height:100px;'></td>";
                    echo "<td><img src='../img/" . htmlspecialchars($row['image']) . "' alt='Image' style='max-width:100px; max-height:100px;'></td>";
                    echo "<td>" . htmlspecialchars($row['nombres_articles']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['urgence']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['type_services']) . "</td>";
                    if ($role == 2 || $role == 21 || $role == 0) {
                    echo "<td class='prix-unitaire'>" . htmlspecialchars($row['prix_unitaire']) . "</td>";
                    }
                    echo "<td>";
                    echo "<div class='button-container'>";
                    echo "<button class='btn-blue edit-button' data-id='" . htmlspecialchars($row['id']) . "' 
                            data-designation='" . htmlspecialchars($row['désignation']) . "'
                            data-objet='" . htmlspecialchars($row['objet']) . "'
                            data-piece='" . htmlspecialchars($row['piece']) . "'
                            data-image='" . htmlspecialchars($row['image']) . "'
                            data-nombres_articles='" . htmlspecialchars($row['nombres_articles']) . "'
                            data-urgence='" . htmlspecialchars($row['urgence']) . "'
                            data-type_services='" . htmlspecialchars($row['type_services']) . "'
                            data-prix_unitaire='" . htmlspecialchars($row['prix_unitaire']) . "'>Modifier</button>";
                    echo "<a href='#' class='btn-red' onclick='confirmDelete(" . $row['id'] . ", " . $row['commande_id'] . ")'>Supprimer</a>";
                    echo "</div>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>Aucune saisie trouvée pour cette commande</td></tr>";
            }
            ?>
        
        </table>
        <br><br>
        <!-- Liens de la Pagination-->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?commande_id=<?php echo $commande_id; ?>&page=<?php echo $page - 1; ?>">&laquo; Précédent</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <strong><?php echo $i; ?></strong>
                <?php else: ?>
                    <a href="?commande_id=<?php echo $commande_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?commande_id=<?php echo $commande_id; ?>&page=<?php echo $page + 1; ?>">Suivant &raquo;</a>
            <?php endif; ?>
        </div>  

        <a href="liste_besoins.php" class="btn-blue">Return</a>


        <!-- Modal pour la modification d'un élément de la commande-->
        <div id="editModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h2><b><u>Modifier l'élément</u> :</b></h2><br><br>
                <form id="editForm" action="details_commande.php?commande_id=<?php echo htmlspecialchars($commande_id); ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="editId">
                    <input type="hidden" name="commande_id" id="editCommandeId" value="<?php echo htmlspecialchars($commande_id); ?>">

                    <label><b>Désignation :</b>
                        <input type="text" name="désignation" id="editDesignation">
                    </label><br><br>
                    <label><b>Objet :</b>
                        <input type="text" name="objet" id="editObjet">
                    </label><br><br>
                    <label><b>Pièce :</b>
                        <input type="file" name="piece" id="editPiece">
                        <div id="currentPiece"></div>
                    </label><br><br>
                    <label><b>Image :</b>
                        <input type="file" name="image" id="editImage">
                        <div id="currentImage"></div>
                    </label><br><br>
                    <label><b>Nombre d'Articles :</b>
                        <input type="number" name="nombres_articles" id="editNombresArticles">
                    </label><br><br>
                    <label><b>Urgence :</b>
                        <select name="urgence" id="editUrgence">
                            <option value="Élevée">Élevée</option>
                            <option value="Moyenne">Moyenne</option>
                            <option value="Basse">Basse</option>
                        </select>
                    </label><br><br>
                    <label><b>Type de Services :</b>
                        <select name="type_services" id="editTypeServices">
                            <option value="Achat de fournitures">Achat de fournitures</option>
                            <option value="Prestation de service">Prestation de service</option>
                            <option value="Achat pour réparation">Achat pour réparation</option>
                            <option value="Achat de matériel">Achat de matériel</option>
                        </select>
                    </label><br><br>
                    <label><b>Prix Unitaire :</b>
                        <input type="number" step="0.01" name="prix_unitaire" id="editPrixUnitaire">
                    </label><br><br>
                    <button type="submit" name="update_price" class="btn-blue">Mettre à jour</button>
                </form>
            </div>
        </div> 
           
    </main>
    <script src="../js/script_details_commande.js"></script><!--Script pour la modal du bouton "Edit"-->
    <script src="../js/script_confirmation_supp_cmd.js"></script><!--Script pour le message de Confirmation de la Suppression-->
</body>
</html>

<?php
mysqli_close($conn);
?>

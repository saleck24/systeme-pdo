<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

include '../serv_projet1.php';

// Pagination
$limit = 10; // Nombre d'éléments par page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Recherche
$search_id = isset($_GET['search_id']) ? intval($_GET['search_id']) : 0;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categorie = $_POST['categorie'];
    $libelle = $_POST['libelle'];
    $prix = $_POST['prix'];
    $beneficiaire = $_POST['beneficiaire'];
    $quantite = $_POST['quantite'];
    $n_stock = $_POST['n_stock'];
    $objet = $_POST['objet'];
    
    // Calcul du coût total
    $total = $prix * $quantite;


    // Gestion de la pièce justificative
    if (isset($_FILES['piece']) && $_FILES['piece']['error'] == UPLOAD_ERR_OK) {
        $piece = $_FILES['piece']['name'];
        $tpiece = $_FILES['piece']['tmp_name'];
        $chemin_piece = "../img/" . $piece;
        move_uploaded_file($tpiece, $chemin_piece);
    } else {
        // Si aucun fichier n'est téléchargé, garder la valeur existante
        $piece = isset($_POST['existing_piece']) ? $_POST['existing_piece'] : null;
    }

    // Préparation de la requête d'insertion
    $sql = "INSERT INTO carburant (categorie, libelle, piece, prix, beneficiaire, quantite, n_stock, objet, total)
            VALUES (?, ?, ?, ?, ?, ?, ?,?,?)";
    
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }

    // Binding des paramètres
    $stmt->bind_param('sssdssssd', $categorie, $libelle, $piece, $prix, $beneficiaire, $quantite, $n_stock, $objet, $total);

    if ($stmt->execute()) {
        //echo "Données ajoutées avec succès.";
        $successMessage = 'Le stock de carburant a été ajouté avec succès !';
    } else {
        $errorMessage = 'Une erreur est survenue lors de l\'insertion du stock de carburant !!!';
    }

    $stmt->close();
}

// Construction de la requête SQL avec pagination et recherche
if ($search_id > 0) {
    $sql = "SELECT * FROM carburant WHERE id = ? LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iii', $search_id, $start, $limit);
} else {
    $sql = "SELECT * FROM carburant LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $start, $limit);
}

$stmt->execute();
$result = $stmt->get_result();

// Compter le nombre total de lignes pour la pagination
$count_sql = "SELECT COUNT(id) as total FROM carburant";
$count_result = $conn->query($count_sql);
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Stock Carburant</title>
    <link rel="stylesheet" type="text/css" href="../css/styles_stock_carburant.css">
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
    </header><br><br>

    

    <fieldset>

            <legend><b>Ajouter Carburant</b> :</legend>

           <!-- Barre de recherche -->
            <form method="GET" action="stock_carburant.php" class="search-bar">
                <input type="number" id="search-id" name="search_id" placeholder="Entrer l'ID">
                <button type="submit">Rechercher</button>
            </form>
            <br><br>


            <form action="stock_carburant.php" method="post" enctype="multipart/form-data">
                <label for="categorie">Catégorie:</label>
                <select id="categorie" name="categorie">
                    <option value="CARBURANT">CARBURANT</option>
                </select><br><br>

                <label for="objet">Objet:</label>
                <input type="text" id="objet" name="objet" required><br><br>
                
                <label for="libelle">Libellé:</label>
                <input type="text" id="libelle" name="libelle" required><br><br>
                
                <label for="piece">Pièce Justificative (Facture + Devis + BL):</label>
                <input type="file" id="piece" name="piece"><br><br>
                
                <label for="prix">Prix:</label>
                <input type="number" step="0.01" id="prix" name="prix" required><br><br>
                
                <label for="beneficiaire">Bénéficiaire:</label>
                <input type="text" id="beneficiaire" name="beneficiaire" required><br><br>
                
                <label for="quantite">Quantité:</label>
                <input type="number" id="quantite" name="quantite" required><br><br>
                
                <label for="n_stock">N° Stock:</label>
                <input type="text" id="n_stock" name="n_stock" required><br>

                
                <input type="hidden" name="existing_piece" value="<?php echo isset($piece) ? htmlspecialchars($piece) : ''; ?>">
                <div class = 'btn-submit'>
                    <button type="submit">Stocker votre Carburant</button>
                </div>
            </form>
    </fieldset>
    
    
    <main>
        <h2><u><b>Liste des Carburants dans le stock</b> :</u></h2>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Catégorie</th>
                <th>Objet</th>
                <th>Libellé</th>
                <th>Pièce</th>
                <th>Prix</th>
                <th>Bénéficiaire</th>
                <th>Quantité</th>
                <th>N° Stock</th>
                <th>Total</th>
                <th>Actions</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $pieceFileName = $row["piece"];
                    $pieceLink = $pieceFileName ? '<a href="../img/' . htmlspecialchars($pieceFileName) . '" download>' . htmlspecialchars($pieceFileName) . '</a>' : 'Aucun';
                    echo "<tr>
                        <td>" . $row["id"] . "</td>
                        <td>" . $row["categorie"] . "</td>
                        <td>" . $row["Objet"] . "</td>
                        <td>" . $row["libelle"] . "</td>
                        <td>" . $pieceLink . "</td>
                        <td>" . $row["prix"] . "</td>
                        <td>" . $row["beneficiaire"] . "</td>
                        <td>" . $row["quantite"] . "</td>
                        <td>" . $row["n_stock"] . "</td>
                        <td>" . $row["Total"] . "</td>
                        <td>";

                        // Affiche les boutons seulement si la demande de carburant n'a pas été validée
                        if ($row["validated"] != 1) {
                            echo "<button onclick=\"openEditModal(" . $row["id"] . ")\" class='edit-button'>Edit</button><br><br>";
                            echo "<a href='../modif_stock_carburant/delete_carburant.php?id=" . $row["id"] . "' class='delete-button'>Delete</a>";
                            
                            // Bouton "Valider" visible uniquement pour user_sup_carburant
                            if ($_SESSION['role'] == 51) {
                                echo "<a href='../modif_stock_carburant/valider_stock_carburant.php?id=" . $row["id"] . "' class='validate-button'>Valider</a>";
                            }
                        }
                        // Affiche le bouton 'Réquisition demande carburant' seulement si la demande de carburant est validée
                        if ($row["validated"] == 1) {
                            echo "<button onclick=\"openModal(" . $row["id"] . ")\" class='requisition-button'>Réquisition demande carburant</button>";
                        }
                        echo "</td>
                        
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='11'>Aucun carburant trouvé</td></tr>";
            }
            $conn->close();
            ?>
        </table>
        <!-- Pagination Links -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="stock_carburant.php?page=<?= $i; ?>"><?= $i; ?></a>
            <?php endfor; ?>
        </div>
    </main>
        <!--Message succes ou echec de l'enregistrement d'un stock de carburant -->
        <?php if (isset($successMessage)) { ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Stock enregistré !',
                text: '<?php echo addslashes($successMessage); ?>',
                confirmButtonText: 'OK'
            });
        </script>
        <?php } elseif (isset($errorMessage)) { ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: '<?php echo addslashes($errorMessage); ?>',
                    confirmButtonText: 'OK'
                });
            </script>
        <?php } ?>

        <?php
        // Affiche une alerte après la mise à jour d'un stock.
        if (isset($_SESSION['update_success'])) {
            if ($_SESSION['update_success'] === true) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès',
                        text: 'Stock mis à jour avec succès !'
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Erreur lors de la mise à jour du Stock !!!'
                    });
                </script>";
            }
            unset($_SESSION['update_success']); // Efface la variable de session pour ne pas répéter l'alerte
        }
        ?>

        <?php
        // Affiche une alerte après la suppression d'un stock.
        if (isset($_SESSION['delete_success'])) {
            if ($_SESSION['delete_success'] === true) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès',
                        text: 'Stock supprimé avec succès !'
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Erreur lors de la suppression du Stock !!!'
                    });
                </script>";
            }
            unset($_SESSION['delete_success']); // Efface la variable de session pour ne pas répéter l'alerte
        }
        ?>




     <!-- Fenêtre modale (la pop-up) -->
     <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>

    <!-- Fenêtre modale pour l'édition -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <div id="edit-modal-body"></div>
        </div>
    </div>


    <script>
        function openModal(id) {
            var modal = document.getElementById('myModal');
            var modalBody = document.getElementById('modal-body');

            // Construire l'URL en utilisant l'ID passé
            var url = '../modif_stock_carburant/requisition_demande_carburant.php?id=' + id;
            
            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    modalBody.innerHTML = xhr.responseText;
                    modal.style.display = 'block';
                }
            };
            xhr.send();
        }

        function closeModal() {
            var modal = document.getElementById('myModal');
            modal.style.display = 'none';
        }

        // Fermer la modale si l'utilisateur clique en dehors de la modale
        window.onclick = function(event) {
            var modal = document.getElementById('myModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
//-------------------------------------------------------------------------------
        //fonction de la modal de "Edit".
        function openEditModal(id) {
            var modal = document.getElementById('editModal');
            var modalBody = document.getElementById('edit-modal-body');

            // Appel AJAX pour charger le formulaire d'édition
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '../modif_stock_carburant/edit_carburant.php?id=' + id, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    modalBody.innerHTML = xhr.responseText;
                    modal.style.display = 'block';
                }
            };
            xhr.send();
        }

        function closeEditModal() {
            var modal = document.getElementById('editModal');
            modal.style.display = 'none';
        }

        // Fermer la modal si l'utilisateur clique en dehors de la modal
        window.onclick = function(event) {
            var modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

    </script>

   <div class = 'btn-return'>
        <a href="../Formulaires/Formulaire_Carburant.php" class="btn-blue">Return</a>      
    </div>
</body>
</html>

<?php
session_start();
require_once("../serv_projet1.php");

// Ajout d'une nouvelle catégorie
if (isset($_POST['eng'])){
    $titre = $_POST['titre'];
    $salaire = $_POST['salaire'];
    
    // Convertir la première lettre du titre en majuscule pour la clé
    $cle = strtoupper(substr($titre, 0, 1));
    
    // Insérer les données dans la table 'categorie' avec requête préparée
    $stmt = $conn->prepare("INSERT INTO categorie(Titre, Cle, salaire) VALUES(?, ?, ?)");
    $stmt->bind_param("ssd", $titre, $cle, $salaire);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: Formulaire_categorie.php?status=success&type=add");
        exit();
    } else {
        $errorMessage = "Erreur : " . $stmt->error;
        $stmt->close();
        header("Location: Formulaire_categorie.php?status=error&type=add&message=" . urlencode($errorMessage));
        exit();
    }
}

// Modification du Salaire d'une Categorie
if (isset($_POST['update_categorie'])) {
    $id = intval($_POST['edit_id']);
    $new_salaire = $_POST['edit_categorie'];
    
    $stmt = $conn->prepare("UPDATE categorie SET salaire=? WHERE code=?");
    $stmt->bind_param("di", $new_salaire, $id);
    
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: Formulaire_categorie.php?status=success&type=update");
        exit();
    } else {
        $errorMessage ="Erreur : " . $stmt->error;
        $stmt->close();
        header("Location: Formulaire_categorie.php?status=error&type=update&message=" . urlencode($errorMessage));
        exit();
    }
}


// Suppression de la Categorie
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete = mysqli_query($conn, "DELETE FROM categorie WHERE code=$id");
    if ($delete) {
        // Redirection avec le statut de succès après la suppression
        header("Location: Formulaire_categorie.php?status=success&type=delete");
        exit();
    } else {
        $errorMessage = "Erreur : " . mysqli_error($conn);
        header("Location: Formulaire_categorie.php?status=error&type=delete&message=" . urlencode($errorMessage));
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de catégorie</title>
    <link rel="stylesheet" href="../css/styles_formulaire-categorie.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <header>
        <nav>
            <div class="logo-container">
                <img src="../img/LOGO.png" alt="Logo de Kenz Mining SA" class="logo-image">
            </div>
            <a href="../logout.php" class="logout-link">
                <div class="logout"> 
                    <img src="../img/icon_logout.png" alt="icon_logout" class="logout">
                    <p>Logout</p>
                </div>
            </a>
        </nav>
    </header>
    <br><br>
    <fieldset>
        <legend><b>Formulaire de Catégorie</b></legend>

        <form method="post" action="">
            <table width="50%" align="center">
                <tr>
                    <td><input type="text" name="titre" placeholder="Titre de la catégorie" required></td>
                </tr>
                <tr>
                    <td><input type="number" name="salaire" placeholder="Salaire" required></td>
                </tr>
                <tr>
                    <td><input type="submit" name="eng" value="Sauvegarder la catégorie"></td>
                </tr>
            </table>

            <table class="categorie-table" width="100%" align="center">
                <tr style="background-color: Green;">
                    <th>Code</th>
                    <th>Titre</th>
                    <th>Cle</th>
                    <th>Salaire</th>
                    <th>Action :</th>
                </tr>

                <!-- Récupèrer toutes les données de la table 'categorie' -->
                <?php
                $lc = mysqli_query($conn, "SELECT * FROM categorie");
                while($l = mysqli_fetch_array($lc)){
                ?>
                <tr>
                    <td><?php echo $l['code']; ?></td>
                    <td><?php echo $l['Titre']; ?></td>
                    <td><?php echo $l['Cle']; ?></td>
                    <td><?php echo $l['salaire']; ?></td>
                    <td>
                        <button type="button" class="btn-edit" onclick="openModal(<?php echo $l['code']; ?>, '<?php echo $l['salaire']; ?>')">Edit</button>
                        <a href="javascript:void(0)" class="btn-delete" onclick="confirmDelete(<?php echo $l['code']; ?>)">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </form>
    </fieldset>

    <!-- Le bouton "Return" -->
    <div class="btn-container">
        <a href="Formulaires_RH.php" class="btn-blue">Return</a>
    </div>

    <!-- Modal pour la modification -->
    <div id="editModal" class="modal">
        <div class="modal-header">Modifier la Categorie</div>
        <form method="post" action="">
            <input type="hidden" name="edit_id" id="edit_id">
            <div class="modal-content">
                <label for="edit_categorie"><b>Salaire :</b>
                    <input type="number" name="edit_categorie" id="edit_categorie" required>
                </label>
            </div>
            <div class="modal-footer">
                <button type="submit" name="update_categorie" class="btn-update">Mettre à jour</button>
                <button type="button" class="btn-cancelled" onclick="closeModal()">Annuler</button>
            </div>
        </form>
    </div>

    <script>
        // Fonction pour afficher les messages via Swal
        function showAlert(type, title, text) {
            Swal.fire({
                icon: type,
                title: title,
                text: text,
                confirmButtonText: 'OK'
            });
        }

        // Vérifier les paramètres dans l'URL pour afficher l'alerte appropriée
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const type = urlParams.get('type');
            const message = urlParams.get('message');

            if (status === 'success' && type === 'add') {
                showAlert('success', 'Ajouté', 'Categorie ajouté avec succès !');
            } else if (status === 'success' && type === 'update') {
                showAlert('success', 'Mise à jour réussie', 'Salaire de la Categorie a été mise à jour avec succès !');
            } else if (status === 'success' && type === 'delete') {
                showAlert('success', 'Supprimé', 'Categorie supprimée avec succès !');
            } else if (status === 'error' && type === 'add') {
                showAlert('error', 'Erreur lors de l\'ajout de la Categorie.', message);
            } else if (status === 'error' && type === 'update') {
                showAlert('error', 'Erreur lors de la modification du Salaire de la Categorie.', message);
            } else if (status === 'error' && type === 'delete') {
                showAlert('error', 'Erreur lors de la suppression de la Categorie.', message);
            }

        };

        // Fonction pour ouvrir la modal avec les informations de la catégorie à modifier
        function openModal(id, salaire) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_categorie').value = salaire;
            document.getElementById('editModal').style.display = 'block';
        }

        // Fonction pour fermer la modal
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Confirmation avant suppression
        function confirmDelete(id) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Vous ne pourrez pas annuler cette action !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer !'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'Formulaire_categorie.php?delete=' + id;
                }
            });
        }
    </script>
</body>
</html>

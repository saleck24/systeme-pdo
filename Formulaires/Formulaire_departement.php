<?php
session_start();
require_once("../serv_projet1.php");

// Sauvegarde d'un nouveau département
if (isset($_POST['eng'])){
    $departement = $_POST['departement'];
    $Ajout = mysqli_query($conn,"INSERT INTO departement(departement) VALUES('$departement')");
    if ($Ajout) {
        header("Location: Formulaire_departement.php?status=success&type=add");
        exit();
    } else {
        $errorMessage = "Erreur : " . mysqli_error($conn);
        header("Location: Formulaire_departement.php?status=error&type=add&message=" . urlencode($errorMessage));
        exit();
    }
}

// Modification d'un département
if (isset($_POST['edit_departement'])) {
    $id = intval($_POST['edit_id']);
    $new_departement = $_POST['edit_departement'];
    $update = mysqli_query($conn, "UPDATE departement SET departement='$new_departement' WHERE num_departement=$id");
    if ($update) {
        header("Location: Formulaire_departement.php?status=success&type=update");
        exit();
    } else {
        $errorMessage ="Erreur : " . mysqli_error($conn);
        header("Location: Formulaire_departement.php?status=error&type=update&message=" . urlencode($errorMessage));
        exit();
    }
}

// Suppression d'un département
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete = mysqli_query($conn, "DELETE FROM departement WHERE num_departement=$id");
    if ($delete) {
        header("Location: Formulaire_departement.php?status=success&type=delete");
        exit();
    } else {
        $errorMessage = "Erreur : " . mysqli_error($conn);
        header("Location: Formulaire_departement.php?status=error&type=delete&message=" . urlencode($errorMessage));
        exit();
    }
}

// Récupérer le nombre total de départements
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM departement");
$row = mysqli_fetch_assoc($result);
$total_departements = $row['total'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de département</title>
    <link rel="stylesheet" href="../css/styles_formulaire-departement.css">
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
        <legend><b>Formulaire de Département</b></legend>

        <form method="post" action="">
            <div style="text-align: center;">
                <input type="text" name="departement" placeholder="Nom du département" required style="width: 250px;">
                <br><br>
                <input type="submit" name="eng" value="Sauvegarder le département" style="width: 300px;">
                <br><br>

                <label><b>Nombre total de départements : <?php echo $total_departements; ?></b></label>
            </div>

            <table class="departement-table" width="100%" align="center" border="1">
                <tr style="background-color : Blue;">
                    <th>Numero du departement :</th>
                    <th>Departement :</th>
                    <th>Action :</th>
                </tr>

                <?php
                $ld = mysqli_query($conn, "SELECT * FROM departement");
                $num_departement = 1; 
                while($l = mysqli_fetch_array($ld)) {
                ?>
                
                <tr>
                    <td><?php echo $num_departement ?></td>
                    <td><?php echo $l['departement'] ?></td>
                    <td>
                        <button type="button" class="btn-edit" onclick="openModal(<?php echo $l['num_departement']; ?>, '<?php echo $l['departement']; ?>')">Edit</button>
                        <a href="javascript:void(0)" class="btn-delete" onclick="confirmDelete(<?php echo $l['num_departement']; ?>)">Delete</a>
                    </td>
                </tr>
                
                <?php
                    $num_departement++; 
                }
                ?>
            </table>
        </form>

        <br><br>
        <a href="Formulaires_RH.php" class="btn-blue">Return</a>
        

    </fieldset>

     <!-- Modal pour la modification -->
     <div id="editModal" class="modal">
        <div class="modal-header">Modifier le Département</div>
        <form method="post" action="">
            <input type="hidden" name="edit_id" id="edit_id">
            <div class="modal-content">
                <label for="edit_departement"><b>Département :</b>
                    <input type="text" name="edit_departement" id="edit_departement" required>
                </label>
                
            </div>
            <div class="modal-footer">
                <button type="submit" name="update_departement" class="btn-update">Mettre à jour</button>
                <button type="button" class="btn-cancelled" onclick="closeModal()">Annuler</button>
            </div>
        </form>
    </div>

    <script>
//----------------------------------------------------------------------------------------------
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
                showAlert('success', 'Ajouté', 'Département ajouté avec succès !');
            } else if (status === 'success' && type === 'update') {
                showAlert('success', 'Mise à jour réussie', 'Département a été mise à jour avec succès !.');
            } else if (status === 'error' && type === 'add') {
                showAlert('error', 'Erreur lors de l\'ajout du département.', message);
            } else if (status === 'error' && type === 'update') {
                showAlert('error', 'Erreur lors de la modification du département.', message);
            }
        };
//--------------------------------------------------------------------------------------------
        // Fonction pour ouvrir la modal avec les informations du département à modifier
        function openModal(id, departement) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_departement').value = departement;
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
                    // Redirection vers l'URL de suppression avec l'ID du département
                    window.location.href = "Formulaire_departement.php?delete=" + id;
                }
            });
        }

    </script>
</body>
</html>
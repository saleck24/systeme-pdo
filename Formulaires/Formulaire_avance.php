<?php
session_start();
require_once("../serv_projet1.php");

$search = "";
if (isset($_POST['search'])) {
    $search = htmlspecialchars($_POST['search']); // Échapper les données pour éviter les injections XSS
}


if (isset($_POST['eng'])) {
    $personnel_id = $_POST['personnel_id'];
    $montant = $_POST['montant'];

    // Validation du montant pour s'assurer qu'il est un nombre valide
    if (!is_numeric($montant) || $montant <= 0) {
        $errorMessage = "Le montant doit être un nombre positif.";
    }

    // Définir la date actuelle et le mois courant
    $date = date('Y-m-d H:i:s'); // Date au format AAAA-MM-JJ HH:MM:SS
    $mois = date('m'); // Mois actuel au format MM

    // Insère les données
    //$Ajout = mysqli_query($conn, "INSERT INTO demande_avance (personnel_id, montant, mois, date, traitée) VALUES ('$personnel_id', '$montant', '$mois', '$date',0)");

    // Utilisation des requêtes préparées pour prévenir les injections SQL
    $stmt = $conn->prepare("INSERT INTO demande_avance (personnel_id, montant, mois, date, traitée) VALUES (?, ?, ?, ?, 0)");
    $stmt->bind_param("iiss", $personnel_id, $montant, $mois, $date);

    // Vérification de la réussite de l'insertion
    if ($stmt->execute()) {
        $successMessage = "Votre demande d'avance a été enregistrée avec succès.";
    } else {
        $errorMessage = "Une erreur est survenue lors de l'enregistrement de la demande.";
    }
    $stmt->close();
}

// Configuration de la pagination
$limit = 3;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start_from = ($page - 1) * $limit;

// Requête pour obtenir le nombre total d'enregistrements
$total_query = $conn->prepare("SELECT COUNT(*) FROM demande_avance a
                               JOIN personnel p ON a.personnel_id = p.id
                               WHERE p.nom LIKE ? AND a.traitée = 0");
$search_term = "%$search%";
$total_query->bind_param("s", $search_term);
$total_query->execute();
$total_query->bind_result($total_records);
$total_query->fetch();
$total_query->close();

$total_pages = ceil($total_records / $limit);


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Demande d'avance</title>
    <link rel="stylesheet" href="../css/styles_formulaire-avance.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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
        <legend><b>Formulaire Demande d'avance</b></legend>

        <!-- Barre de recherche -->
        <form method="POST" action="">
            <div style="text-align: right;">
                <input type="text" name="search" placeholder="Rechercher employé" value="<?php echo $search; ?>">
                <input type="submit" value="Rechercher">
            </div>
        </form>

        <!-- Formulaire pour demander une avance -->
        <form method="post" action="" enctype="multipart/form-data" onsubmit="return validateMontant()">
            <table width="50%" align="center">
                <tr>
                    <td><label> <b>Employé demandeur : </b></label></td>
                    <td>
                        <select name="personnel_id" required>
                            <option value="">Sélectionner l'employé</option>
                            <?php 
                            $p = mysqli_query($conn, "SELECT id, nom, prenom FROM personnel");
                            while ($per = mysqli_fetch_array($p)) {
                            ?>
                            <option value="<?php echo $per['id'] ?>"> <?php echo $per['nom'] . ' ' . $per['prenom'] ?> </option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label> <b>Montant Demandé :</b> </label></td>
                    <td><input type="text" name="montant"  id="montant" placeholder="Montant" required></td>
                </tr>
                <tr>
                    <td><label><b>Date : </b></label></td>
                    <td><?php echo date('Y-m-d H:i:s'); // Date actuelle avec l'heure ?></td>
                </tr>
                <tr>
                    <td><label><b>Mois :</b> </label></td>
                    <td><?php echo date('F'); // Nom du mois actuel ?></td>
                </tr>
                
                <tr>
                    <td colspan="2" style="text-align: center; padding-top: 15px;">
                        <input type="submit" name="eng" value="Sauvegarder">
                    </td>
                </tr>
                
            </table>
            <br><br>
            <table class="avance-table" width="100%" align="center" border="1">
                <tr style="background-color : Orange;">
                    <th>Nom & Prenom de l'Employé</th>
                    <th>Montant</th>
                    <th>Date</th>
                    <th>Mois </th>
                    <th>Actions </th>
                </tr>
                <?php
                $lp = mysqli_query($conn, "SELECT a.*, CONCAT(p.nom, ' ', p.prenom) AS nom_complet FROM demande_avance a
                                    JOIN personnel p ON a.personnel_id = p.id
                                    WHERE a.traitée = 0 AND (p.nom LIKE '%$search%' OR p.prenom LIKE '%$search%')
                                    LIMIT $start_from, $limit");

                while ($l = mysqli_fetch_array($lp)) {
                ?>
                <tr id="row_<?php echo $l['id']; ?>">
                    <td> <?php echo $l['nom_complet'] ?></td>
                    <td> <?php echo $l['montant'] ?> </td>
                    <td> <?php echo $l['date'] ?> </td>
                    <td> <?php echo $l['mois'] ?> </td>
                    <td>
                        &nbsp;&nbsp;
                        <!--  supprimer la Demande -->
                        <button type="button" class="btn-common btn-delete" onclick="deleteAdvance(<?php echo $l['id']; ?>)">Supprimer</button>
                        <!-- Afficher la Demande -->
                        <button type="button" class="btn-common btn-info" onclick="showModal(<?php echo $l['id'] ?>)"> Afficher l'avance </button>
                        <!-- Bouton Modifier -->
                        <button type="button" class="btn-common btn-edit" onclick="showEditModal(<?php echo $l['id'] ?>, '<?php echo $l['nom_complet'] ?>', <?php echo $l['montant'] ?>)">Modifier</button>
                        <!-- Bouton Valider visible que pour "Admin" et "DAF"-->
                        <?php if ($_SESSION['role'] == 0 || $_SESSION['role'] == 4): // 0 = Admin, 4 = DAF ?>
                            <button type="button" class="btn-common btn-validate" onclick="validateDAF(<?php echo $l['id'] ?>)">Valider DAF</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php } ?>
            </table>

            
            <!-- Pagination -->
            <div class="pagination">
                <?php
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<a href='formulaire_avance.php?page=".$i."'";
                    if ($i == $page) echo " class='active'";
                    echo ">".$i."</a> ";
                }
                ?>
            </div>
        </form>
        
        <!--Message succes ou echec de l'enregistrement d'une demande d'avance-->
        <?php if (isset($successMessage)) { ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Demande enregistrée!',
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

        <!--Bouton "Return"-->
        <div class="btn-container">
            <a href="Formulaires_RH.php" class="btn-blue">Return</a>
        </div>
    </fieldset>

    <!-- Modal pour afficher les détails de l'avance -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>

    <!-- Modal pour modifier les détails de l'avance -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2 style="text-align: center;"><b><u>Modifier l'avance :</h2></u></b><br>
            <form id="editForm" method="post" action="update_avance.php">
                <input type="hidden" name="id" id="edit_id">
                <label for="edit_personnel_id">Employé demandeur :</label>
                <select name="personnel_id" id="edit_personnel_id" required>
                    <?php 
                    // Remplir le select avec les employés
                    $p = mysqli_query($conn, "SELECT id, nom, prenom FROM personnel");
                    while ($per = mysqli_fetch_array($p)) {
                    ?>
                    <option value="<?php echo $per['id'] ?>"> <?php echo $per['nom'] . ' ' . $per['prenom'] ?> </option>
                    <?php } ?>
                </select>
                <br><br>
                <label for="edit_montant">Montant demandé :</label>
                <input type="text" name="montant" id="edit_montant" required>
                <br><br>
                <input type="submit" value="Sauvegarder les modifications">
            </form>
        </div>
    </div>

    <script>
//------------------------------------------------------------------------------------------------
    
//------------------------------------------------------------------------------------------------
        // Validation du montant
        function validateMontant() {
            var montant = document.getElementById('montant').value;
            if (isNaN(montant) || montant <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Montant incorrect',
                    text: 'Veuillez entrer un montant valide et positif.',
                    confirmButtonText: 'OK'
                });
                return false; // Empêche l'envoi du formulaire
            }
            return true; // Autorise l'envoi du formulaire
        }
//----------------------------------------------------------------------------------------------------
//fonction pour Supprimer une demande d'avance.
        function deleteAdvance(id) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Voulez-vous vraiment supprimer cette demande ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Faire une requête AJAX pour supprimer la demande
                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", "../modif_supp_form_avance/delete_form_avance.php?id=" + id, true);
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            // Si la suppression est réussie, supprimer la ligne du tableau
                            var row = document.getElementById("row_" + id);
                            if (row) {
                                row.parentNode.removeChild(row);
                            }

                            // Afficher un message de confirmation
                            Swal.fire({
                                icon: 'success',
                                title: 'Supprimée !',
                                text: 'La demande a été supprimée avec succès.',
                                confirmButtonText: 'OK'
                            });
                        } else if (xhr.readyState == 4 && xhr.status !== 200) {
                            // Si une erreur survient, afficher un message d'erreur
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: 'Une erreur est survenue lors de la suppression.',
                                confirmButtonText: 'OK'
                            });
                        }
                    };
                    xhr.send("id=" + id); // Envoi de l'ID de la demande d'avance au fichier PHP
                }
            });
        }

//-------------------------------------------------------------------------
//fonction pour Afficher la Modal de 'Modifier'
        function showModal(id) {
            var modal = document.getElementById("myModal");
            var modalBody = document.getElementById("modal-body");

            // Faire une requête AJAX pour récupérer les détails de l'avance
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "../modif_supp_form_avance/get_avance.php?id=" + id, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    modalBody.innerHTML = xhr.responseText;
                    modal.style.display = "block";
                }
            };
            xhr.send();

            // Fermer la modal lorsque l'utilisateur clique sur (x)
            var span = document.getElementsByClassName("close")[0];
            span.onclick = function() {
                modal.style.display = "none";
            }

            // Fermer la modal lorsqu'on clique en dehors de celle-ci
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        }
//---------------------------------------------------------------
//Modal pour le bouton "Modifier"
        function showEditModal(id, nomComplet, montant) {
            var modal = document.getElementById("editModal");
            document.getElementById("edit_id").value = id;
            document.getElementById("edit_personnel_id").value = nomComplet;
            document.getElementById("edit_montant").value = montant;

            modal.style.display = "block";
        }

        function closeEditModal() {
            var modal = document.getElementById("editModal");
            modal.style.display = "none";
        }

        // Fermer la modal lorsque l'utilisateur clique en dehors de celle-ci
        window.onclick = function(event) {
            var modal = document.getElementById("editModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
//-------------------------------------------------------------------------------
// Fonction pour Valider la demande d'avance
        function validateDAF(id) {
            // Confirmation avant validation avec Swal.fire
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Voulez-vous vraiment valider cette demande ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, valider !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Faire une requête AJAX pour valider la demande
                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", "../modif_supp_form_avance/validate_daf.php?id=" + id, true);
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            // Afficher un message de confirmation
                            Swal.fire({
                                icon: 'success',
                                title: 'Validée !',
                                text: 'La demande a été validée avec succès.',
                                confirmButtonText: 'OK'
                            });
                        } else if (xhr.readyState === 4 && xhr.status !== 200) {
                            // Si une erreur survient, afficher un message d'erreur
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: 'Erreur lors de la validation de la demande.',
                                confirmButtonText: 'OK'
                            });
                        }
                    };
                    xhr.send();
                }
            });
        }

    /*function validateDAF(id) {
        // Confirmation avant validation
        if (confirm('Voulez-vous vraiment valider cette demande ?')) {
            // Faire une requête AJAX pour valider la demande
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "../modif_supp_form_avance/validate_daf.php?id=" + id, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert(xhr.responseText); // Afficher le message de succès ou d'erreur
                    //location.reload(); // Recharger la page après validation
                }
            };
            xhr.send();
        }
    }*/
    </script>
</body>

</html>

<?php
session_start();
require_once("../serv_projet1.php");

$search = "";
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}

// Vérifie si le formulaire a été soumis
if (isset($_POST['eng'])){
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $departement = $_POST['departement'];
    $photo = $_FILES['photo']['name'];// Récupère le nom de l'image
    $tphoto = $_FILES['photo']['tmp_name'];// Récupère le chemin temporaire de l'image
    move_uploaded_file($tphoto,".././img/$photo");// Déplace l'image vers le dossier 'img'
    $categorie = $_POST['categorie'];
    $salaire = $_POST['salaire'];

    $nni_emp = $_POST['nni_emp'];
    $matricule_emp = $_POST['matricule_emp'];
    $lieu_travail = $_POST['lieu_travail'];
    $fonction = $_POST['fonction'];
    $suphierarchie = $_POST['suphierarchie'];
    $emailsup = $_POST['emailsup'];
    $datesaisie = $_POST['datesaisie'];


    // Récupérer le num_departement basé sur le nom du département
    $result = mysqli_query($conn, "SELECT num_departement FROM departement WHERE departement = '$departement'");
    $row = mysqli_fetch_assoc($result);
    $num_departement = $row['num_departement'];


    // Insère les données dans la table 'personnel'
    $Ajout = mysqli_query($conn,"insert into personnel(nom, prenom, photo, num_departement, categorie, salaire,nni_emp,matricule_emp,lieu_travail,
                        fonction,suphierarchie,emailsup, datesaisie) values
                        ('$nom','$prenom', '$photo', '$num_departement','$categorie','$salaire','$nni_emp', '$matricule_emp', 
                        '$lieu_travail', '$fonction', '$suphierarchie', '$emailsup', '$datesaisie')");

    if ($Ajout) {
        // Redirige vers la même page avec un message de succès
        header("Location: Formulaire_personnel.php?status=success&type=add");
        exit(); //sortir après la redirection
    } else {
        $errorMessage = "Erreur : " . mysqli_error($conn);
        header("Location: Formulaire_personnel.php?status=error&type=add&message=" . urlencode($errorMessage));
        exit();
    }
    
    
}

//la Soumission après modification et mis à jour des des données d'un personnel
if (isset($_POST['updatePersonnel'])) {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $departement = $_POST['departement'];
    $categorie = $_POST['categorie'];
    $salaire = $_POST['salaire'];
    $nni_emp = $_POST['nni_emp'];
    $matricule_emp = $_POST['matricule_emp'];
    $lieu_travail = $_POST['lieu_travail'];
    $fonction = $_POST['fonction'];
    $suphierarchie = $_POST['suphierarchie'];
    $emailsup = $_POST['emailsup'];
    $datesaisie = $_POST['datesaisie'];

    // Gérer l'upload de la nouvelle photo si une nouvelle photo est téléchargée
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        // Dossier de destination
        $dossier_upload = '../img/';
        $photo_nom = basename($_FILES['photo']['name']);
        $destination_fichier = $dossier_upload . $photo_nom;

        // Déplacer le fichier uploadé dans le dossier de destination
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination_fichier)) {
            // Si le fichier a été déplacé correctement,mettre à jour la base de données avec le nom du fichier
            $photo = $photo_nom;

            // Mettre à jour la photo et autres informations dans la base de données.
            $update = mysqli_query($conn, "UPDATE personnel SET nom = '$nom', prenom = '$prenom', num_departement = 
                    (SELECT num_departement FROM departement WHERE departement = '$departement'), 
                    categorie = '$categorie', salaire = '$salaire', nni_emp = '$nni_emp', 
                    matricule_emp = '$matricule_emp', lieu_travail = '$lieu_travail', 
                    fonction = '$fonction', suphierarchie = '$suphierarchie', 
                    emailsup = '$emailsup', datesaisie = '$datesaisie', photo='$photo' WHERE id = '$id'");

        } else {
            //echo "Erreur lors de l'upload de l'image.";
            $errorMessage = "Erreur lors de l'upload de l'image.";
            header("Location: Formulaire_personnel.php?status=error&type=update&message=" . urlencode($errorMessage));
            exit();
        }
    } else {
        // Si aucune photo n'est uploadée, mettre à jour seulement les autres champs
        $update = mysqli_query($conn,"UPDATE personnel SET nom='$nom', prenom='$prenom', departement='$departement', categorie='$categorie', 
                      salaire='$salaire', nni_emp='$nni', matricule_emp='$matricule', lieu_travail='$lieu_travail', 
                      fonction='$fonction', suphierarchie='$sup_hierarchie', emailsup='$email_sup', datesaisie='$date_saisie' 
                      WHERE id='$id'");
    }
    // Vérifier si la mise à jour a réussi
    if ($update) {
        header("Location: Formulaire_personnel.php?status=success&type=update");
        exit();
    }else{
        $errorMessage = "Erreur : " . mysqli_error($conn);
        header("Location: Formulaire_personnel.php?status=error&type=update&message=" . urlencode($errorMessage));
        exit();
    }
}


//la configuration de la pagination 
$limit = 3; // Nombre de ligne affichée par page.
if (isset($_GET["page"])) {
    $page  = $_GET["page"];
} else {
    $page = 1;
};
$start_from = ($page - 1) * $limit;


$query = "SELECT p.*, d.departement, c.Titre AS categorie_titre 
          FROM personnel p 
          LEFT JOIN departement d ON p.num_departement = d.num_departement 
          LEFT JOIN categorie c ON p.categorie = c.Titre 
          WHERE p.nom LIKE '%$search%' OR p.prenom LIKE '%$search%' OR d.departement LIKE '%$search%'
          LIMIT $start_from, $limit";
$lp = mysqli_query($conn, $query);

// Requête pour compter le nombre total de résultats
$total_query = "SELECT COUNT(*) FROM personnel 
                WHERE nom LIKE '%$search%' OR prenom LIKE '%$search%' OR num_departement IN 
                (SELECT num_departement FROM departement WHERE departement LIKE '%$search%')";
$total_result = mysqli_query($conn, $total_query);
$total_records = mysqli_fetch_array($total_result)[0];
$total_pages = ceil($total_records / $limit);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <!-- Permet une mise en page responsive --> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Personnel</title>
    <link rel="stylesheet" href="../css/styles_formulaire-personnel.css">
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
        <legend> <b>Formulaire Personnel</b></legend>
        <!-- Formulaire de recherche -->
        <div style="text-align: right;">
            <form method="post" action="">
                <input type="text" id="search" name="search" placeholder="Rechercher un employé..." value="<?php echo htmlspecialchars($search); ?>">
                <input type="submit" value="Rechercher">
            </form>
        </div>
        
        <!-- Formulaire pour ajouter un nouveau personnel -->
        <form method="post" action="" enctype="multipart/form-data">
            
            <table width= "50%" align = "center">
                <tr>
                    <td><label> <b>Nom : </b></label></td>
                    <td><input type = "text" name= "nom" placeholder="Nom" required></td>
                </tr>

                <tr>
                    <td><label> <b>Prenom :</b> </label></td>
                    <td><input type = "text" name= "prenom" placeholder="Prenom" required></td>
                </tr>

                <tr>
                    <td><label> <b>Département :</b> </label></td>
                    <td><select name= "departement" required>
                        <option value = ""> Merci de sélectionner le département</option>  
                        <?php $d = mysqli_query($conn, "select distinct(departement) from departement") ;//Récupère les départements distincts
                            while($dep = mysqli_fetch_array($d)){
                        ?>
                        <option value = "<?php echo $dep['departement']?>"> <?php echo $dep['departement']?> </option> 
                        <?php }?>
                    </select></td>
                </tr>

                <tr>
                    <td><label> <b>Categorie : </b></label></td>
                    <td>
                        <select name="categorie" required>
                            <option value="">Merci de sélectionner la catégorie</option>
                            <?php
                            $c = mysqli_query($conn, "select Titre from categorie");
                            while ($cat = mysqli_fetch_array($c)) {
                                echo "<option value='{$cat['Titre']}'>{$cat['Titre']}</option>";
                            }
                            ?>

                        </select>
                    </td>
                </tr>

                <tr>
                    <td><label> <b>Salaire : </b></label></td>
                    <td><input type="number" name="salaire" placeholder="Salaire" required></td>
                </tr>
            
            
                <tr>
                    <td><label> <b>Photo : </b></label></td>
                    <td><input type = "file" name= "photo" value = ""> </td>
                </tr>

                <tr>
                    <td><label> <b>NNI : </b></label></td>
                    <td><input type="text" name="nni_emp" placeholder="NNI" required></td>
                </tr>

                <tr>
                    <td><label> <b>Matricule : </b></label></td>
                    <td><input type="text" name="matricule_emp" placeholder="Matricule" required></td>
                </tr>

                <tr>
                    <td><label> <b>Lieu de travail :</b> </label></td>
                    <td><input type="text" name="lieu_travail" placeholder="Lieu de travail" required></td>
                </tr>

                <tr>
                    <td><label> <b>Fonction :</b> </label></td>
                    <td><input type="text" name="fonction" placeholder="Fonction" required></td>
                </tr>

                <tr>
                    <td><label><b> Supérieur hiérarchique : </b></label></td>
                    <td><input type="text" name="suphierarchie" placeholder="Supérieur hiérarchique" required></td>
                </tr>

                <tr>
                    <td><label> <b>Email du supérieur : </b></label></td>
                    <td><input type="email" name="emailsup" placeholder="Email du supérieur" required></td>
                </tr>

                <tr>
                    <td><label> <b>Date de saisie :</b> </label></td>
                    <td><input type="date" name="datesaisie" required></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center; padding-top: 15px;">
                        <input type = "submit" name= "eng" value = "Sauvegarder le personnel" class="btn-save"> 
                    </td>
                </tr>
            </table>

            <br><br>

            <!--ajout de la class "personnel-table" pour cibler que la 2ème table avec le CSS-->
            <table class="personnel-table" width= "100%" align = "center" border = "1">
                <tr style= "background-color : Orange;">
                    <th>N° :</th>
                    <th>Nom & Prenom :</th>
                    <th>Photo :</th>
                    <th>Departement :</th>
                    <th>Categorie :</th>
                    <th>Salaire :</th>
                    <th>NNI :</th>
                    <th>Matricule :</th>
                    <th>Lieu de travail :</th>
                    <th>Fonction :</th>
                    <th>Supérieur hiérarchique :</th>
                    <th>Email du supérieur :</th>
                    <th>Date de saisie :</th>
                    <th>Action :</th>
                </tr>

                <!-- Récupère les infos paginées de la table 'personnel'-->
                <?php while($l = mysqli_fetch_array($lp)){?>
                
                <tr id="row_<?php echo $l['id']; ?>">
                    <td> <?php echo $l['id']?> </td>
                    <td> <?php echo $l['nom'].' '.$l['prenom']?></td>
                    <td> <img src="../img/<?php echo $l['photo']?>" width="80" height = "80"></td>
                    <td> <?php echo $l['departement']?> </td>
                    <td> <?php echo $l['categorie_titre']?> </td>
                    <td> <?php echo $l['salaire']?> </td>
                    <td> <?php echo $l['nni_emp']?> </td>
                    <td> <?php echo $l['matricule_emp']?> </td>
                    <td> <?php echo $l['lieu_travail']?> </td>
                    <td> <?php echo $l['fonction']?> </td>
                    <td> <?php echo $l['suphierarchie']?> </td>
                    <td> <?php echo $l['emailsup']?> </td>
                    <!-- Conversion de la date de saisie au format jj/mm/aaaa -->
                    <td><?php 
                        // Conversion de la date si elle n'est pas nulle
                        if (!empty($l['datesaisie'])) {
                            $dateSaisie = new DateTime($l['datesaisie']);
                            echo $dateSaisie->format('d/m/Y'); 
                        } else {
                            echo "Non spécifiée"; // Gestion du cas où la date est vide
                        }
                    ?> </td>
                    <td> 
                        <!-- Bouton "Edit" pour modifier les infos d'un personnel -->
                        <button type="button" class="editButton" 
                            data-id="<?php echo $l['id']; ?>" 
                            data-nom="<?php echo $l['nom']; ?>" 
                            data-prenom="<?php echo $l['prenom']; ?>" 
                            data-photo="<?php echo $l['photo']; ?>" 
                            data-departement="<?php echo $l['departement']; ?>" 
                            data-categorie="<?php echo $l['categorie_titre']; ?>" 
                            data-salaire="<?php echo $l['salaire']; ?>" 
                            data-nni="<?php echo $l['nni_emp']; ?>" 
                            data-matricule="<?php echo $l['matricule_emp']; ?>" 
                            data-lieu="<?php echo $l['lieu_travail']; ?>" 
                            data-fonction="<?php echo $l['fonction']; ?>" 
                            data-suphierarchie="<?php echo $l['suphierarchie']; ?>" 
                            data-emailsup="<?php echo $l['emailsup']; ?>" 
                            data-datesaisie="<?php echo $l['datesaisie']; ?>" style="background-color: green; color: white;">Edit</button>
                            &nbsp;&nbsp;
                        <!-- Lien de suppression -->
                        <a href="javascript:void(0)" class="btn btn-delete" onclick="confirmDelete(<?php echo $l['id']; ?>)">Delete</a>
                        <!-- Bouton pour afficher la fiche d'identification -->
                        <button type="button" class="btn btn-info" onclick="showModal(<?php echo $l['id']?>)">Fiche d'identification</button>
                    </td>
                </tr>
                <?php }?>
            </table>
             <!-- Pagination -->
             <div class="pagination">
                <?php
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<a href='formulaire_personnel.php?page=".$i."'";
                    if ($i == $page) echo " class='active'";
                    echo ">".$i."</a> ";
                }
                ?>
            </div>
        </form>
        <br><br>
         <!--bouton "Return"  -->     
        <a href="Formulaires_RH.php" class="btn-blue">Return</a> 
    </fieldset>


    <!-- Modal pour l'édition d'un personnel-->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h1 class="modal-title"><u><b>Modifier l'Employé </u>:</b></h1><br>
            <form id="editForm" method="post" enctype="multipart/form-data">
                <input type="hidden" id="editId" name="id">

                <!-- Ajout du champ pour la photo et son aperçu -->
                <label for="editPhoto">Photo actuelle :</label>
                <img id="editPhotoPreview" src="" alt="Aperçu de la photo" width="80" height="80"><br><br>
                
                <label for="editPhoto">Modifier la photo :</label>
                <input type="file" id="editPhoto" name="photo"><br>

                <label for="editNom">Nom : <input type="text" id="editNom" name="nom" required></label>
                

                <label for="editPrenom">Prénom : <input type="text" id="editPrenom" name="prenom" required></label>
                

                <label for="editDepartement">Département :
                    <select id="editDepartement" name="departement" required>
                        <?php
                        $d = mysqli_query($conn, "select distinct(departement) from departement");
                        while ($dep = mysqli_fetch_array($d)) {
                            echo "<option value='{$dep['departement']}'>{$dep['departement']}</option>";
                        }
                        ?>
                    </select>
                </label>

                <label for="editCategorie">Catégorie :
                    <select id="editCategorie" name="categorie" required>
                        <?php
                        $c = mysqli_query($conn, "select Titre from categorie");
                        while ($cat = mysqli_fetch_array($c)) {
                            echo "<option value='{$cat['Titre']}'>{$cat['Titre']}</option>";
                        }
                        ?>
                    </select>
                </label>

                <label for="editSalaire">Salaire :
                    <input type="number" id="editSalaire" name="salaire" required>
                </label>
                
                <label for="editNni">NNI :
                    <input type="text" id="editNni" name="nni_emp" required>
                </label>
                
                <label for="editMatricule">Matricule :
                    <input type="text" id="editMatricule" name="matricule_emp" required>
                </label>
                
                <label for="editLieu">Lieu de travail :
                    <input type="text" id="editLieu" name="lieu_travail" required>
                </label>
                
                <label for="editFonction">Fonction :
                    <input type="text" id="editFonction" name="fonction" required>
                </label>
                
                <label for="editSuphierarchie">Supérieur hiérarchique :
                    <input type="text" id="editSuphierarchie" name="suphierarchie" required>
                </label>
                
                <label for="editEmailsup">Email du supérieur :
                    <input type="email" id="editEmailsup" name="emailsup" required>
                </label>
                
                <label for="editDatesaisie">Date de saisie :
                    <input type="date" id="editDatesaisie" name="datesaisie" required><br><br>
                </label>
                
                <button type="submit" style="background-color: green; color: white; padding: 10px 20px; font-size: 16px; *
                        border: none; border-radius: 5px; cursor: pointer; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" 
                        name="updatePersonnel">
                    Sauvegarder les modifications
                </button>

            </form>
        </div>
    </div>

    <!-- Modal pour afficher les détails du personnel -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>


    <script>
//------------------------------------------------------------------------------------------------------

    // Fonction JavaScript pour afficher les alertes dynamiquement pour "l'Ajout" et la "Modification" 
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
            showAlert('success', 'Ajouté', 'Le personnel a été ajouté avec succès.');
        } else if (status === 'success' && type === 'update') {
            showAlert('success', 'Mise à jour réussie', 'Les informations ont été mises à jour avec succès.');
        } else if (status === 'error' && type === 'add') {
            showAlert('error', 'Erreur lors de l\'ajout', message);
        } else if (status === 'error' && type === 'update') {
            showAlert('error', 'Erreur lors de la mise à jour', message);
        }
    };
//------------------------------------------------------------------------------------------------------
        //fonction qui affiche la fiche d'un employé "Fiche d'identification". 
        function showModal(id) {
            var modal = document.getElementById("myModal");
            var modalBody = document.getElementById("modal-body");

            //requête AJAX pour récupérer les détails du personnel
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "../modif_supp_form_personnel/get_personnel_details.php?id=" + id, true);
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
//----------------------------------------------------------------------------------------
        //Code JS pour la gestion de la modal du bouton "Edit"
        document.querySelectorAll('.editButton').forEach(button => {
            button.addEventListener('click', function () {
                // Récupérer les données de l'employé à partir des attributs data-*
                const id = this.getAttribute('data-id');
                const nom = this.getAttribute('data-nom');
                const prenom = this.getAttribute('data-prenom');
                const photo = this.getAttribute('data-photo');
                const departement = this.getAttribute('data-departement');
                const categorie = this.getAttribute('data-categorie');
                const salaire = this.getAttribute('data-salaire');
                const nni = this.getAttribute('data-nni');
                const matricule = this.getAttribute('data-matricule');
                const lieu = this.getAttribute('data-lieu');
                const fonction = this.getAttribute('data-fonction');
                const sup = this.getAttribute('data-suphierarchie');
                const emailSup = this.getAttribute('data-emailsup');
                const dateSaisie = this.getAttribute('data-datesaisie');

                // Remplir les champs de la modal avec les valeurs récupérées
                document.getElementById('editId').value = id;
                document.getElementById('editNom').value = nom;
                document.getElementById('editPrenom').value = prenom;
                document.getElementById('editDepartement').value = departement;
                document.getElementById('editCategorie').value = categorie;
                document.getElementById('editSalaire').value = salaire;
                document.getElementById('editNni').value = nni;
                document.getElementById('editMatricule').value = matricule;
                document.getElementById('editLieu').value = lieu;
                document.getElementById('editFonction').value = fonction;
                document.getElementById('editSuphierarchie').value = sup;
                document.getElementById('editEmailsup').value = emailSup;
                document.getElementById('editDatesaisie').value = dateSaisie;
                

                // Afficher la photo actuelle
                const photoPreview = document.getElementById('editPhotoPreview');
                if (photoPreview) {
                    photoPreview.src = "../img/" + photo;
                }
                // Afficher la modal
                document.getElementById('editModal').style.display = 'block';
            });
        });

        // Fermer la modal si l'utilisateur clique sur la croix
        document.querySelector('.close').addEventListener('click', function () {
            document.getElementById('editModal').style.display = 'none';
        });

//---------------------------------------------------------------------------------------------------
//fonction de Suppression et confirmation de la Suppression.
        function confirmDelete(id) {
            Swal.fire({
                title: 'Êtes-vous sûr?',
                text: "Vous ne pourrez pas revenir en arrière !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Appel AJAX pour supprimer la personne
                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", "../modif_supp_form_personnel/delete_form_personnel.php?id=" + id, true);
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
                                text: 'La personne a été supprimée avec succès.',
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

    </script>
</body>
</html>


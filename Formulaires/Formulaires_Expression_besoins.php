<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

include '../serv_projet1.php';

//fonction qui valide le type et la taille des fichiers valides.
function validate_file($file) {
    $allowed_types = ['image/jpeg', 'image/png', 'application/pdf']; // Types de fichiers autorisés
    $max_size = 2 * 1024 * 1024; // Taille maximale de 2 Mo

    if ($file['error'] === UPLOAD_ERR_OK) {
        $file_type = mime_content_type($file['tmp_name']);
        if (in_array($file_type, $allowed_types) && $file['size'] <= $max_size) {
            return true;
        }
    }
    return false;
}

// Initialiser ou récupérer le commande_id
if (!isset($_SESSION['commande_id'])) {
    // Rechercher le dernier ID de commande et définir le prochain ID
    $sql = "SELECT MAX(id) AS last_id FROM commandes WHERE status = 'ouvert'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $_SESSION['commande_id'] = $row['last_id'] ?? 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_element'])) {
        // Vérifier si la commande en cours est définie, sinon en créer une nouvelle
        if (!isset($_SESSION['commande_id']) || $_SESSION['commande_id'] === 0) {
            
            // Récupérer la date du formulaire
            $date_commande = mysqli_real_escape_string($conn, $_POST['date']);
            
            // Insérer une nouvelle commande avec la date
            $sql_create_commande = "INSERT INTO commandes (status, date_commande) VALUES ('ouvert', '$date_commande')";
            if (mysqli_query($conn, $sql_create_commande)) {
                $_SESSION['commande_id'] = mysqli_insert_id($conn);
            } else {
                echo "Erreur: " . $sql_create_commande . "<br>" . mysqli_error($conn);
            }
        }

        // Enregistrer l'objet unique pour les désignations suivantes
        if (!isset($_SESSION['objet_unique'])) {
            $_SESSION['objet_unique'] = mysqli_real_escape_string($conn, $_POST['objet']);//éviter les injections sql.

        }
        // Enregistrer une unique destination pour la commande.
        if (!isset($_SESSION['destination_unique'])) {
            $_SESSION['destination_unique'] = mysqli_real_escape_string($conn, $_POST['destination']);
        }

        // Récupérer les données du formulaire
        $date_saisie = date("Y-m-d H:i:s");
        $désignation = mysqli_real_escape_string($conn, $_POST['désignation']);
        $objet = $_SESSION['objet_unique'];;
        $nombres_articles = intval($_POST['nombres_articles']);
        $urgence =  mysqli_real_escape_string($conn, $_POST['urgence']);
        $type_services = mysqli_real_escape_string($conn, $_POST['type_services']);
        $destination = $_SESSION['destination_unique'];;
        $commande_id = $_SESSION['commande_id'];

        // Gestion des fichiers - Pièce
        if (isset($_FILES['piece']) && validate_file($_FILES['piece'])) {
            $piece = basename($_FILES['piece']['name']);
            $tpiece = $_FILES['piece']['tmp_name'];
            move_uploaded_file($tpiece, "../img/$piece");
        } else {
            $piece = null;
        }
        
        // Gestion des fichiers - Image
        if (isset($_FILES['image']) && validate_file($_FILES['image'])) {
            $image = basename($_FILES['image']['name']);
            $timage = $_FILES['image']['tmp_name'];
            move_uploaded_file($timage, "../img/$image");
        } else {
            $image = null;
        }
        
        // Insertion dans la base de données
        $sql = "INSERT INTO expression_besoins (date_saisie, désignation, objet, piece, image, nombres_articles, urgence, type_services, commande_id,destination) 
                VALUES ('$date_saisie', '$désignation', '$objet', '$piece', '$image', '$nombres_articles', '$urgence', '$type_services', '$commande_id', '$destination')";

        if (mysqli_query($conn, $sql)) {
            $success_message = 'L\'élément a été ajouté avec succès à la commande.';
        } else {
            $error_message = "Erreur: " . $sql . "<br>" . mysqli_error($conn);
        }
            
        
    } elseif (isset($_POST['close_commande'])) {
        if ($_SESSION['commande_id'] !== null) {
            // Fermer la commande en mettant à jour le statut
            $commande_id = $_SESSION['commande_id'];
            $sql = "UPDATE commandes SET status='fermé' WHERE id='$commande_id'";
            if (mysqli_query($conn, $sql)) {
                $success_message = 'Commande fermée avec succès.';
                // Réinitialiser le commande_id , l'objet ainsi que la destination pour la prochaine commande
                $_SESSION['commande_id'] = 0;
                unset($_SESSION['objet_unique']);
                unset($_SESSION['destination_unique']);
            } else {
                $error_message = "Erreur: " . $sql . "<br>" . mysqli_error($conn);
            }
        } else {
            $error_message = "Aucune commande à fermer.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expression des Besoins</title>
    <link rel="stylesheet" href="../css/styles_Formulaires_expression_besoins.css">
    
    <script src="../js/toaster.js"></script><!--Script qui traite les Toaster -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">


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
        <legend>Expression des Besoins</legend>
           <!-- Affichage des messages de succès ou d'erreur -->
            <div id="toast-container" class="toast-container"></div>

            <!-- Affichage des messages "Succes" et "Error" grâce à l'appel de ShowToast() -->
            <?php if (isset($success_message)): ?>
                <script>showToast("<?php echo $success_message; ?>", "success");</script>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <script>showToast("<?php echo $error_message; ?>", "error");</script>
            <?php endif; ?>
            <!-- la barre de recherche 
            <div style="text-align: right;">
                <form method="post" action="">
                    <input type="text" id="search" name="search" placeholder="Rechercher une expression de besoin..." value=" echo htmlspecialchars($search); ?>">
                    <input type="submit" value="Rechercher">
                </form>
            </div>-->

            <!-- Formulaire pour ajouter une nouvelle expression de besoin -->
            <form method="post" action="" enctype="multipart/form-data">      
                <table width= "50%" align = "center">
                    <tr>
                        <td><label><b>Date</b> : </label></td>
                        <td><input type="text" name="date" value="<?php echo date('Y-m-d H:i:s'); ?>" readonly></td>
                    </tr>
                    <tr>
                        <td><label><b> Destination </b>: </label></td>
                        <td>
                            <select id="destination" name="destination" <?php echo isset($_SESSION['destination_unique']) ? 'disabled' : ''; ?>>
                                    <option value="Nouakchott" <?php echo (isset($_SESSION['destination_unique']) && $_SESSION['destination_unique'] == 'Nouakchott') ? 'selected' : ''; ?>>Nouakchott</option>
                                    <option value="Chami" <?php echo (isset($_SESSION['destination_unique']) && $_SESSION['destination_unique'] == 'Chami') ? 'selected' : ''; ?>>Chami</option>
                                    <option value="M1" <?php echo (isset($_SESSION['destination_unique']) && $_SESSION['destination_unique'] == 'M1') ? 'selected' : ''; ?>>M1</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label> <b>Objet</b> : </label></td>
                        <td>
                            <input type="text" name="objet" value="<?php echo isset($_SESSION['objet_unique']) ? $_SESSION['objet_unique'] : ''; ?>" <?php echo isset($_SESSION['objet_unique']) ? 'readonly' : ''; ?> required>
                        </td>
                    </tr>

                    <tr>
                        <td><label> <b>Désignation</b> : </label></td>
                        <td><input type = "text" name= "désignation" placeholder="désignation" required></td>
                    </tr>

                    <tr>
                        <td><label> <b>Pièce</b> : </label></td>
                        <td><input type = "file" name= "piece" placeholder="pièce"></td>
                    </tr>
                    <tr>
                        <td><label> <b>Image</b> : </label></td>
                        <td><input type = "file" name= "image" placeholder="image" required></td>
                    </tr>
                    <tr>
                        <td><label> <b>Nombre d'Articles</b> : </label></td>
                        <td><input type = "number" name= "nombres_articles" placeholder="nombre d'articles" required></td>
                    </tr>
                    <tr>
                        <td><label> <b>Urgence</b> :</label></td>
                        <td>
                            <select id="urgence" name="urgence" >
                                    <option value="Élevée">Élevée</option>
                                    <option value="Moyenne">Moyenne</option>
                                    <option value="Basse">Basse</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><label for="type_services"><b>Type de Services</b> : </td></label> 
                        <td>
                            <select id="type_services" name="type_services" >
                                <option value="Achat de fournitures">Achat de fournitures</option>
                                <option value="Prestation de service">Prestation de service</option>
                                <option value="Achat pour réparation">Achat pour réparation</option>
                                <option value="Achat de matériel">Achat de matériel</option>
                            </select>
                        </td> 
                    </tr>

                    <tr>
                        <td colspan="2" style="text-align: center; padding-top: 15px;">
                        <input type = "submit" name= "add_element" value = "Ajouter l'élement"> </td>

                    </tr>
                </table>

                <!--Le bouton 'Liste des besoins'-->
                <div class="button-container">
                    <a href="../modif_details_commande/liste_besoins.php" class="btn-yellow">Liste des besoins</a>
                </div>

                <!-- Tableau affichant les éléments ajoutés -->
                <table class="exp_b-table" width="100%" align="center" border="1">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Destination</th>
                            <th>Désignation</th>
                            <th>Objet</th>
                            <th>Nombre d'articles</th>
                            <th>Urgence</th>
                            <th>Type de services</th>
                            <th>Pièce</th>
                            <th>Image</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $sql = "SELECT * FROM expression_besoins WHERE commande_id = " . $_SESSION['commande_id'];
                            $result = mysqli_query($conn, $sql);
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['date_saisie'] . "</td>";
                                    echo "<td>" . $row['destination'] . "</td>";
                                    echo "<td>" . $row['désignation'] . "</td>";
                                    echo "<td>" . $row['objet'] . "</td>";
                                    echo "<td>" . $row['nombres_articles'] . "</td>";
                                    echo "<td>" . $row['urgence'] . "</td>";
                                    echo "<td>" . $row['type_services'] . "</td>";
                                    echo "<td><a href='../img/" . $row['piece'] . "'>" . $row['piece'] . "</a></td>";
                                    echo "<td><a href='../img/" . $row['image'] . "'>" . $row['image'] . "</a></td>";
                                    echo "</tr>";
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </form>
            <!-- Formulaire séparé pour fermer la commande -->
            <form method="post" action="">
                <table width="50%" align="center">
                    <tr>
                        <td colspan="2" style="text-align: center; padding-top: 15px;">
                            <input type="submit" name="close_commande" value="Fermer la commande">
                        </td>
                    </tr>
                </table>
            </form>
    </fieldset>
    <a href="../Formulaires/Formulaires_MG.php" class="btn-blue">Return</a>
</body>
</html>

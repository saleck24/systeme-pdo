<?php
session_start();
require_once("../serv_projet1.php");

$selected_demande = null; // Variable pour stocker la demande sélectionnée


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process'])) {
    // Récupérer l'ID de la demande sélectionnée
    $demande_id = $_POST['demande_avance_id'];

    // Requête pour récupérer les détails de la demande d'avance, y compris le nom et prénom du personnel
    $query_select = "
        SELECT a.*, CONCAT(p.nom, ' ', p.prenom) AS nom_complet,YEAR(a.date) AS annee
        FROM demande_avance a 
        JOIN personnel p ON a.personnel_id = p.id 
        WHERE a.id = '$demande_id'
    ";
    $result_select = mysqli_query($conn, $query_select);
    $demande = mysqli_fetch_assoc($result_select);

    if ($demande) {
        // Date de traitement de la demande
        $date_traitement = date('Y-m-d H:i:s'); // Date actuelle
        $mois_traitement = date('m'); // Mois actuel
        $annee_traitement = date('Y'); // Année actuelle
        
        // Insérer la demande dans la table 'avance_sur_salaire'
        $personnel_id = $demande['personnel_id'];
        
        // Générer le codeavance pour éviter les doublons
        $codeavance = $demande_id . '/' . $mois_traitement . '/' . $annee_traitement;

        // Vérifier s'il n'y a pas de doublon
        $query_check = "SELECT COUNT(*) AS count FROM avance_sur_salaire WHERE codeavance = '$codeavance'";
        $result_check = mysqli_query($conn, $query_check);
        $row_check = mysqli_fetch_assoc($result_check);

        if ($row_check['count'] == 0) { // Si Aucun doublon est trouvé
            // Insérer la demande dans la table 'avance_sur_salaire'
            $query_insert = "INSERT INTO avance_sur_salaire (id_demande_avance, date, mois, annee, codeavance) 
                             VALUES ('$demande_id', '$date_traitement', '$mois_traitement', '$annee_traitement', '$codeavance')";
           
            // Si l'insertion dans 'avance_sur_salaire' est réussie, marquer la demande comme traitée
            if (mysqli_query($conn, $query_insert)) {
                // Requête pour marquer la demande comme traitée
                $query_update = "UPDATE demande_avance SET traitée = 1 WHERE id = '$demande_id'";
                mysqli_query($conn, $query_update);

                // Stocker la demande sélectionnée pour l'afficher après traitement.
                $selected_demande = $demande;
            } else {
                echo "Erreur lors de l'insertion de la demande dans 'avance_sur_salaire'.";
            }
            
        } else {
            echo "Erreur : Cette demande a déjà été traitée.";
        }

    }
}

// Récupérer les demandes validées par le DAF
$query = "SELECT a.id, CONCAT(p.nom, ' ', p.prenom) AS nom_complet, a.montant, a.date , a.mois
          FROM demande_avance a 
          JOIN personnel p ON a.personnel_id = p.id 
          WHERE a.accord_DAF = 1 AND a.traitée = 0";

$result = mysqli_query($conn, $query);


// Récupérer les mois et années distincts
$query_mois = "SELECT DISTINCT mois FROM demande_avance";
$result_mois = mysqli_query($conn, $query_mois);

$query_annee = "SELECT DISTINCT YEAR(date) AS annee FROM demande_avance";
$result_annee = mysqli_query($conn, $query_annee);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avance sur Salaire</title>
    <link rel="stylesheet" href="../css/styles_avance_salaire.css">

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
<body><br>
    
    <h1><strong><u>Liste Des Avances Sur Les Salaires</u> :</strong></h1><br>

    <h2 style="text-align: center;">Visualiser la situation par période</h2><br><br>
    

     <!-- Formulaire pour choisir le mois et l'année -->
     <form method="get" action="Releve_avance_par_periode.php">
        <table width="50%" align="center">
            <tr>
                <td><label><b>Choisir le mois:</b></label></td>
                <td>
                    <select name="mois" required>
                        <option value="">Sélectionner un mois</option>
                        <?php while ($row_mois = mysqli_fetch_array($result_mois)): ?>
                            <option value="<?php echo $row_mois['mois']; ?>">
                                <?php echo mois_en_lettres(intval($row_mois['mois'])); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><b>Choisir l'année:</b></label></td>
                <td>
                    <select name="annee" required>
                        <option value="">Sélectionner une année</option>
                        <?php while ($row_annee = mysqli_fetch_array($result_annee)): ?>
                            <option value="<?php echo $row_annee['annee']; ?>">
                                <?php echo $row_annee['annee']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; padding-top: 15px;">
                    <input type="submit" value="Relevé par Période">
                </td>
            </tr>
        </table>
    </form>
    <fieldset> 
        <legend>Avance sur Salaire</legend>

        <!-- Formulaire pour afficher les demandes validées par le DAF -->
        <form method="post" action="">
            <table width="50%" align="center">
                <tr>
                    <td><label> <b>Demandes d'avance validées :</b>  </label></td>
                    <td>
                        <select name="demande_avance_id" required>
                            <option value="">Sélectionner une demande</option>
                            <?php 
                            while ($row = mysqli_fetch_array($result)) {
                            ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo $row['nom_complet'] . " - " . $row['montant'] . " MRU - " . date('d/m/Y', strtotime($row['date'])); ?>
                            </option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="text-align: center; padding-top: 15px;">
                        <input type="submit" name="process" value="Traiter la Demande">
                    </td>
                </tr>
            </table>
        </form>
        <!-- Table pour afficher les demandes après le traitement -->
        <?php if ($selected_demande): ?>
            <h2>Demande traitée :</h2>
            <table class="center-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom & Prénom</th>
                        <th>Montant Avancé</th>
                        <th>Date</th>
                        <th>Période</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $selected_demande['id']; ?></td>
                        <td><?php echo $selected_demande['nom_complet']; ?></td>
                        <td><?php echo $selected_demande['montant']; ?> MRU</td>
                        <td><?php echo date('d/m/Y', strtotime($selected_demande['date'])); ?></td>
                        <td><?php echo mois_en_lettres(intval($selected_demande['mois'])) . ' ' . $selected_demande['annee']; ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </fieldset>
</body>
    <div class="button-container">
        <a href="../Formulaires/Formulaires_finance.php" class="btn-blue">Return</a>
    </div>

    <?php
    // Fonction pour convertir un numéro de mois en lettres
    function mois_en_lettres($numero_mois) {
        $mois = [
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre'
        ];
        return $mois[$numero_mois] ?? 'Mois invalide'; // Retourne le mois en toutes lettres ou un message d'erreur
    }?>
</html>

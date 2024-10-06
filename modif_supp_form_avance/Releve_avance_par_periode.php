<?php
session_start();
require_once("../serv_projet1.php");

// Fonction pour convertir le mois en lettres
function mois_en_lettres($mois) {
    $mois_let = [
        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
    ];
    return $mois_let[$mois];
}

//fonction pour convertir les nombres en lettres
function nombre_en_lettres($nombre) {
    $f = new NumberFormatter("fr", NumberFormatter::SPELLOUT);
    return $f->format($nombre);
}

// Vérifier si le mois et l'année sont passés en paramètres
if (isset($_GET['mois']) && isset($_GET['annee'])) {
    $mois = mysqli_real_escape_string($conn, $_GET['mois']);
    $annee = mysqli_real_escape_string($conn, $_GET['annee']);
    
    // Requête pour récupérer les avances sur salaire pour la période spécifiée
    $query = "
        SELECT a.id_demande_avance, a.date, a.codeavance, d.montant, 
               CONCAT(p.nom, ' ', p.prenom) AS nom_complet, p.fonction, 
               p.photo,p.matricule_emp
        FROM avance_sur_salaire AS a
        JOIN demande_avance AS d ON a.id_demande_avance = d.id
        JOIN personnel AS p ON d.personnel_id = p.id

    ";
    $result = mysqli_query($conn, $query);
} else {
    // Redirection en cas d'absence de mois ou d'année
    header('Location: avance_salaire.php');
    exit();
}

//Récupérer la somme des avances sur salaires.
$query_total = "SELECT SUM(a.montant) AS total_avances 
                FROM demande_avance a 
                JOIN personnel p ON a.personnel_id = p.id 
                WHERE a.accord_DAF = 1 AND a.traitée = 1";
$result_total = mysqli_query($conn, $query_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_avances = $row_total['total_avances'];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relevé des Avances par Période</title>
    <link rel="stylesheet" href="../css/styles_avance_salaire.css">
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


    <h1 class="encadre">Relevé des Avances sur Salaire par Période :  <?php echo mois_en_lettres(intval($mois)) . ' ' . $annee; ?></h1>




    <!-- Table pour afficher les avances sur salaire -->
    <table class="center-table">
        <thead>
            <tr>
                <th>Matricule</th>
                <th>Nom & Prénom</th>
                <th>Fonction</th>
                <th>Total Avancé</th>
                <th>Période</th>
                <th>Photo</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['matricule_emp']); ?></td>
                    <td><?php echo htmlspecialchars($row['nom_complet']); ?></td>
                    <td><?php echo htmlspecialchars($row['fonction']); ?></td>
                    <td><?php echo htmlspecialchars($row['montant']); ?> MRU</td>
                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['date']))); ?></td>
                    <td>
                        <?php if (!empty($row['photo'])): ?>
                            <img src="../img/<?php echo htmlspecialchars($row['photo']); ?>" alt="Photo du Personnel" width="50" height="50">
                        <?php else: ?>
                            Pas de photo
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php
        // Si la somme des avances est null, on considère que le total est 0.
        if ($total_avances === null) {
            $total_avances = 0;
        }
    ?>

   <!-- Afficher le "TOTAL" des avances sur salaires -->
    <h3 style="text-align:center;">TOTAL : <?php echo number_format($total_avances, 2) . ' MRU (' . nombre_en_lettres($total_avances) . ')'; ?></h3>


    <!-- Bouton Imprimer -->
    <button onclick="window.print()" class="btn-print">Imprimer</button>

    <div class="button-container">
        <a href="../modif_supp_form_avance/avance_salaire.php" class="btn-blue">Return</a>
    </div>   
</body>
</html>

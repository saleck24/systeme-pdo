<?php
require_once("../serv_projet1.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = mysqli_query($conn, "SELECT a.*, CONCAT(p.nom, ' ', p.prenom) AS nom_complet, p.fonction FROM demande_avance a
                                   JOIN personnel p ON a.personnel_id = p.id
                                   WHERE a.id = $id");
    if ($row = mysqli_fetch_assoc($result)) {
        // Requête pour obtenir le nom et prénom du Responsable DRH
        $result_responsable = mysqli_query($conn, "SELECT CONCAT(nom, ' ', prenom) AS nom_complet FROM personnel WHERE fonction = 'Responsable DRH' LIMIT 1");
        $responsable = mysqli_fetch_assoc($result_responsable);
        $nom_responsable = $responsable ? $responsable['nom_complet'] : 'Nom non trouvé';

        // Requête pour obtenir le nom et prénom du DAF si accord_DAF est égal à 1
        $nom_daf = '';
        if ($row['accord_DAF'] == 1) {
            $result_daf = mysqli_query($conn, "SELECT CONCAT(nom, ' ', prenom) AS nom_complet FROM personnel WHERE fonction = ' Directeur Administrative et financier' LIMIT 1");
            $daf = mysqli_fetch_assoc($result_daf);
            $nom_daf = $daf ? $daf['nom_complet'] : 'Nom DAF non trouvé';
        }

       // Formater la date avec DateTime
       $date = new DateTime($row['date']);
       $date_formatee = '<strong><u>' . $date->format('d/m/Y') . '</u></strong>';
       
       // Convertir le mois en lettre
       $numero_mois = intval($row['mois']); // Récupère le numéro du mois
       $mois_lettre = mois_en_lettres($numero_mois); // Convertit le numéro du mois en lettres


        echo '<div style="text-align: center;"><h2>DEMANDE D\'AVANCE SUR SALAIRE</h2></div>';
        echo '<div style="text-align: right;"><p>adressée au : <u>RESPONSABLE RH</u></p></div>';
        echo '<div style="text-align: right;"><p>Le : ' . $date_formatee . '</p></div>';
        echo '<div style="text-align: center;"><p><u><strong>N° : ' . $row['id'] . '</strong></u></p></div>';
        echo '<p><strong><u>Objet:</u></strong> DEMANDE D\'AVANCE SUR SALAIRE</p>';
        echo '<p>Madame, Monsieur,</p>';
        echo '<p>Je soussigné(e) :<strong> ' . $row['nom_complet'] . '</strong></p>';
        echo '<p>Poste de travail : <strong>' . $row['fonction'] . '</strong></p>';
        echo '<p>Souhaiterais bénéficier d\'une avance de :<strong> ' . $row['montant'] . ' MRU</strong> (' . convertir_en_lettres($row['montant']) . ') sur mon salaire du mois de <strong>' . $mois_lettre . '</strong></p>';
        echo '<p>Dans l\'attente de votre réponse, je vous prie d\'agréer, Madame, Monsieur, l\'expression de mes salutations distinguées.</p>';
        
        // Alignement horizontal avec flexbox
        echo '<div style="display: flex; justify-content: space-between; margin-top: 20px;">';
        echo '<div style="text-align: left;"><p><u>RESPONSABLE DRH:</u></p><p><strong>' . $nom_responsable . '</strong></p></div>';
        echo '<div style="text-align: center;"><p><u>BENEFICIAIRE:</u></p><p><strong>' . $row['nom_complet'] . '</strong></p></div>';
        echo '<div style="text-align: right;"><p><u>DAF:</u></p>';
        if ($row['accord_DAF'] == 1) {
            echo '<p><strong>' . $nom_daf . '</strong></p>';
        }
        echo '</div>';
        echo '</div>';

        // Ajouter "SIGNATURE :" en dessous du nom du bénéficiaire
        echo '<div style="text-align: center; margin-top: 20px;"><p><u>SIGNATURE :</u></p></div>';

        // Bouton d'impression
        echo '<div style="text-align: center; margin-top: 20px;">';
        echo '<button onclick="window.print()" style="background-color: blue; color: white; padding: 10px; border: none; cursor: pointer;">Imprimer</button>';
        echo '</div>';
    } else {
        echo "Aucun détail trouvé.";
    }
} else {
    echo "ID non spécifié.";
}

// Fonction pour convertir le montant en lettres en utilisant NumberFormatter
function convertir_en_lettres($montant) {
    // Vérifier si l'extension intl est disponible
    if (!class_exists("NumberFormatter")) {
        return "L'extension intl n'est pas disponible.";
    }

    // Créer une instance de NumberFormatter pour le français
    $fmt = new NumberFormatter("fr_FR", NumberFormatter::SPELLOUT);

    // Convertir le nombre en lettres
    return ucfirst($fmt->format($montant));
}

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
}
?>

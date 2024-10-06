<?php
include 'serv_projet1.php'; // Inclure le fichier de connexion à la base de données


//Fonction pour l'archivage des commandes qui sont validées et dont le prix_total > 0 au bout de 10min
function archiverCommandes() {
    global $conn;

    // Archiver les commandes validées et les déplacer vers la table archives après 10min
    $sql_archive = "INSERT INTO archives (commande_id, date_saisie, adjoint_mg_valide, admin_valide, daf_valide, prix_total)
                    SELECT id, date_commande, adjoint_mg_valide, admin_valide, daf_valide, prix_total
                    FROM commandes
                    WHERE adjoint_mg_valide = 1 AND admin_valide = 1 AND daf_valide = 1 AND prix_total > 0 AND status ='fermé'
                    AND TIMESTAMPDIFF(MINUTE, date_commande, NOW()) >= 10
                    ON DUPLICATE KEY UPDATE date_archive = NOW()";

    $result_archive = mysqli_query($conn, $sql_archive);

    // Supprimer les commandes archivées après 10min
   /* $sql_delete = "DELETE FROM commandes WHERE adjoint_mg_valide = 1 AND admin_valide = 1 AND daf_valide = 1
                   AND TIMESTAMPDIFF(MINUTE, date_commande, NOW()) >= 10";

    $result_delete = mysqli_query($conn, $sql_delete);*/
}

//Fonction pour déplacer les commandes non validées au bout de 10min vers les instances.
function deplacerCommandesVersInstances() {
    global $conn;

    // Déplacer les commandes non validées après 10 minutes vers la table instances
    $sql_deplacer = "INSERT INTO instances (commande_id, date_saisie, adjoint_mg_valide, admin_valide, daf_valide, prix_total)
                      SELECT id, date_commande, adjoint_mg_valide, admin_valide, daf_valide, prix_total
                      FROM commandes
                      WHERE TIMESTAMPDIFF(MINUTE, date_commande, NOW()) >= 10
                      AND (adjoint_mg_valide = 0 OR admin_valide = 0 OR daf_valide = 0)
                      /*AND status != 'fermé'*/
                      ON DUPLICATE KEY UPDATE date_instance = NOW()";

    $result_deplacer = mysqli_query($conn, $sql_deplacer);

    // Supprimer les commandes déplacées
    /*$sql_delete = "DELETE FROM commandes WHERE TIMESTAMPDIFF(MINUTE, date_commande, NOW()) >= 10
                   AND (adjoint_mg_valide = 0 OR admin_valide = 0 OR daf_valide = 0)";

    $result_delete = mysqli_query($conn, $sql_delete);*/
}

// Exécuter les fonctions
archiverCommandes();
deplacerCommandesVersInstances();


?>

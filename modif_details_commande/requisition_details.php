<?php
include '../serv_projet1.php';

if (isset($_GET['commande_id'])) {
    $commande_id = $_GET['commande_id'];

    $sql = "SELECT désignation, nombres_articles, prix_unitaire, (nombres_articles * prix_unitaire) AS total 
            FROM expression_besoins WHERE commande_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $commande_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $total_general = 0;

    echo "<table border='1'>";
    echo "<tr><th>Designation</th><th>Quantité</th><th>Prix Unitaire</th><th>Total</th></tr>";

    while ($row = $result->fetch_assoc()) {
        $total_general += $row['total'];  
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['désignation']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nombres_articles']) . "</td>";
        echo "<td>" . htmlspecialchars($row['prix_unitaire']) . "</td>";
        echo "<td>" . htmlspecialchars($row['total']) . " MRU</td>";
        echo "</tr>";
    }

    echo "</table>";

     // Convertir le total général en lettres
     function convertirNombreEnLettres($nombre) {
        $f = new NumberFormatter("fr", NumberFormatter::SPELLOUT);
        return ucfirst($f->format($nombre));
    }

    $total_en_lettres = convertirNombreEnLettres($total_general);

    // Afficher le total général
    echo "<h4>Total Général : " . htmlspecialchars($total_general) . " MRU</h4>";
    echo "<h4>En lettres : " . htmlspecialchars($total_en_lettres) . " MRU</h4>";

    // Requête pour récupérer le Directeur Administratif (role = 21)
    $sql_directeur_admin = "SELECT nom, prenom FROM users WHERE role = 21 LIMIT 1";
    $result_directeur_admin = mysqli_query($conn, $sql_directeur_admin);
    $directeur_admin = mysqli_fetch_assoc($result_directeur_admin);

    // Requête pour récupérer le Directeur Administratif et Financier (role = 4)
    $sql_directeur_financier = "SELECT nom, prenom FROM users WHERE role = 4 LIMIT 1";
    $result_directeur_financier = mysqli_query($conn, $sql_directeur_financier);
    $directeur_financier = mysqli_fetch_assoc($result_directeur_financier);
 


    // Afficher les informations supplémentaires
    echo "<table border='0'>";
    echo "<tr>";
    echo "<td style='font-weight:bold; text-decoration:underline;'>Moyen Généraux :</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td style='font-weight:bold; text-decoration:underline;'>Directeur Administratif :</td>";
     echo "<td>" . htmlspecialchars($directeur_admin['nom']) . " " . htmlspecialchars($directeur_admin['prenom']) . "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td style='font-weight:bold; text-decoration:underline;'>Directeur Administratif et Financier :</td>";
    echo "<td>" . htmlspecialchars($directeur_financier['nom']) . " " . htmlspecialchars($directeur_financier['prenom']) . "</td>";
    echo "</tr>";

    // Ajouter la date du jour et l'heure
    date_default_timezone_set('Africa/Nouakchott'); // Définit le fuseau horaire
    $date_heure = date("d/m/Y H:i");
    echo "<tr>";
    echo "<td colspan='2'>Date : " . htmlspecialchars($date_heure) . "</td>";
    echo "</tr>";
    echo "</table>";
 

    $stmt->close();

    // Mettre à jour le champ 'prix_total' dans la table 'commandes'
    $update_sql = "UPDATE commandes SET prix_total = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("di", $total_general, $commande_id);

    if ($update_stmt->execute()) {
        echo "<script>toastr.success('Le champ \"prix_total\" de la commande a été mis à jour avec succès.');</script>";
    } else {
        echo "<script>toastr.error('Erreur lors de la mise à jour du champ \"prix_total\" : " . $conn->error . "');</script>";
    }

    $update_stmt->close();
}

mysqli_close($conn);
?>

<!-- Inclusion de Toastr CSS et JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


<!-- Script pour afficher les messages de toasts -->
<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
</script>

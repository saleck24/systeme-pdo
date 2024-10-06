<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

include '../serv_projet1.php';

$commande_id = $_GET['commande_id'];

// Requête pour récupérer les informations de la commande et l'utilisateur
$sql = "SELECT eb.*, u.nom, u.prenom, eb.destination 
        FROM expression_besoins eb
        JOIN users u ON eb.user_id = u.role
        WHERE eb.commande_id = $commande_id";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    $commande = mysqli_fetch_assoc($result);
} else {
    echo "Aucune commande trouvée avec l'ID $commande_id";
    exit();
}

// Requête pour récupérer l'utilisateur ayant le rôle 2 (Adjoint Directeur des Moyens Généraux)
$sql_role2 = "SELECT nom, prenom FROM users WHERE role = 2 LIMIT 1";
$result_role2 = mysqli_query($conn, $sql_role2);
if (mysqli_num_rows($result_role2) > 0) {
    $adjoint = mysqli_fetch_assoc($result_role2);
} else {
    echo "Adjoint Directeur des Moyens Généraux non trouvé";
    exit();
}

?>

<div id="commande-content">
    <p style="text-align:left;">Date de Saisie: <?php echo $commande['date_saisie']; ?></p>
    <p style="text-align:center;">Objet: <?php echo $commande['objet']; ?></p>
    <p style="text-align:left;">N Commande: <?php echo $commande['commande_id']; ?></p>

    <h2>Détails de la Commande</h2>
    <table border="1">
        <tr>
            <th>Désignation</th>
            <th>Quantité</th>
            <th>Urgence</th>
            <th>Type de Services</th>
            <th>Image</th>
        </tr>
        

        <?php
        mysqli_data_seek($result, 0);
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['désignation'] . "</td>";
                echo "<td>" . $row['nombres_articles'] . "</td>";
                echo "<td>" . $row['urgence'] . "</td>";
                echo "<td>" . $row['type_services'] . "</td>";
                echo "<td><img src='../img/" . $row['image'] . "' alt='Image' style='max-width:100px; max-height:100px;'></td>";    
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>Aucune saisie trouvée pour cette commande</td></tr>";
        }
        ?>
        <!-- Les cellules de la signature et la destination -->
        <tr>
            <td><strong><u>Moyen Généraux :</u></strong><br><strong><?php echo $commande['nom'] . ' ' . $commande['prenom']; ?></strong> </td><br>
            <td><strong><u>Destination :</u><br> <strong><?php echo $commande['destination']; ?></strong></strong></td>
            <td colspan="3"><strong><u>Adjoint Directeur Moyen Généraux :</u></strong><br><strong><?php echo $adjoint['nom'] . ' ' . $adjoint['prenom'];?></strong> </td>
        </tr>
    </table>
</div>

<?php
mysqli_close($conn);
?>

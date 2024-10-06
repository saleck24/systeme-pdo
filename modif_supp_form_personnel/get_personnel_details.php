<?php
require_once("../serv_projet1.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = mysqli_query($conn, "SELECT p.*, d.departement, c.Titre AS categorie_titre FROM personnel p 
        LEFT JOIN departement d ON p.num_departement = d.num_departement LEFT JOIN categorie c ON p.categorie = c.Titre WHERE p.id = $id");
    if ($row = mysqli_fetch_assoc($result)) {

        // Conteneur pour centrer le titre
        echo "<div style='text-align: center;'>";
        echo "<h1><b><u>Fiche d'identification</u> :</b></h1>";
        echo "</div>"; // Fin du conteneur

        // Affichage des détails
        echo "<br><img src='../img/" . $row['photo'] . "' width='100' height='100'>";
        echo "<p><strong>Nom:</strong> " . $row['nom'] . "</p>";
        echo "<p><strong>Prénom:</strong> " . $row['prenom'] . "</p>";
        echo "<p><strong>Département:</strong> " . $row['departement'] . "</p>";
        echo "<p><strong>Catégorie:</strong> " . $row['categorie_titre'] . "</p>";
        echo "<p><strong>Salaire:</strong> " . $row['salaire'] . "</p>";
        echo "<p><strong>NNI:</strong> ". $row['nni_emp'] . "</p>";
        echo "<p><strong>Matricule:</strong> ". $row['matricule_emp'] . "</p>";
        echo "<p><strong>Lieu de travail:</strong> ". $row['lieu_travail'] . "</p>";
        echo "<p><strong>Fonction:</strong> ". $row['lieu_travail'] . "</p>";
        echo "<p><strong>Supérieur Hierarchique:</strong> ". $row['suphierarchie'] . "</p>";
        echo "<p><strong>Email du Supérieur Hierarchique:</strong> ". $row['emailsup'] . "</p>";

        // Conversion de la date au format jj/mm/aaaa
        $dateSaisie = new DateTime($row['datesaisie']);
        echo "<p><strong>Date de Saisie:</strong> " . $dateSaisie->format('d/m/Y') . "</p>";

        
        echo "<div style='text-align: center; margin-top: 20px;'>";
        echo "<button style='background-color: blue; color: white; border: none; padding: 10px 20px; cursor: pointer;' onclick='window.print()'>Imprimer</button>";
        echo "</div>";
    } else {
        echo "Aucun détail trouvé pour cette personne.";
    }
} else {
    echo "ID de personnel non spécifié.";
}
?>

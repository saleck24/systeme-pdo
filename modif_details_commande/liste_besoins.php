<?php
session_start();

// Vérifie si l'utilisateur est connecté, sinon redirige vers la page de connexion.
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

include '../serv_projet1.php';

//le fichier de gestion des commandes pour les archivés ou les mettre dans les instances.
include '../gestion_commandes.php'; 

// Exécute les fonctions pour archiver et déplacer les commandes vers les instances.
archiverCommandes();
deplacerCommandesVersInstances();

// Fonction pour afficher la liste des commandes dont le statut est 'fermé'.
$sql = "SELECT c.id AS commande_id, MIN(eb.date_saisie) AS date_saisie, c.adjoint_mg_valide, c.admin_valide, c.daf_valide, c.prix_total
        FROM expression_besoins eb JOIN commandes c ON eb.commande_id = c.id WHERE c.status = 'fermé'
        GROUP BY c.id 
        ORDER BY c.id ASC";
$result = mysqli_query($conn, $sql);
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Besoins</title>
    <link rel="stylesheet" href="../css/styles_liste_besoins.css">
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
    <main>
        <!--Le traitement de la popup réquisition-->
        <div class="popup" id="requisition-popup">
            <span class="popup-close" onclick="closeRequisitionPopup()">&times;</span>
            <div class="popup-content" id="requisition-popup-content"></div>
            <button class="print-button" onclick="printRequisition()">Imprimer</button>
        </div>

        <!-- Le traitement de la popup pour la validation de l'expression des besoins -->
        <div class="popup" id="popup">
            <span class="popup-close" onclick="closePopup()">&times;</span>
            <div class="popup-content" id="popup-content"></div>
            <button class="print-button" onclick="printCommande()">Imprimer</button>
        </div>

        <h2><u>Liste des Commandes : </u></h2>

        <!-- Table affichant la liste des commandes -->
        <table border="1">
            <tr>
                <th>N° Commande</th>
                <th>Date de Saisie</th>
                <th>Prix Total</th>
                <th>Action</th>
                <th>Validation</th>
            </tr>
            <?php
            // Affiche les commandes si présentes dans la base de données
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $commande_id = $row['commande_id'];
                    $date_saisie = $row['date_saisie'];
                    $adjoint_mg_valide = $row['adjoint_mg_valide'];
                    $admin_valide = $row['admin_valide'];
                    $daf_valide = $row['daf_valide'];
                    $prix_total = $row['prix_total'];

                    echo "<tr>";
                    echo "<td><a href='details_commande.php?commande_id=$commande_id'>$commande_id</a></td>";
                    echo "<td>" . $date_saisie . "</td>";
                    echo "<td>" . number_format($prix_total?? 0, 2) . " MRU</td>";
                    echo "<td>";
                    echo "<button class='btn-green btn-margin-right' onclick='openPopup($commande_id)'>Validation Expression Besoins</button>";
                    echo "<button class='btn-blue' onclick='openRequisitionPopup($commande_id)'>Réquisition</button>";
                    echo "</td>";

                    echo "<td>";
                    // Afficher les boutons de validation en fonction du rôle
                    if ($role == 2 && !$adjoint_mg_valide) { // user_Adjoint_MG à changer dans la table 'commandes'
                        echo "<form action='valider_commande.php' method='POST'>";
                        echo "<input type='hidden' name='commande_id' value='$commande_id'>";
                        echo "<input type='hidden' name='role' value='Adjoint_MG'>";
                        echo "<button type='submit' class='validate-button'>Validation Adjoint_MG</button>";
                        echo "</form>";
                    } elseif ($role == 21 && $adjoint_mg_valide && !$admin_valide) { // user_dept_administratif
                        echo "<form action='valider_commande.php' method='POST'>";
                        echo "<input type='hidden' name='commande_id' value='$commande_id'>";
                        echo "<input type='hidden' name='role' value='Dept_Admin'>";
                        echo "<button type='submit' class='validate-button'>Valider Administratif</button>";
                        echo "</form>";
                    } elseif ($role == 4 && $adjoint_mg_valide && $admin_valide && !$daf_valide) { // user_daf
                        echo "<form action='valider_commande.php' method='POST'>";
                        echo "<input type='hidden' name='commande_id' value='$commande_id'>";
                        echo "<input type='hidden' name='role' value='daf'>";
                        echo "<button type='submit' class='validate-button'>Valider DAF</button>";
                        echo "</form>";
                    } else {
                        // Affiche l'état de validation pour chaque rôle
                        echo "<b>";
                        echo $adjoint_mg_valide ? "Adjoint Directeur Moyen Généraux : <span class='badge validated'>Validée</span><br>" : "Adjoint Directeur Moyen Généraux : <span class='badge pending'>En attente</span><br>";
                        echo $admin_valide ? "Directeur Administratif: <span class='badge validated'>Validée</span><br>" : "Directeur Administratif: <span class='badge pending'>En attente</span><br>";
                        echo $daf_valide ? "DAF: <span class='badge validated'>Validée</span><br>" : "DAF: <span class='badge pending'>En attente</span><br>";
                        //echo $adjoint_mg_valide ? "Adjoint Directeur Moyen Généraux : <span style='color: green;'>Validée</span><br>" : "Adjoint Directeur Moyen Généraux : <span style='color: red;'>En attente</span><br>";
                        //echo $admin_valide ? "Directeur Administratif: <span style='color: green;'>Validée</span><br>" : "Directeur Administratif: <span style='color: red;'>En attente</span><br>";
                        //echo $daf_valide ? "DAF: <span style='color: green;'>Validée</span><br>" : "DAF: <span style='color: red;'>En attente</span><br>";
                        echo "</b>";
                    }
                    echo "</td></tr>";
                }
            } else {
                // Message si aucune commande n'est trouvée
                echo "<tr><td colspan='5'>Aucune commande trouvée</td></tr>";
            }
            ?>  
        </table>
        <br><br>
        <a href="../Formulaires/Formulaires_Expression_besoins.php" class="btn-blue">Return</a>
        <br><br>
        <!-- Vérifier le rôle de l'utilisateur pour le bouton "Return to Home" -->
       <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] == 0): ?>
                <a href="../users_dashboard/admin_dashboard.php" class="btn-violet">Return Home</a>
            <?php elseif ($_SESSION['role'] == 1): ?>
                <a href="../users_dashboard/user_dashboard.php" class="btn-violet">Return Home</a>
            <?php elseif ($_SESSION['role'] == -1): ?>
                <a href="../users_dashboard/assistant_dashboard.php" class="btn-violet">Return Home</a>
            <?php endif; ?>
        <?php endif; ?>

        <script src="../js/script_liste_besoins.js"></script>
    </main>
</body>
</html>

<?php
mysqli_close($conn);
?>

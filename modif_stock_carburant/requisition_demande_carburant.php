<?php
session_start();
if (!isset($_SESSION['email']) || ($_SESSION['role'] != 51)) {
    header("Location: index.php");
    exit();
}

include '../serv_projet1.php';

// Vérifier si l'ID de la demande de carburant est passé en paramètre
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $demande_id = intval($_GET['id']);

    // Récupérer la demande de carburant et les informations associées en une seule requête
    $sql = "SELECT d.*, c.* FROM demande_carburant d 
        JOIN carburant c ON d.carburant_id = c.id WHERE c.id = ?";
    
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('i', $demande_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $demande = $result->fetch_assoc();

            // Calculer le total
            $total = $demande['quantite_demande'] * $demande['prix'];
            
            // Obtenir la date actuelle
            $date = date('d/m/Y');

            ?>
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Réquisition Demande Carburant</title>
                <link rel="stylesheet" type="text/css" href="../css/styles_requisition_carburant.css">
            </head>
            <body>
                <div class="header">
                        <p>Date : <?php echo htmlspecialchars($date); ?></p>
                </div><br><br>

                <h1><b><u>Demande de Carburant N° <?php echo htmlspecialchars($demande_id); ?></u> :</b></h1>
                <table border="1">
                    <tr>
                        <th>Objet</th>
                        <td><?php echo htmlspecialchars($demande['Objet']); ?></td>
                    </tr>
                    <tr>
                        <th>N° Stock</th>
                        <td><?php echo htmlspecialchars($demande['n_stock']); ?></td>
                    </tr>
                    <tr>
                        <th>Libellé</th>
                        <td><?php echo htmlspecialchars($demande['libelle']); ?></td>
                    </tr>
                    <tr>
                        <th>Bénéficiaire</th>
                        <td><?php echo htmlspecialchars($demande['beneficiaire']); ?></td>
                    </tr>
                    <tr>
                        <th>Quantité en Stock</th>
                        <td><?php echo htmlspecialchars($demande['quantite']); ?></td>
                    </tr>
                    <tr>
                        <th>Prix</th>
                        <td><?php echo htmlspecialchars($demande['prix']); ?> MRU/Litre</td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td><?php echo htmlspecialchars($total); ?> MRU</td>
                    </tr>
                    <tr>
                        <th>Demandeur</th>
                        <td><?php echo htmlspecialchars($demande['demandeur']); ?></td>
                    </tr>
                    <tr>
                        <th>Quantité demandée</th>
                        <td><?php echo htmlspecialchars($demande['quantite_demande']); ?> Litres</td>
                    </tr>
                    <tr>
                        <th>Chef Carburant</th>
                        <td><?php echo htmlspecialchars($_SESSION['email'] ); ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><?php echo htmlspecialchars($demande['status']); ?></td>
                    </tr>
                </table>
                
                <!-- Bouton pour imprimer la réquisition -->
                <div class = 'btn-print'>
                    <button onclick="window.print()" class="print-button">Imprimer</button>
                </div>
            </body>
            </html>
            <?php
        } else {
            echo "<p>Demande de carburant non trouvée ou non validée.</p>";
        }

        $stmt->close();
    } else {
        echo "Erreur dans la préparation de la requête SQL.";
    }
} else {
    echo "ID de la demande non spécifié ou invalide.";
}

$conn->close();
?>

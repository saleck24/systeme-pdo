<?php
session_start();
require_once("serv_projet1.php");

if (isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];

    $sql = "SELECT pr.*, u.email
            FROM permission_requests pr
            JOIN users u ON pr.user_id = u.id
            WHERE pr.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();

    if ($request) {
        // Convertir les dates de départ et de retour au format jj/mm/aaaa
        $date_depart = DateTime::createFromFormat('Y-m-d', $request['date_depart']);
        $date_retour = DateTime::createFromFormat('Y-m-d', $request['date_retour']);
        ?>
        
    
        <h2>Permission de Congé</h2>
        <p><strong>Nom :</strong> <?php echo htmlspecialchars($request['nom']); ?></p>
        <p><strong>Prenom :</strong> <?php echo htmlspecialchars($request['prenom']); ?></p>
        <p><strong>Demandeur :</strong> <?php echo htmlspecialchars($request['email']); ?></p>
        <p><strong>Date de Demande :</strong> <?php echo htmlspecialchars($request['request_date']); ?></p>
        <p><strong>Status :</strong> <?php echo htmlspecialchars($request['status']); ?></p>
        <p><strong>Date de Départ :</strong><?php echo $date_depart->format('d/m/Y'); ?> </p> 
        <p><strong>Date de Retour :</strong> <?php echo $date_retour->format('d/m/Y'); ?></p>
        <p><strong>Nombre de Jours :</strong> <?php echo htmlspecialchars($request['nb_jours']); ?></p>
        <div class="btn-print">
                <button onclick="window.print();">Imprimer</button>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "Demande non trouvée.";
    }
} else {
    echo "ID de demande non fourni.";
}
?>

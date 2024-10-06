<?php
session_start();
require_once("../serv_projet1.php");

date_default_timezone_set('UTC');// définir le fuseau horaire pour éviter les décalages.

// Compteur du nombre de jours de repos.
$nb = 0;
$successMessage = "";// Variable pour stocker le message de réussite
$nom = "";
$prenom = "";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // Récupérer les informations de l'utilisateur connecté
    $sql = "SELECT p.nom, p.prenom FROM personnel p INNER JOIN users u ON p.id = u.id WHERE u.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nom = $row['nom'];
        $prenom = $row['prenom'];
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dp = $_POST['date_depart']; // Récupérer la valeur saisie pour date_depart
    $dr = $_POST['date_retour']; // Récupérer la valeur saisie pour date_retour
    
    // Calculer le nombre de jours de repos en excluant les dimanches
    $date1 = new DateTime($dp);
    $date2 = new DateTime($dr);
    $interval = $date1->diff($date2);
    
    $period = new DatePeriod($date1->modify('+1 day'), new DateInterval('P1D'), $date2/*->modify('+1 day')*/);
    
    // Compter les jours de repos en excluant les dimanches
    foreach ($period as $dt) {
        if ($dt->format('N') != 7) { // Exclure les dimanches
            $nb++;
        }
    }
    
    // Envoi de la demande à un supérieur hiérarchique
    $supervisor_id = $_POST['supervisor_id'];
    $request_date = date('Y-m-d H:i:s');
    $notified = 0; // Valeur par défaut pour le champ notified

    $sql = "INSERT INTO permission_requests (user_id, supervisor_id, request_date, status, date_depart, date_retour, nb_jours,notified, nom, prenom) 
            VALUES (?, ?, ?, 'Pending', ?, ?, ?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssiiss", $user_id, $supervisor_id, $request_date, $dp, $dr, $nb, $notified,$nom,$prenom);
    
    if ($stmt->execute()) {
        $successMessage = "Demande de Permission envoyée avec succès.";
    } else {
        $successMessage = "Erreur lors de l'envoi de la Demande de Permission.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Permission</title>
    <link rel="stylesheet" href="../css/styles_formulaire-permission.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Inclure le CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
<body><br><br>  
    <form method="post" action="">
        <div class="container">
            <div>Formulaire de permission</div>
            <hr>
            <label>Nom : </label>
            <input type="text" name="nom" value="<?php echo $nom; ?>" readonly required>
            <label>Prénom : </label>
            <input type="text" name="prenom" value="<?php echo $prenom; ?>" readonly required>
            <label>Superieur hiérarchique : </label>
            <select id="supervisor-select" name="supervisor_id" required>
            <?php
                try {
                    // Récupérer tous les utilisateurs de la base de données.
                    $result = $conn->query("SELECT id, email FROM users");
                    if ($result === false) {
                        throw new Exception("Erreur lors de la récupération des utilisateurs.");
                    }
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['email'] . "</option>";
                    }
                } catch (Exception $e) {
                    echo "<option value=''>Erreur de chargement des superviseurs</option>";
                    error_log($e->getMessage());
                }
            ?>
            </select>
            <label>Date de départ : </label>
            <input type="date" name="date_depart" placeholder="date de départ" required>
            <label>Date de retour : </label>
            <input type="date" name="date_retour" placeholder="date de retour" required>
            <br>
            <label>Nombre total de jours de repos : <?php echo $nb; ?></label>
            <br>
            <input type="submit" value="Envoyer">
        </div>
    </form>

    <div class="btn-container">
        <a href="Formulaires_RH.php" class="btn-blue">Return</a>
    </div>

    <!-- Inclure jQuery et le JS de Select2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#supervisor-select').select2({
                placeholder: 'Sélectionner le Supérieur hiérarchique',
                allowClear: true
            });
        });
//------------------------------------------------------------------
        // Vérifier s'il y a un message de succès depuis PHP et afficher l'alerte
        <?php if (!empty($successMessage)): ?>
            Swal.fire({
                title: 'Succès',
                text: '<?php echo $successMessage; ?>',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        // Vérifier s'il y a un message d'erreur depuis PHP et afficher l'alerte
        <?php if (!empty($errorMessage)): ?>
            Swal.fire({
                title: 'Erreur',
                text: '<?php echo $errorMessage; ?>',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    </script>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaires Finance</title>
    <link rel="stylesheet" href="../css/styles_formulairesRH.css">
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
        <div class="form-container">
            <div class="form-item">
                <a href="../modif_supp_form_avance/avance_salaire.php">
                <img src="../img/icon_formulaire.png" alt="avance_salaire">
                    <div><strong>Avance Salaire</strong></div>
                </a>
            </div>

            
        </div>
        <br><br>
       <!-- Vérifier le rôle de l'utilisateur pour le bouton "Return to Home" -->
       <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] == 0): ?>
                <a href="../users_dashboard/admin_dashboard.php" class="btn-blue">Return Home</a>
            <?php elseif ($_SESSION['role'] == 1): ?>
                <a href="../users_dashboard/user_dashboard.php" class="btn-blue">Return Home</a>
            <?php elseif ($_SESSION['role'] == -1): ?>
                <a href="../users_dashboard/assistant_dashboard.php" class="btn-blue">Return Home</a>
            <?php elseif ($_SESSION['role'] == 3): ?>
                <a href="../users_dashboard/user_site_dashboard.php" class="btn-blue">Return Home</a>
            <?php elseif ($_SESSION['role'] == 4): ?>
                <a href="../users_dashboard/user_DAF_dashboard.php" class="btn-blue">Return Home</a>
            <?php elseif ($_SESSION['role'] == 31): ?>
                <a href="../users_dashboard/user_logistique_dashboard.php" class="btn-blue">Return Home</a>
            <?php endif; ?>
        <?php endif; ?>
    </main>
    <br><br>
    <div class="copyright">© Copyright Saleck BAYA 2024</div>
</body>
</html>

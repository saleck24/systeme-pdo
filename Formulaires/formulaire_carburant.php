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
    <title>Formulaires</title>
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
                <a href="../Formulaires/stock_carburant.php">
                <img src="../img/icon_formulaire.png" alt="Formulaire stock carburant">
                    <div><strong>Formulaire Stock Carburant</strong></div>
                </a>
            </div>

            <div class="form-item">
                <a href="../modif_stock_carburant/liste_demande_carburant.php">
                <img src="../img/icon_formulaire.png" alt="liste demande carburant">
                    <div><strong>Liste demande Carburant</strong></div>
                </a>
            </div>
            
        </div>
        <br><br>
       <!-- Vérifier le rôle de l'utilisateur pour le bouton "Return to Home" -->
       <div class = 'btn-return'>
            <?php if (isset($_SESSION['role'])): ?>
                    <?php if ($_SESSION['role'] == 0): ?>
                        <a href="../users_dashboard/admin_dashboard.php" class="btn-blue">Return</a>
                    <?php elseif ($_SESSION['role'] == 1): ?>
                        <a href="../users_dashboard/user_dashboard.php" class="btn-blue">Return</a>
                    <?php elseif ($_SESSION['role'] == -1): ?>
                        <a href="../users_dashboard/assistant_dashboard.php" class="btn-blue">Return</a>
                    <?php elseif ($_SESSION['role'] == 4): ?>
                        <a href="../users_dashboard/user_DAF_dashboard.php" class="btn-blue">Return</a>
                    <?php elseif ($_SESSION['role'] == 5): ?>
                        <a href="../users_dashboard/user_agent_carburant.php" class="btn-blue">Return</a>
                    <?php elseif ($_SESSION['role'] == 51): ?>
                        <a href="../users_dashboard/user_sup_carburant.php" class="btn-blue">Return</a>
                    <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
    <br><br>
    <div class="copyright">© Copyright Saleck BAYA 2024</div>
</body>
</html>

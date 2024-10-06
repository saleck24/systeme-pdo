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
    <title>Formulaires_Ach</title>
    <link rel="stylesheet" href="../css/styles_formulairesRH.css">
</head>
<body>
    <header>
        <nav>
            <div class="title">Kenz Mining</div>
            <div class="logout">
                    <a href="../logout.php">
                    <img src="../img/icon_logout.png" alt="Logout">
                    </a>
            </div>
        </nav>
    </header>
    <main>
        <div class="form-container">
            <div class="form-item">
                <a href="../Formulaires/formulaires_Achat.php">
                <img src="../img/icon_formulaire.png" alt="Formulaire_Achat">
                    <div><strong>Formulaires_Ach</strong></div>
                </a>
            </div>
            
        </div>
        <br><br>
       <!-- Vérifier le rôle de l'utilisateur pour le bouton "Return to Home" -->
       <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] == 0): ?>
                <a href="../users_dashboard/admin_dashboard.php" class="btn-blue">Return Home</a>
            <?php elseif ($_SESSION['role'] == 2): ?>
                <a href="../users_dashboard/user_achat_dashboard.php" class="btn-blue">Return Home</a>
            <?php elseif ($_SESSION['role'] == -1): ?>
                <a href="../users_dashboard/assistant_dashboard.php" class="btn-blue">Return Home</a>
            <?php elseif ($_SESSION['role'] == 21): ?>
                <a href="../users_dashboard/user_dept_administratif_dashboard.php" class="btn-blue">Return Home</a>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</body>
</html>

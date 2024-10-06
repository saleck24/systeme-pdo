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
    <title>Paramètres de l'Admin</title>
    <link rel="stylesheet" href="css/styles_paramètre-admin.css">
</head>
<body>
    <header>
        <nav>
            <div class="title">Kenz Mining</div>
            <div class="logout">
                    <a href="logout.php">
                    <img src="img/icon_logout.png" alt="Logout">
                    </a>
            </div>
        </nav>
    </header>
    <main>
        <div class="form-container">
            <div class="form-item">
                <a href="add_user.php">
                <img src="img/icon_user.png" alt="Ajouter un Utilisateur">
                    <div><strong>Ajouter un Utilisateur</strong></div>
                </a>
            </div>
            <div class="form-item">
                <a href="gestion_users/manage_users.php">
                <img src="img/icon_manage.png" alt="Gérer les Utilisateurs">
                    <div><strong>Gérer les Utilisateurs</strong></div>
                </a>
            </div>
            
            <div class="form-item">
                <a href="Formulaires/formulaire_parametre-appli.php">
                <img src="img/icon_parametre-appli.png" alt="Formulaire Parametre-appli">
                    <div><strong>Formulaire des Paramètres Application</strong></div>
                </a>
            </div>
        </div>
        <a href="users_dashboard/admin_dashboard.php" class="btn-blue">Return Home</a>
    </main>

</body>
</html>

















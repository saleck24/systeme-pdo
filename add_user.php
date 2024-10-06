<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 0) {
    header("Location: unauthorized.php");
    exit();
}

require 'serv_projet1.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($conn) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $hashed_password, $role);

        if ($stmt->execute()) {
            $success_message = "Utilisateur ajouté avec succès.";
        } else {
            $error_message = "Erreur lors de l'ajout de l'utilisateur.";
        }
    } else {
        $error_message = "Erreur de connexion à la base de données.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Utilisateur</title>
    <link rel="stylesheet" href="css/styles_add-user.css">
</head>
<body>
    <header>
        <nav>
            <div class="title">Kenz Mining</div>
            <div class="logout">
                <a href="logout.php">Logout</a>
            </div>
        </nav>
    </header>
    <main>
        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <div class="container">
            <div class="logo">
                <img src="img/LOGO.png" alt="Logo">
            </div>
            <div class="container1">
                <div class="title">Ajouter un Utilisateur</div>
                <hr>
                <br>
                <form action="add_user.php" method="post">
                    <div class="input-container">
                        <div class="input-group">
                            <img src="img/icon-email.png" alt="Email" class="input-icon input-icon-email">
                            <input type="email" id="email" name="email" placeholder="Email" required>
                        </div>
                        
                    </div>
                    <div class="input-container">
                        <div class="input-group">
                            <img src="img/icon-lock.png" alt="Lock" class="input-icon input-icon-lock">
                            <input type="password" id="password" name="password" placeholder="Password" required>
                            <img src="img/icon-eye.png" alt="Toggle Password" class="input-icon input-icon-eye toggle-password">
                        </div>
                    </div>
                    <div class="input-container">
                        <label for="role">Niveau d'autorisation : <select id="role" name="role">
                                                    <option value="0">Admin</option>
                                                    <option value="1">User</option>
                                                    <option value="-1">Assistant</option>
                                                    <option value="2">User_Adjoint_Admin</option>
                                                    <option value="21">User_Dept_administratif</option>
                                                    <option value="22">User_Agent_MG</option>
                                                    <option value="3">User_site</option>
                                                    <option value="31">User_Logistique</option>
                                                    <option value="4">User_DAF</option>
                                                    <option value="5">User_Agent_Carburant</option>
                                                    <option value="51">User_Sup_Carburant</option>
                                                    <option value="7">User_resp_rh</option>
                                                </select>
                        </label>
                    </div>
                    <button type="submit">Ajouter</button>
                </form>
            </div>    
        </div>
        <br>
        <a href="users_dashboard/admin_dashboard.php" class="btn-blue">Return to Home</a>
    </main>
    <script src="js/script.js"></script>
</body>
</html>

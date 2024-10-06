<?php
session_start();
// Connexion à la base de données
require 'serv_projet1.php';

$errorMessage = "";  // Variable pour stocker le message d'erreur

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Rechercher l'utilisateur par email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Vérifier le mot de passe haché
        if (password_verify($password, $user['password'])) {

            if ($user['role'] == -2) {
                // Utilisateur bloqué
                header("Location: unauthorized.php");
                exit();
            }

            // Stocker les informations de l'utilisateur dans la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            
            // Rediriger en fonction du rôle de l'utilisateur
            switch ($user['role']) {
                case 0:
                    header("Location: users_dashboard/admin_dashboard.php");
                    break;
                case -1:
                    header("Location: users_dashboard/assistant_dashboard.php");
                    break;
                case 1:
                    header("Location: users_dashboard/user_dashboard.php");
                    break;
                case 2:
                    header("Location: users_dashboard/user_Adjoint_MG.php");
                    break;
                case 21:
                    header("Location: users_dashboard/user_dept_admin_dashboard.php");
                    break;
                case 22:
                    header("Location: users_dashboard/user_agent_MG.php");
                    break;
                case 3:
                    header("Location: users_dashboard/user_site_dashboard.php");
                    break;
                case 31:
                    header("Location: users_dashboard/user_logistique_dashboard.php");
                    break;
                case 4:
                    header("Location: users_dashboard/user_daf_dashboard.php");
                    break;
                case 5:
                    header("Location: users_dashboard/user_agent_carburant.php");
                    break;
                case 51:
                    header("Location: users_dashboard/user_sup_carburant.php");
                    break;
                case 7:
                    header("Location: users_dashboard/user_resp_rh.php");
                    break;
                default:
                    header("Location: unauthorized.php");
            }
            exit();
        } else {
            // Mot de passe incorrect
            $errorMessage = "Mot de passe incorrect.";
        }
    } else {
        // Email incorrect ou utilisateur non trouvé
        $errorMessage = "Email incorrect ou utilisateur non trouvé.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="css/styles_index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="img/LOGO.png" alt="Logo">
        </div>
        <div class="title">Login</div>
        <form action="" method="post">
            <div class="input-container">
                <div class="input-group">
                    <img src="img/email.png" alt="Email" class="input-icon input-icon-email">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <img src="img/verrouiller-alt.png" alt="Lock" class="input-icon input-icon-lock">
                    <input type="password" name="password" placeholder="Mot de passe" required>
                    <img src="img/icon-eye.png" alt="Toggle Password" class="input-icon input-icon-eye toggle-password">
                </div>
            </div>
            <button type="submit">Login</button>
            <br><br>
            <div class="copyright">© Copyright Saleck BAYA 2024</div>
        </form>
    </div>

    <!-- Loader -->
    <div id="loader" class="loader" style="display: none;">
        <img src="img/spinner.gif" alt="Loading...">
    </div>

    <!-- Fonction JavaScript pour afficher un message d'erreur avec SweetAlert2 -->
    <script>
        function showErrorMessage(message) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: message,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'my-confirm-button'
                }
            }).then(function() {
                window.location = 'index.php';
            });
        }

        // Vérifier si un message d'erreur existe
        <?php if (!empty($errorMessage)) : ?>
            showErrorMessage('<?php echo $errorMessage; ?>');
        <?php endif; ?>
    </script>

    <script src="js/script.js"></script>
</body>
</html>

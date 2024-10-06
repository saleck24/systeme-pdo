<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}
if ($_SESSION['role'] != 7) {
    header("Location: unauthorized.php");
    exit();
}

require_once("../serv_projet1.php");

// Vérifiez si l'identifiant de l'utilisateur est défini
if (!isset($_SESSION['user_id'])) {
    echo "L'identifiant de l'utilisateur n'est pas défini dans la session.";
    exit();
}

// Requête pour récupérer les informations de l'utilisateur connecté
$sql_user_info = "SELECT p.nom, p.prenom, p.photo
                  FROM personnel p
                  JOIN users u ON p.user_id = u.id
                  WHERE u.id = ?";

$stmt_user_info = $conn->prepare($sql_user_info);
if ($stmt_user_info) {
    $stmt_user_info->bind_param("i", $_SESSION['user_id']);
    $stmt_user_info->execute();
    $result_user_info = $stmt_user_info->get_result();

   
    // Vérifier si la requête a retourné un résultat
    if ($result_user_info && $result_user_info->num_rows > 0) {
        $user_info = $result_user_info->fetch_assoc();
        
        //S'assurer de bien avoir récupérer les bonnes valeurs.
        $_SESSION['nom'] = $user_info['nom'];
        $_SESSION['prenom'] = $user_info['prenom'];
        $_SESSION['photo'] = $user_info['photo'];
    }else {
        echo "Aucune information trouvée pour l'utilisateur avec l'ID " . $_SESSION['user_id'];
        exit();
    }
    
} else {
    echo "Erreur de préparation de la requête: " . $conn->error;
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsable RH Dashboard</title>
    <link rel="stylesheet" href="../css/styles_userdashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- script pour la swal.fire-->
</head>
<body>
    <header>
        <nav>
            <!-- Bouton hamburger -->
            <div class="menu-toggle" id="menu-toggle">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>

            <!-- Titre -->
            <div class="logo-container">
                 <img src="../img/LOGO.png" alt="Logo de Kenz Mining SA" class="logo-image">
            </div>
            <div class="header-icons">
                <!-- Conteneur de notification -->
                <div class="notification-container">
                    <!-- Icône de notification -->
                    <img src="../img/icon_notification.png" alt="Notification Icon" class="notification-icon" onclick="toggleDropdown()" />

                    <!-- Liste déroulante des notifications -->
                    <div id="notification-dropdown" class="notification-dropdown">
                        <!-- Le contenu sera chargé dynamiquement ici -->
                        <div id="notification-content">
                        <!-- Le contenu de notification.php sera inséré ici -->
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <!-- Fenêtre coulissante -->
    <div class="slide-out-panel" id="slide-out-panel">
        <div class="panel-content"> 
            <?php if (!empty($_SESSION['photo'])): ?>
                <img src="../img/<?php echo htmlspecialchars($_SESSION['photo']); ?>" alt="Photo de l'utilisateur" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
            <?php endif; ?><br><br>
            <?php echo  htmlspecialchars($_SESSION['nom']) . ' ' . htmlspecialchars($_SESSION['prenom']); ?>
        </div><br><br>

        <!-- Le changement de mot de passe -->
        <div class="Change_pswd">
            <a href="../change_pswd.php">
                <img src="../img/icon_pswd.png" alt="change_pswd">
                <strong>Changer votre mon de passe</strong>
            </a>
        </div>

        <div class="logout">
            <a href="../logout.php">
                <img src="../img/se-deconnecter.png" alt="Logout">
                <strong>Se déconnecté</strong>
            </a>
        </div> 
    </div>   
    <main>
        <!-- Texte de bienvenue -->
        <div class="welcome-message">
            <?php
            echo "Bienvenu(e) : " . strtoupper(htmlspecialchars($_SESSION['nom'])) . " " . strtoupper(htmlspecialchars($_SESSION['prenom']));
            ?>
        </div>
        <div class="form-container">
            <div class="form-item">
                <a href="../Formulaires/Formulaires_RH.php">
                    <img src="../img/GRH.png" alt="RH">
                    <div>Département Ressources Humaines</div>
                </a>
            </div>  
        </div>
    </main>

    <!-- Code HTML pour la modal de l'impression de la permission -->
    <div id="print-modal" class="modal print-modal">
        <div class="modal-content print-modal-content">
            <span class="close">&times;</span>
            <iframe id="print-iframe" style="width: 100%; height: 500px; border: none;"></iframe>
        </div>
    </div>

    <!-- Partie Html(Modal) pour changer le mot de passe -->
    <div id="changePasswordModal" class="modal change-password-modal">
        <div class="modal-content change-password-modal">
            <span class="close">&times;</span>
            <h2 class="modal-title">Changer votre mot de passe</h2>
            <form id="changePasswordForm">
                <div class="input-group">
                    <img src="../img/fermer-a-cle.png" alt="Lock" class="input-icon input-icon-lock">
                    <input type="password" id="currentPassword" name="currentPassword" placeholder="Mot de passe actuel"required>
                    <img src="../img/icon-eye.png" alt="Toggle Password" class="input-icon input-icon-eye toggle-password">
                </div>
                <div class="input-group">
                    <img src="../img/fermer-a-cle.png" alt="Lock" class="input-icon input-icon-lock">
                    <input type="password" id="newPassword" name="newPassword" placeholder="Nouveau mot de passe" required>
                    <img src="../img/icon-eye.png" alt="Toggle Password" class="input-icon input-icon-eye toggle-password">
                </div>
                <button type="submit" id="updatePasswordButton"  class="btn-blue">Mettre à jour</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- script pour la swal.fire-->
    <script>
            document.addEventListener('DOMContentLoaded', function() {//script pour changer le mot de passe et son affichage
                // Gestion de l'affichage/masquage du mot de passe
                const togglePasswordIcons = document.querySelectorAll('.toggle-password');
                togglePasswordIcons.forEach(function(togglePassword) {
                togglePassword.addEventListener('click', function() {
                    const passwordInput = this.previousElementSibling;
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Changer l'icône en fonction du type de champ
                    this.src = type === 'password' ? '../img/icon-eye.png' : '../img/icon-eye-off.png';
                });
            });

            // Affichage de la modal lors du clic sur le lien "Changer votre mot de passe"
            document.querySelector(".Change_pswd a").addEventListener("click", function(event) {
                event.preventDefault();
                document.getElementById("changePasswordModal").style.display = "block";
            });

            // Fermeture de la modal
            document.querySelector(".close").addEventListener("click", function() {
                document.getElementById("changePasswordModal").style.display = "none";
            });

            // Fermer la modal en cliquant en dehors de celle-ci
            window.onclick = function(event) {
                if (event.target == document.getElementById("changePasswordModal")) {
                    document.getElementById("changePasswordModal").style.display = "none";
                }
            };

            // Gestion du formulaire de changement de mot de passe
            document.getElementById("changePasswordForm").addEventListener("submit", function(event) {
                event.preventDefault();

                var currentPassword = document.getElementById("currentPassword").value;
                var newPassword = document.getElementById("newPassword").value;

                // Envoyer une requête AJAX pour vérifier le mot de passe
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "change_pswd.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            Swal.fire("Succès", "Mot de passe changé avec succès", "success");
                            document.getElementById("changePasswordModal").style.display = "none";
                        } else {
                            Swal.fire("Erreur", "Mot de passe saisie est incorrect", "error");
                        }
                    }
                };
                xhr.send("currentPassword=" + encodeURIComponent(currentPassword) + "&newPassword=" + encodeURIComponent(newPassword));
            });
        });
//-------------------------------------------------------------------------------------------------------------------------------------
        //script pour l'activation et la gestion de la fenêtre coulissante.
        document.getElementById('menu-toggle').addEventListener('click', function(event) {
            var panel = document.getElementById('slide-out-panel');
            panel.classList.toggle('active');
            event.stopPropagation(); // Empêche le clic de se propager au document
        });

        // Ferme la fenêtre coulissante en cliquant à l'extérieur
        document.addEventListener('click', function(event) {
            var panel = document.getElementById('slide-out-panel');
            var menuToggle = document.getElementById('menu-toggle');
            if (!panel.contains(event.target) && !menuToggle.contains(event.target)) {
                panel.classList.remove('active'); // Enlève la classe active pour fermer le panneau
            }
        });

        // Empêche la fermeture de la fenêtre coulissante lorsque l'on clique à l'intérieur
        document.getElementById('slide-out-panel').addEventListener('click', function(event) {
            event.stopPropagation();
        });


        // Script pour gérer le menu déroulant dans la fenêtre coulissante
        document.getElementById('dropdown-toggle').addEventListener('click', function(event) {
            var dropdownMenu = document.getElementById('dropdown-menu');
            if (dropdownMenu.style.display === 'none' || dropdownMenu.style.display === '') {
                dropdownMenu.style.display = 'block';
            } else {
                dropdownMenu.style.display = 'none';
            }
            event.preventDefault(); // Empêche le comportement par défaut du lien
            event.stopPropagation(); // Empêche le clic de se propager au document
        });

        // Ferme le menu déroulant en cliquant à l'extérieur de celui-ci
        document.addEventListener('click', function(event) {
            var dropdownMenu = document.getElementById('dropdown-menu');
            var dropdownToggle = document.getElementById('dropdown-toggle');
            if (!dropdownMenu.contains(event.target) && !dropdownToggle.contains(event.target)) {
                dropdownMenu.style.display = 'none'; // Masque le menu déroulant
            }
        });
//---------------------------------------------------------------------------------------------
//Gestion du "Dark Mode"

        document.addEventListener('DOMContentLoaded', function() {
            // Gestion du bouton Dark Mode
            const darkModeToggle = document.getElementById('toggle-dark-mode');
            const darkModeIcon = document.getElementById('dark-mode-icon');
            const body = document.body;

            // Vérifier si le mode sombre est déjà activé
            if (localStorage.getItem('darkMode') === 'enabled') {
                body.classList.add('dark-mode');
                darkModeToggle.innerHTML = '<strong>Mode Clair</strong>'; // Change le texte du lien en gras
            }

            darkModeToggle.addEventListener('click', function(event) {
                event.preventDefault(); // Empêche le comportement par défaut du lien
                body.classList.toggle('dark-mode');

                // Vérifier si le mode sombre est activé
                if (body.classList.contains('dark-mode')) {
                    localStorage.setItem('darkMode', 'enabled');
                    darkModeToggle.innerHTML = '<strong>Mode Sombre</strong>'; // Change le texte du lien en gras
                } else {
                    localStorage.setItem('darkMode', 'disabled');
                    darkModeToggle.innerHTML = '<strong>Mode Clair</strong>'; // Change le texte du lien en gras
                }
            });
        });

//--------------------------------------------------------------------------------------------------------------
//script pour le menu déroulant 'notification'
        // Fonction pour afficher ou cacher la liste déroulante
        function toggleDropdown() {
            var dropdown = document.getElementById('notification-dropdown');

            if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                // Afficher la liste déroulante
                dropdown.style.display = 'block';

                // Charger les notifications
                loadNotifications();
            } else {
                // Cacher la liste déroulante
                dropdown.style.display = 'none';
            }
        }

        // Fonction pour charger dynamiquement les notifications
        function loadNotifications() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '../notification.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById('notification-content').innerHTML = xhr.responseText;
                } else {
                    document.getElementById('notification-content').innerHTML = 'Erreur lors du chargement des notifications.';
                }
            };
            xhr.send();
        }

        // Fermer la liste déroulante si l'utilisateur clique en dehors
        window.onclick = function(event) {
            if (!event.target.matches('.notification-icon')) {
                var dropdowns = document.getElementsByClassName("notification-dropdown");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.style.display === 'block') {
                        openDropdown.style.display = 'none';
                    }
                }
            }
        }

//--------------------------------------------------------------------------------------------------------
//Script pour la modal de l'impression de la permission.

        // Fonction pour ouvrir la modal
        function openModal(requestId) {
            var modal = document.getElementById('print-modal');
            var iframe = document.getElementById('print-iframe');
            
            // Ouvrir la modal
            modal.style.display = "block";

            // Charger le contenu de 'imprimer_permission.php' dans l'iframe
            iframe.src = '../imprimer_permission.php?request_id=' + requestId;
        }

        // Fonction pour fermer la modal
        var span = document.getElementsByClassName("close")[0];
        span.onclick = function() {
            document.getElementById('print-modal').style.display = "none";
        }

        // Fermer la modal si l'utilisateur clique en dehors de la modal
        window.onclick = function(event) {
            var modal = document.getElementById('print-modal');
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }

    </script>

</body>
</html>

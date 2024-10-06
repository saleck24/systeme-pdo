<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}
if ($_SESSION['role'] != 0) {
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

// Compter le nombre de demandes en attente de notification
$sql = "SELECT COUNT(*) AS notification_count FROM permission_requests WHERE supervisor_id = ? AND status = 'Pending' AND notified = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$notification_count = $row['notification_count'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles_admindashboard.css">
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

         <!-- Liste déroulante pour les paramètres de l'application -->
        <div class="dropdown">
            <a href="#" class="dropdown-toggle" id="dropdown-toggle">
                <img src="../img/icon-paramètre1.png" alt="Paramètre admin">
                <strong>Paramètre admin</strong>
            </a>
            <div class="dropdown-menu" id="dropdown-menu" style="display: none;">
                <a href="../add_user.php">
                    <img src="../img/icon_user.png" alt="Ajouter un Utilisateur">
                    <strong>Ajouter un Utilisateur</strong>
                </a><br>

                <a href="../gestion_users/manage_users.php">
                    <img src="../img/icon_manage.png" alt="Gérer les Utilisateurs">    
                    <strong>Gérer les Utilisateurs</strong>
                </a><br>

                <a href="../Formulaires/formulaire_parametre-appli.php">
                    <img src="../img/icon_parametre-appli.png" alt="Formulaire Parametre-appli">
                    <strong>Formulaire des Paramètres Application</strong>
                </a>
            </div>
        </div><br>

        <div class="Change_pswd">
            <a href="../change_pswd.php">
                <img src="../img/icon_pswd.png" alt="change_pswd">
                <strong>Changer votre mon de passe</strong>
            </a>
        </div>

        <!-- le bouton Dark Mode -->
        <div class="dark-mode-toggle">
            <img src="../img/mode-sombre.png" alt="dark mode" id="dark-mode-icon">
            <a href="#" id="toggle-dark-mode"><strong>Mode Clair</strong></a>
        </div>


        <div class="logout">
            <a href="../logout.php">
                <img src="../img/se-deconnecter.png" alt="Logout">
                <strong>Se déconnecté</strong>
            </a>
        </div> 
    </div>  
     
    <main class="d-flex">
        <!-- Sidebar fixe -->
        <div class="sidebar position-fixed">
            <!-- Logo -->
            <img src="../img/LOGO.png" alt="Logo" class="menu-logo">
            <hr> <!-- Trait horizontal -->
            <h2 class="h5">Menu principal</h2>
            <hr class="custom-hr">
            <!-- Menu items -->
            <div class="menu-item mb-3">
                <div class="menu-header" onclick="toggleDropdown('personnelDropdown')">
                    <span class="arrow">&#9654;</span> <!-- Flèche vers la droite -->
                    <span><b>Département Ressources Humaines</b></span>
                </div>
                <div class="collapse" id="personnelDropdown">
                    <ul class="list-unstyled">
                        <li><a href="../Formulaires/formulaire_personnel.php" class="dropdown-item">Formulaire Personnel</a></li>
                        <li><a href="../Formulaires/formulaire_departement.php" class="dropdown-item">Formulaire Département</a></li>
                        <li><a href="../Formulaires/formulaire_categorie.php" class="dropdown-item">Formulaire Catégorie</a></li>
                        <li><a href="../Formulaires/formulaire_permission.php" class="dropdown-item">Formulaire Permission</a></li>
                        <li><a href="../Formulaires/Formulaire_avance.php" class="dropdown-item">Formulaire Avance</a></li>
                    </ul>
                </div>
            </div>

            <div class="menu-item mb-3">
                <div class="menu-header" onclick="toggleDropdown('departementDropdown')">
                    <span class="arrow">&#9654;</span> <!-- Flèche vers la droite -->
                    <span><b>Département Moyen Généraux</b></span>
                </div>
                <div class="collapse" id="departementDropdown">
                    <ul class="list-unstyled">
                        <li><a href="../Formulaires/formulaires_Expression_besoins.php" class="dropdown-item">Formulaires Expression besoins</a></li>
                        <li><a href="../Formulaires/formulaires_Expression_besoins_carburant.php" class="dropdown-item">Formulaires Expression besoins Carburant</a></li>
                    </ul>
                </div>
            </div>

            <div class="menu-item mb-3">
                <div class="menu-header" onclick="toggleDropdown('FinanceDropdown')">
                    <span class="arrow">&#9654;</span> <!-- Flèche vers la droite -->
                    <span><b>Département Finance</b></span>
                </div>
                <div class="collapse" id="FinanceDropdown">
                    <ul class="list-unstyled">
                        <li><a href="../modif_supp_form_avance/avance_salaire.php" class="dropdown-item">Avance sur Salaire</a></li>
                    </ul>
                </div>
            </div>

            <div class="menu-item mb-3">
                <div class="menu-header" onclick="toggleDropdown('ArchiveDropdown')">
                    <span class="arrow">&#9654;</span> <!-- Flèche vers la droite -->
                    <span><b>Les Archives & Instances</b></span>
                </div>
                <div class="collapse" id="ArchiveDropdown">
                    <ul class="list-unstyled">
                        <li><a href="../Formulaires/archive.php" class="dropdown-item">Les Archive</a></li>
                        <li><a href="../instance.php" class="dropdown-item">Les Instances</a></li>
                    </ul>
                </div>
            </div>

            <div class="menu-item mb-3">
                <div class="menu-header" onclick="toggleDropdown('CarburantDropdown')">
                    <span class="arrow">&#9654;</span> <!-- Flèche vers la droite -->
                    <span><b>Carburant</b></span>
                </div>
                <div class="collapse" id="CarburantDropdown">
                    <ul class="list-unstyled">
                        <li><a href="../Formulaires/stock_carburant.php" class="dropdown-item">Formulaire Stock Carburant</a></li>
                        <li><a href="../modif_stock_carburant/liste_demande_carburant.php" class="dropdown-item">Liste demande Carburant</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content section (Welcome message and form container) -->
        <div class="content-section ms-auto p-5" style="margin-left: 250px;">
            <!-- Texte de bienvenue -->
            <div class="welcome-message mb-4 text-center">
                
                    <?php
                    echo "Bienvenu(e) : " . strtoupper(htmlspecialchars($_SESSION['nom'])) . " " . strtoupper(htmlspecialchars($_SESSION['prenom']));
                    ?>
                
            </div><br>

            <!-- Form container -->
            <div class="form-container d-flex justify-content-center flex-wrap">
                <div class="form-item text-center">
                    <a href="../Formulaires/Formulaires_RH.php">
                        <img src="../img/GRH.png" alt="RH">
                        <div>Département Ressources Humaines</div>
                    </a>
                </div>

                <div class="form-item text-center">
                    <a href="../Formulaires/Formulaires_MG.php">
                        <img src="../img/pngwing.png" alt="Moyens Géneraux">
                        <div>Département Moyens Géneraux</div>
                    </a>
                </div>

                <div class="form-item text-center">
                    <a href="../Formulaires/Formulaires_finance.php">
                        <img src="../img/finance.png" alt="Finance">
                        <div>Département Finance</div>
                    </a>
                </div>

                <div class="form-item text-center">
                    <a href="../Formulaires/archive.php">
                        <img src="../img/icon-archive.png" alt="archive">
                        <div>Les Archives</div>
                    </a>
                </div>

                <div class="form-item text-center">
                    <a href="../modif_details_commande/liste_besoins.php">
                        <img src="../img/icon_liste_cmd.png" alt="liste_besoins">
                        <div>Liste des commandes</div>
                    </a>
                </div>

                <div class="form-item text-center">
                    <a href="../instance.php">
                        <img src="../img/icon-instance.jpg" alt="instance">
                        <div>Les Instances</div>
                    </a>
                </div>

                <div class="form-item text-center">
                    <a href="../Formulaires/Formulaire_Carburant.php">
                        <img src="../img/icon-carburant.png" alt="carburant">
                        <div>Carburant</div>
                    </a>
                </div>
            </div>
        </div>
    </main>
    <br><br>
            <div class="copyright">© Copyright Saleck BAYA 2024</div>
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

    </script>

    
    <script>
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
//----------------------------------------------------------------------------------
//code pour l'activation des liste du Menu principal de la SideBarr
        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            const isVisible = dropdown.style.display === "block";

            // Ferme tous les autres dropdowns
            const allDropdowns = document.querySelectorAll('.collapse');
            allDropdowns.forEach(dd => {
                dd.style.display = "none";
            });

            // Ferme toutes les flèches
            const arrows = document.querySelectorAll('.arrow');
            arrows.forEach(arrow => {
                arrow.style.transform = "rotate(0deg)";
            });

            // Si le dropdown n'était pas visible, l'affiche
            if (!isVisible) {
                dropdown.style.display = "block";
                dropdown.previousElementSibling.querySelector('.arrow').style.transform = "rotate(90deg)";
            }
        }


    </script>

</body>
</html>

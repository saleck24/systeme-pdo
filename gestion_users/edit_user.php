<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 0) {
    header("Location: unauthorized.php");
    exit();
}

require '../serv_projet1.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Récupérer les détails de l'utilisateur
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        echo "Utilisateur non trouvé.";
        exit();
    }
    
    // Si le formulaire est soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $role = $_POST['role'];
        
        // Mettre à jour le rôle de l'utilisateur
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("ii", $role, $id);
        $stmt->execute();
        
        header("Location: manage_users.php");
        exit();
    }
} else {
    echo "ID utilisateur non spécifié.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le role de l'Utilisateur</title>
    <link rel="stylesheet" href="../css/styles_edit-user.css">
</head>
<body>
    <header>
        <nav>
            <div class="title">Kenz Mining</div>
            <div class="logout">
                <a href="../logout.php">Logout</a>
            </div>
        </nav>
    </header>
    <main>
        <h1>Modifier le role de l'Utilisateur</h1>
        <form method="POST">
            <div class="input-container">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value=" <?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>
            <div class="input-container">
                <label for="role">Rôle</label>
                <select id="role" name="role" required>
                    <option value="0" <?php if ($user['role'] == 0) echo 'selected'; ?>>Admin</option>
                    <option value="1" <?php if ($user['role'] == 1) echo 'selected'; ?>>Utilisateur</option>
                    <option value="-1" <?php if ($user['role'] == -1) echo 'selected'; ?>>Assistant</option>
                    <option value="2" <?php if ($user['role'] == 2) echo 'selected'; ?>>User_Adjoint_Admin</option>
                    <option value="21" <?php if ($user['role'] == 21) echo 'selected'; ?>>User_Dept_administratif</option>
                    <option value="22" <?php if ($user['role'] == 22) echo 'selected'; ?>>User_Agent_MG</option>
                    <option value="3" <?php if ($user['role'] == 3) echo 'selected'; ?>>User_site</option>
                    <option value="31" <?php if ($user['role'] == 31) echo 'selected'; ?>>User_Logistique</option>
                    <option value="4" <?php if ($user['role'] == 4) echo 'selected'; ?>>User_DAF</option>
                    <option value="5" <?php if ($user['role'] == 5) echo 'selected'; ?>>User_Agent_Carburant</option>
                    <option value="51" <?php if ($user['role'] == 51) echo 'selected'; ?>>User_Sup_Carburant</option>
                    <option value="7" <?php if ($user['role'] == 7) echo 'selected'; ?>>User_Resp_RH</option>
                </select>
            </div>
            <button type="submit">Mettre à jour</button>
        </form>
    </main>
</body>
</html>

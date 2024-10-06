<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 0) {
    header("Location: unauthorized.php");
    exit();
}

require '../serv_projet1.php';

// Bloquer un utilisateur
if (isset($_GET['block_id'])) {
    $block_id = intval($_GET['block_id']); // Assurer que l'ID est un entier

    // Assurez que l'utilisateur à bloquer n'est pas un admin
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $block_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $user['role'] != 0) {
        // L'utilisateur n'est pas un admin, on peut le bloquer
        $stmt = $conn->prepare("UPDATE users SET previous_role = role, role = -2 WHERE id = ?");
        $stmt->bind_param("i", $block_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: ../gestion_users/manage_users.php");
    exit();
}

// Débloquer un utilisateur
if (isset($_GET['unblock_id'])) {
    $unblock_id = intval($_GET['unblock_id']); // Assurer que l'ID est un entier

    // S'assurer que l'utilisateur à débloquer est bloqué
    $stmt = $conn->prepare("SELECT role, previous_role FROM users WHERE id = ? AND role = -2");
    $stmt->bind_param("i", $unblock_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
    // L'utilisateur est bloqué, on le débloque et on restaure son rôle d'origine
        $stmt = $conn->prepare("UPDATE users SET role = previous_role WHERE id = ?");
        $stmt->bind_param("i", $unblock_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: ../gestion_users/manage_users.php");
    exit();
}

$result = $conn->query("SELECT id, email, role, CASE 
    WHEN role = -2 THEN 'Bloqué' 
    WHEN role = 0 THEN 'Admin' 
    WHEN role = -1 THEN 'Assistant'
    WHEN role = 1 THEN 'User' 
    WHEN role = 2 THEN 'User_Adjoint_Admin' 
    WHEN role = 21 THEN 'User_Dept_administratif'
    WHEN role = 22 THEN 'User_Agent_MG'
    WHEN role = 3 THEN 'User_site' 
    WHEN role = 31 THEN 'User_Logistique'
    WHEN role = 4 THEN 'User_DAF'
    WHEN role = 5 THEN 'User_Agent_Carburant'
    WHEN role = 51 THEN 'User_Sup_Carburant'
    WHEN role = 7 THEN 'User_Resp_RH'
    ELSE 'Inconnu' END AS role_description 
    FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Utilisateurs</title>
    <link rel="stylesheet" href="../css/styles_manage-users.css">
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
        <h1>Gérer les Utilisateurs</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                <!-- echo htmlspecialchars($row['role']); ?></td>-->
                    <td><?php echo htmlspecialchars($row['role_description']); ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo urlencode($row['id']); ?>" class="btn btn-edit">Modifier</a>
                        <?php if ($row['role'] == -2): ?> <!-- Si l'utilisateur est bloqué -->
                            <a href="manage_users.php?unblock_id=<?php echo urlencode($row['id']); ?>" class="btn btn-unblock">Débloquer</a>
                        <?php elseif ($row['role'] != 0): ?> <!-- Ne pas afficher le bouton "Bloquer" pour les admins -->
                            <a href="manage_users.php?block_id=<?php echo urlencode($row['id']); ?>" class="btn btn-block">Bloquer</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <br><br>
        <a href="../users_dashboard/admin_dashboard.php" class="btn-blue">Return to Home</a>
    </main>
</body>
</html>

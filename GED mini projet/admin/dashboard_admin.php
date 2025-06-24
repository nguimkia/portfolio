<?php
session_start();
require '../includes/db.php';

// Vérification que l'utilisateur est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$msg = '';

// Ajouter un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Vérification si l'utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $existing_user = $stmt->fetch();

    if ($existing_user) {
        $msg = "❌ Cet email est déjà utilisé.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $role]);
        $msg = "✅ Utilisateur ajouté avec succès !";
    }
}

// Supprimer un utilisateur
if (isset($_GET['delete'])) {
    $user_id_to_delete = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id_to_delete]);
    header("Location: admin_users.php");
    exit;
}

// Récupérer la liste des utilisateurs
$stmt = $pdo->prepare("SELECT id, username, email, role FROM users");
$stmt->execute();
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Gestion des utilisateurs</h2>

    <!-- Message d'ajout ou erreur -->
    <?php if ($msg): ?>
        <p class="message"><?= $msg ?></p>
    <?php endif; ?>

    <h3>Ajouter un utilisateur</h3>
    <form method="POST">
        <label for="username">Nom d'utilisateur</label>
        <input type="text" name="username" required><br>

        <label for="email">Email</label>
        <input type="email" name="email" required><br>

        <label for="password">Mot de passe</label>
        <input type="password" name="password" required><br>

        <label for="role">Rôle</label>
        <select name="role" required>
            <option value="user">Utilisateur</option>
            <option value="admin">Administrateur</option>
        </select><br>

        <button type="submit" name="add_user">Ajouter</button>
    </form>

    <h3>Liste des utilisateurs</h3>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $user['id'] ?>">Modifier</a> |
                        <a href="?delete=<?= $user['id'] ?>" onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br><a href="dashboard_admin.php">← Retour au tableau de bord</a>
</body>
</html>

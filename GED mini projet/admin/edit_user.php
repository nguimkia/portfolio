<?php
session_start();
require '../includes/db.php';

// Vérification que l'utilisateur est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$msg = '';
$user_id = $_GET['id'];

// Récupérer les informations actuelles de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: admin_users.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $role = $_POST['role'];

    // Si un mot de passe est modifié
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ?, role = ? WHERE id = ?");
        $stmt->execute([$username, $email, $password, $role, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
        $stmt->execute([$username, $email, $role, $user_id]);
    }

    $msg = "✅ Utilisateur mis à jour avec succès !";
    header("Location: admin_users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un utilisateur</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Modifier l'utilisateur</h2>

    <?php if ($msg): ?>
        <p class="message"><?= $msg ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="username">Nom d'utilisateur</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br>

        <label for="email">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>

        <label for="password">Mot de passe (optionnel)</label>
        <input type="password" name="password"><br>

        <label for="role">Rôle</label>
        <select name="role" required>
            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Utilisateur</option>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
        </select><br>

        <button type="submit">Mettre à jour</button>
    </form>

    <br><a href="admin_users.php">← Retour à la gestion des utilisateurs</a>
</body>
</html>

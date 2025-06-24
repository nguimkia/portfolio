<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$msg = '';

// Récupérer les informations actuelles de l'utilisateur
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // Vérifier si un mot de passe a été changé
    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
        $stmt->execute([$username, $email, $password_hash, $user_id]);
        $msg = "✅ Profil mis à jour avec succès !";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $user_id]);
        $msg = "✅ Profil mis à jour avec succès !";
    }

    // Mettre à jour la session
    $_SESSION['user']['username'] = $username;
    $_SESSION['user']['email'] = $email;

    header('Location: edit_profile.php?updated=1');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Profil</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Modifier mon profil</h2>
    
    <?php if (isset($_GET['updated'])): ?>
        <p class="success"><?= $msg ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="username">Nom d'utilisateur</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br>

        <label for="email">Adresse email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>

        <label for="password">Nouveau mot de passe (optionnel)</label>
        <input type="password" id="password" name="password"><br>

        <button type="submit">Mettre à jour</button>
    </form>
    
    <br>
    <a href="dashboard_user.php">← Retour au tableau de bord</a>
</body>
</html>

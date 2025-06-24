<?php
session_start();
require 'includes/db.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user'] = $user;
        if ($user['role'] == 'admin') {
            header('Location: dashboard_admin.php');
        } else {
            header('Location: accueil.php');
        }
        exit;
    } else {
        $msg = "Identifiants incorrects.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
    <link rel="stylesheet" href="css/style.css">
    <style> 
    body {
    font-family: Arial, sans-serif;
    text-align: center;
    padding: 40px;
    background-image: url('Transform Your Home Office with These 20 Organization Ideas for Maximum Productivity!.jpeg');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    }
    </style>
</head>
<body>
    <h2>Connexion</h2>
    <form method="post">
        <input type="email" name="email" placeholder="Adresse email" required><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br>
        <button type="submit">Se connecter</button>
    </form>
    <p><?= $msg ?></p>
</body>
</html>

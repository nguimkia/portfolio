<?php
require 'includes/db.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if ($email && $username && $_POST['password']) {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);
        $msg = "Inscription réussie. <a href='login.php'>Se connecter</a>";
    } else {
        $msg = "Tous les champs sont obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
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
    <h2>Inscription</h2>
    <form method="post">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required><br>
        <input type="email" name="email" placeholder="Adresse email" required><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br>
        <button type="submit">S'inscrire</button>
    </form>
    <p><?= $msg ?></p>
</body>
</html>

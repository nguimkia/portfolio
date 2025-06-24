<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Accueil - GED</title>
    <link rel="stylesheet" href="css/style.css">
    <style> 
    body {
    font-family: Arial, sans-serif;
    text-align: center;
    padding: 40px;
    background-image: url('31307f4d-63ea-4b42-b804-0537610f78b8.jpeg');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    }
    </style>
</head>
<body>
    <h1>Bienvenue <?= htmlspecialchars($user['username']) ?> !</h1>
    <p>Prêt à gérer vos documents efficacement ?</p>
    <a href="dashboard_user.php"><button>Commencer</button></a>
    <br><br>
    <a href="logout.php">Se déconnecter</a>
</body>
</html>

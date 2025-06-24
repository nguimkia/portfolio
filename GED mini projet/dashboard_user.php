<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Utilisateur</title>
    <link rel="stylesheet" href="css/style.css">
    <style> 
    body {
    font-family: Arial, sans-serif;
    text-align: center;
    padding: 40px;
    background-image: url('Dicas para organizar a papelada e documentos importantes_.jpeg');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    }
    </style>
</head>
<body>
    <h1>Tableau de bord - Bonjour <?= htmlspecialchars($user['username']) ?></h1>

    <nav>
        <ul>
            <li><a href="add_document.php">➕ Ajouter un document</a></li>
            <li><a href="view_documents.php">📄 Voir mes documents</a></li>
            <li><a href="search_document.php">🔍 Rechercher un document</a></li>
            <li><a href="statistics.php">📊 Statistiques</a></li>
            <li><a href="edit_profile.php">⚙️ Modifier mon profil</a></li>
            <li><a href="logout.PHP">🚪 Déconnexion</a></li>
        </ul>
    </nav>
</body>
</html>

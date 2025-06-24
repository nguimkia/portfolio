<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("SELECT * FROM documents WHERE user_id = ? ORDER BY uploaded_at DESC");
$stmt->execute([$user_id]);
$documents = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mes Documents</title>
    <link rel="stylesheet" href="css/style.css">
    <style> 
    body {
    font-family: Arial, sans-serif;
    text-align: center;
    padding: 40px;
    background-image: url('WhatsApp Image 2025-04-23 à 18.21.11_dcfb075a.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    }
    </style>
</head>
<body>
    <h2>📁 Mes documents</h2>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Type</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($documents as $doc): ?>
                <tr>
                    <td><?= htmlspecialchars($doc['name']) ?></td>
                    <td><?= htmlspecialchars($doc['description']) ?></td>
                    <td><?= htmlspecialchars($doc['type']) ?></td>
                    <td><?= $doc['uploaded_at'] ?></td>
                    <td>
                        <a href="<?= $doc['file_path'] ?>" download>📥</a>
                        <a href="<?= $doc['file_path'] ?>" target="_blank" onclick="window.print(); return false;">🖨️</a>
                        <a href="delete_document.php?id=<?= $doc['id'] ?>" onclick="return confirm('Supprimer ce document ?')">🗑️</a>
                        <a href="edit_document.php?id=<?= $doc['id'] ?>">✏️</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <a href="dashboard_user.php">← Retour au tableau de bord</a>
</body>
</html>

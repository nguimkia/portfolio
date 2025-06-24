<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];
$keyword = '';
$documents = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keyword = htmlspecialchars($_POST['keyword']);
    $query = "SELECT * FROM documents 
              WHERE user_id = ? 
              AND (name LIKE ? OR description LIKE ? OR type LIKE ?)
              ORDER BY uploaded_at DESC";

    $stmt = $pdo->prepare($query);
    $like = "%" . $keyword . "%";
    $stmt->execute([$user_id, $like, $like, $like]);
    $documents = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Recherche de documents</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>🔍 Rechercher un document</h2>
    <form method="POST">
        <input type="text" name="keyword" placeholder="Mot-clé..." value="<?= $keyword ?>" required>
        <button type="submit">Rechercher</button>
    </form>

    <?php if (!empty($documents)): ?>
        <h3>Résultats de recherche :</h3>
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
                            <a href="delete_document.php?id=<?= $doc['id'] ?>">🗑️</a>
                            <a href="edit_document.php?id=<?= $doc['id'] ?>">✏️</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <p>Aucun document trouvé pour : <strong><?= $keyword ?></strong></p>
    <?php endif; ?>

    <br>
    <a href="dashboard_user.php">← Retour au tableau de bord</a>
</body>
</html>

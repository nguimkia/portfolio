<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];

if (!isset($_GET['id'])) {
    echo "ID manquant.";
    exit;
}

$doc_id = $_GET['id'];

// Récupération du document
$stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ? AND user_id = ?");
$stmt->execute([$doc_id, $user_id]);
$doc = $stmt->fetch();

if (!$doc) {
    echo "Document non trouvé.";
    exit;
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $type = htmlspecialchars($_POST['type']);

    // Vérifie s’il y a un nouveau fichier
    if ($_FILES['file']['error'] === 0) {
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'docx'];
        if (in_array($ext, $allowed)) {
            // Supprimer l’ancien fichier
            if (file_exists($doc['file_path'])) {
                unlink($doc['file_path']);
            }
            $new_path = "uploads/" . uniqid() . "." . $ext;
            move_uploaded_file($_FILES['file']['tmp_name'], $new_path);

            $stmt = $pdo->prepare("UPDATE documents SET name=?, description=?, type=?, file_path=? WHERE id=? AND user_id=?");
            $stmt->execute([$name, $description, $type, $new_path, $doc_id, $user_id]);
        } else {
            $msg = "❌ Format de fichier invalide.";
        }
    } else {
        // Mise à jour sans changer de fichier
        $stmt = $pdo->prepare("UPDATE documents SET name=?, description=?, type=? WHERE id=? AND user_id=?");
        $stmt->execute([$name, $description, $type, $doc_id, $user_id]);
    }

    $msg = "✅ Document mis à jour.";
    header("Location: view_documents.php?updated=1");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modifier Document</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>✏️ Modifier le document</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" value="<?= htmlspecialchars($doc['name']) ?>" required><br>
        <textarea name="description" required><?= htmlspecialchars($doc['description']) ?></textarea><br>
        <select name="type" required>
            <option value="PDF" <?= $doc['type'] === 'PDF' ? 'selected' : '' ?>>PDF</option>
            <option value="Image" <?= $doc['type'] === 'Image' ? 'selected' : '' ?>>Image</option>
            <option value="Texte" <?= $doc['type'] === 'Texte' ? 'selected' : '' ?>>Texte</option>
            <option value="Autre" <?= $doc['type'] === 'Autre' ? 'selected' : '' ?>>Autre</option>
        </select><br>
        <label>Changer de fichier (optionnel) :</label>
        <input type="file" name="file"><br>
        <button type="submit">✅ Enregistrer</button>
    </form>
    <p><?= $msg ?></p>
    <br>
    <a href="view_documents.php">← Retour à mes documents</a>
</body>
</html>

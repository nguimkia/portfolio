<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user']['id'];
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $type = htmlspecialchars($_POST['type']);
    $file = $_FILES['file'];

    if ($file['error'] === 0) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'docx'];
        if (in_array($ext, $allowed)) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir);
            $file_name = uniqid() . "." . $ext;
            $path = $target_dir . $file_name;

            move_uploaded_file($file['tmp_name'], $path);

            $stmt = $pdo->prepare("INSERT INTO documents (user_id, name, description, type, file_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $name, $description, $type, $path]);

            $msg = "📁 Document ajouté avec succès.";
        } else {
            $msg = "❌ Type de fichier non autorisé.";
        }
    } else {
        $msg = "❌ Erreur lors de l'upload.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Document</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>➕ Ajouter un document</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Nom du document" required><br>
        <textarea name="description" placeholder="Description du document" required></textarea><br>
        <select name="type" required>
            <option value="">Choisir un type</option>
            <option value="PDF">PDF</option>
            <option value="Image">Image</option>
            <option value="Texte">Texte</option>
            <option value="Autre">Autre</option>
        </select><br>
        <input type="file" name="file" required><br>
        <button type="submit">Ajouter</button>
    </form>
    <p><?= $msg ?></p>
    <br>
    <a href="dashboard_user.php">← Retour au tableau de bord</a>
</body>
</html>

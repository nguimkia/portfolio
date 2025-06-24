<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $doc_id = $_GET['id'];
    $user_id = $_SESSION['user']['id'];

    // Récupérer le chemin du fichier
    $stmt = $pdo->prepare("SELECT file_path FROM documents WHERE id = ? AND user_id = ?");
    $stmt->execute([$doc_id, $user_id]);
    $doc = $stmt->fetch();

    if ($doc) {
        // Supprimer le fichier du dossier
        if (file_exists($doc['file_path'])) {
            unlink($doc['file_path']);
        }

        // Supprimer le document de la base
        $stmt = $pdo->prepare("DELETE FROM documents WHERE id = ? AND user_id = ?");
        $stmt->execute([$doc_id, $user_id]);

        header("Location: view_documents.php?deleted=1");
        exit;
    } else {
        echo "Document non trouvé ou accès refusé.";
    }
} else {
    echo "ID du document manquant.";
}

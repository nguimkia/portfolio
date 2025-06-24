<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include_once("config/db.php");

// Nombre total de documents de l'utilisateur
$sql_total = "SELECT COUNT(*) as total_docs FROM documents WHERE user_id = ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->execute([$_SESSION['user_id']]);
$total_docs = $stmt_total->fetch(PDO::FETCH_ASSOC)['total_docs'];

// Nombre de documents par type
$sql_by_type = "SELECT type, COUNT(*) as count FROM documents WHERE user_id = ? GROUP BY type";
$stmt_by_type = $conn->prepare($sql_by_type);
$stmt_by_type->execute([$_SESSION['user_id']]);
$type_counts = array();
while ($row = $stmt_by_type->fetch(PDO::FETCH_ASSOC)) {
    $type_counts[$row['type']] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 30px;
        }
        .stats-box {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
        }
        .legend {
            margin-top: 20px;
        }
        .legend div {
            margin-bottom: 10px;
        }
        .pdf, .img, .txt {
            display: inline-block;
            width: 15px;
            height: 15px;
            margin-right: 8px;
        }
        .pdf { background: #3498db; }
        .img { background: #e67e22; }
        .txt { background: #2ecc71; }
    </style>
</head>
<body>

<div class="stats-box">
    <h2>📊 Statistiques de vos documents</h2>
    <p>Total de documents : <strong><?php echo $total_docs; ?></strong></p>

    <canvas id="chartTypes" width="400" height="400"></canvas>

    <div class="legend">
        <div><span class="pdf"></span> PDF (<?php echo isset($type_counts['PDF']) ? $type_counts['PDF'] : 0; ?>)</div>
        <div><span class="img"></span> Image (<?php echo isset($type_counts['Image']) ? $type_counts['Image'] : 0; ?>)</div>
        <div><span class="txt"></span> Texte (<?php echo isset($type_counts['Texte']) ? $type_counts['Texte'] : 0; ?>)</div>
    </div>

    <br><a href="dashboard_user.php">← Retour au tableau de bord</a>
</div>

<script>
    const ctx = document.getElementById('chartTypes').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_keys($type_counts)); ?>,
            datasets: [{
                label: 'Documents par type',
                data: <?php echo json_encode(array_values($type_counts)); ?>,
                backgroundColor: ['#3498db', '#e67e22', '#2ecc71']
            }]
        }
    });
</script>

</body>
</html>

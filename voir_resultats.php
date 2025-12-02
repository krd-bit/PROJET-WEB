<?php
session_start();
require 'db.php';

if(!isset($_GET['id'])) header("Location: dashboard_prof.php");
$quiz_id = $_GET['id'];

// On récupère le nom des élèves et leur score
$sql = "SELECT users.email, results.score, results.date_played 
        FROM results 
        JOIN users ON results.user_id = users.id 
        WHERE results.quiz_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$quiz_id]);
$notes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h2>Résultats du Quiz</h2>
    <a href="dashboard_prof.php">Retour</a>
    <table>
        <tr><th>Élève (Email)</th><th>Note</th><th>Date</th></tr>
        <?php foreach($notes as $n): ?>
        <tr>
            <td><?php echo $n['email']; ?></td>
            <td><?php echo $n['score']; ?></td>
            <td><?php echo $n['date_played']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
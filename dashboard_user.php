<?php
session_start();
require 'db.php';
// On prend tous les quiz "live"
$quizzes = $pdo->query("SELECT * FROM quizzes WHERE status = 'live'")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
<nav><span>Espace Élève</span><a href="deconnexion.php">Déconnexion</a></nav>
<div class="container">
    <h2>Quiz disponibles</h2>
    <?php foreach($quizzes as $q): ?>
        <div class="card">
            <h3><?php echo $q['title']; ?></h3>
            <a href="repondre.php?id=<?php echo $q['id']; ?>" class="btn">Répondre</a>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
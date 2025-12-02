<?php
session_start();
require 'db.php';
// On ne montre que les quiz EN LIGNE (live)
$quizzes = $pdo->query("SELECT * FROM quizzes WHERE status = 'live'")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
<nav>
    <a href="dashboard_user.php" class="logo-brand">
        <span class="l-q">Q</span><span class="l-z">ui</span><span class="l-z">zz</span><span class="l-z">e</span><span class="l-o">o</span>
    </a>
    <div>
        <a href="profil.php">Mon Profil</a>
        <a href="deconnexion.php">Déconnexion</a>
    </div>
</nav>

<div class="container">
    <h2>Quiz disponibles</h2>
    <?php foreach($quizzes as $q): ?>
        <div class="card">
            <h3><?php echo $q['title']; ?></h3>
            <a href="repondre.php?id=<?php echo $q['id']; ?>" class="btn">Répondre au quiz</a>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
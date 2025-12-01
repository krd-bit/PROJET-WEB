<?php
session_start();
require 'db.php';
// Vérif sécurité simple
if (!in_array($_SESSION['user']['role'], ['school', 'company'])) header("Location: connexion.php");

$my_id = $_SESSION['user']['id'];
$quizzes = $pdo->query("SELECT * FROM quizzes WHERE author_id = $my_id")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
<nav>
    <span>Espace <?php echo $_SESSION['user']['company_name']; ?></span>
    <div><a href="creer_quiz.php">Nouveau Quiz</a> <a href="deconnexion.php">Déconnexion</a></div>
</nav>
<div class="container">
    <h2>Mes Quiz</h2>
    <?php foreach($quizzes as $q): ?>
        <div class="card">
            <h3><?php echo $q['title']; ?> (Statut: <?php echo $q['status']; ?>)</h3>
            <?php if($q['status'] == 'building'): ?>
                <a href="ajouter_question.php?id=<?php echo $q['id']; ?>">+ Ajouter Questions</a> | 
                <a href="publier_quiz.php?id=<?php echo $q['id']; ?>">Mettre en ligne</a>
            <?php else: ?>
                <a href="resultats.php?id=<?php echo $q['id']; ?>">Voir les résultats</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
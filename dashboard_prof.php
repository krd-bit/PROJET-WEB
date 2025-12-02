<?php
session_start();
require 'db.php';
if (!in_array($_SESSION['user']['role'], ['school', 'company'])) header("Location: connexion.php");

$my_id = $_SESSION['user']['id'];
// Requête qui compte aussi les réponses
$sql = "SELECT quizzes.*, COUNT(results.id) as nb_reponses 
        FROM quizzes 
        LEFT JOIN results ON quizzes.id = results.quiz_id 
        WHERE author_id = ? 
        GROUP BY quizzes.id";
$stmt = $pdo->prepare($sql);
$stmt->execute([$my_id]);
$quizzes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
<nav>
    <a href="dashboard_prof.php" class="logo-brand">
        <span class="l-q">Q</span><span class="l-z">ui</span><span class="l-z">zz</span><span class="l-z">e</span><span class="l-o">o</span>
    </a>

    <div style="display:flex; align-items:center;">
        <span style="margin-right:15px; font-size:14px;">Espace <?php echo htmlspecialchars($_SESSION['user']['company_name']); ?></span>
        <a href="profil.php">Profil</a>
        <a href="creer_quiz.php">Nouveau Quiz</a> 
        <a href="deconnexion.php" style="background:#f6d87d; padding:5px 10px; border-radius:15px; color:#333;">Déconnexion</a>
    </div>
</nav>

<div class="container">
    <h2>Gestion de mes Quiz</h2>
    <?php if(count($quizzes) == 0) echo "<p>Vous n'avez pas encore créé de quiz.</p>"; ?>
    
    <?php foreach($quizzes as $q): ?>
        <div class="card">
            <div style="display:flex; justify-content:space-between;">
                <h3><?php echo htmlspecialchars($q['title']); ?></h3>
                <span style="color:#555;">👥 <?php echo $q['nb_reponses']; ?> réponse(s)</span>
            </div>
            
            <p>Statut : 
                <?php 
                if($q['status']=='building') echo "<strong style='color:orange'>🚧 En construction</strong>";
                else echo "<strong style='color:green'>✅ En ligne</strong>";
                ?>
            </p>

            <div style="margin-top:10px;">
                <?php if($q['status'] == 'building'): ?>
                    <a href="ajouter_question.php?id=<?php echo $q['id']; ?>" class="btn">Modifier</a>
                    <a href="publier_quiz.php?id=<?php echo $q['id']; ?>" class="btn" style="background:#9b8cce">Publier</a>
                <?php else: ?>
                    <a href="voir_resultats.php?id=<?php echo $q['id']; ?>" class="btn">Voir Résultats</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
<?php
session_start();
require 'db.php';

// SÉCURITÉ : Vérifie qu'un quiz est sélectionné
if (!isset($_GET['id'])) {
    die("Erreur : Aucun quiz choisi. <a href='dashboard_user.php'>Retour</a>");
}

$quiz_id = $_GET['id'];

// Récupération des questions
$stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll();

// Traitement des réponses
if (isset($_POST['finish'])) {
    $score = 0;
    if (isset($_POST['rep'])) {
        foreach($_POST['rep'] as $q_id => $choice_id) {
            // Vérification si la réponse est correcte
            $check = $pdo->prepare("SELECT is_correct, points FROM choices c JOIN questions q ON c.question_id = q.id WHERE c.id = ?");
            $check->execute([$choice_id]);
            $res = $check->fetch();
            if ($res && $res['is_correct']) $score += $res['points'];
        }
    }
    
    // Sauvegarde du résultat
    $pdo->prepare("INSERT INTO results (user_id, quiz_id, score) VALUES (?, ?, ?)")
        ->execute([$_SESSION['user']['id'], $quiz_id, $score]);
        
    die("<div class='container'><h1>Terminé ! Score : $score</h1><a href='dashboard_user.php'>Retour</a></div><link rel='stylesheet' href='style.css'>");
}
?>

<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <form method="post">
        <?php foreach($questions as $q): ?>
            <div class="card">
                <h3><?php echo $q['question_text']; ?> (<?php echo $q['points']; ?> pts)</h3>
                <?php 
                $choix = $pdo->prepare("SELECT * FROM choices WHERE question_id = ?");
                $choix->execute([$q['id']]);
                foreach($choix->fetchAll() as $c): ?>
                    <input type="radio" name="rep[<?php echo $q['id']; ?>]" value="<?php echo $c['id']; ?>">
                    <?php echo $c['choice_text']; ?><br>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit" name="finish" class="btn">Valider</button>
    </form>
</div>
</body>
</html>
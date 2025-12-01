<?php
session_start();
require 'db.php';
$quiz_id = $_GET['id'];

// On récupère les questions
$stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll();

if (isset($_POST['finish'])) {
    $score = 0;
    // Calcul du score "maison"
    foreach($_POST['reponse'] as $q_id => $choice_id) {
        // On vérifie si le choix est bon
        $chk = $pdo->prepare("SELECT is_correct, points FROM choices c JOIN questions q ON q.id=c.question_id WHERE c.id=?");
        $chk->execute([$choice_id]);
        $res = $chk->fetch();
        if ($res && $res['is_correct']) $score += $res['points'];
    }
    
    // Sauvegarde
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
                // On cherche les choix pour cette question
                $choix = $pdo->prepare("SELECT * FROM choices WHERE question_id = ?");
                $choix->execute([$q['id']]);
                foreach($choix->fetchAll() as $c): ?>
                    <input type="radio" name="reponse[<?php echo $q['id']; ?>]" value="<?php echo $c['id']; ?>">
                    <?php echo $c['choice_text']; ?><br>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit" name="finish" class="btn">Envoyer mes réponses</button>
    </form>
</div>
</body>
</html>
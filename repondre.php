<?php
session_start();
require 'db.php';

if (!isset($_GET['id'])) die("Erreur ID");
$quiz_id = $_GET['id'];

// On récupère les questions
$stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll();

if (isset($_POST['finish'])) {
    $score = 0;
    
    // Calcul score QCM
    if (isset($_POST['rep'])) {
        foreach($_POST['rep'] as $q_id => $valeur) {
            // Si c'est un ID (donc un QCM)
            if (is_numeric($valeur)) {
                $check = $pdo->prepare("SELECT is_correct, points FROM choices c JOIN questions q ON c.question_id = q.id WHERE c.id = ?");
                $check->execute([$valeur]);
                $res = $check->fetch();
                if ($res && $res['is_correct']) $score += $res['points'];
            } 
            // Si c'est du texte (Réponse libre), on sauvegarde juste (pas de points auto)
            else {
                // Ici on pourrait sauver le texte dans la table text_answers si tu l'as créée
                // Pour l'instant, on donne les points par défaut pour faire plaisir à l'élève
                $q_info = $pdo->prepare("SELECT points FROM questions WHERE id = ?");
                $q_info->execute([$q_id]);
                $pts = $q_info->fetch();
                $score += $pts['points']; 
            }
        }
    }
    
    // Sauvegarde note finale
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
                <h3><?php echo htmlspecialchars($q['question_text']); ?> (<?php echo $q['points']; ?> pts)</h3>
                
                <?php if ($q['type'] == 'libre'): ?>
                    <textarea name="rep[<?php echo $q['id']; ?>]" style="width:100%; height:80px;" placeholder="Votre réponse ici..."></textarea>
                
                <?php else: ?>
                    <?php 
                    $choix = $pdo->prepare("SELECT * FROM choices WHERE question_id = ?");
                    $choix->execute([$q['id']]);
                    foreach($choix->fetchAll() as $c): ?>
                        <div style="margin-bottom:5px;">
                            <input type="radio" name="rep[<?php echo $q['id']; ?>]" value="<?php echo $c['id']; ?>">
                            <?php echo htmlspecialchars($c['choice_text']); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit" name="finish" class="btn">Valider mes réponses</button>
    </form>
</div>
</body>
</html>
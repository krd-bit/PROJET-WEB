<?php
session_start();
require 'db.php';
$quiz_id = $_GET['id'];

if (isset($_POST['add'])) {
    // 1. Créer la question
    $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, points) VALUES (?, ?, ?)");
    $stmt->execute([$quiz_id, $_POST['question'], $_POST['points']]);
    $q_id = $pdo->lastInsertId();

    // 2. Créer les 2 choix (simplifié pour le projet étudiant)
    $stmt = $pdo->prepare("INSERT INTO choices (question_id, choice_text, is_correct) VALUES (?, ?, ?)");
    
    // Choix 1 (Bonne réponse)
    $stmt->execute([$q_id, $_POST['rep1'], 1]);
    // Choix 2 (Mauvaise réponse)
    $stmt->execute([$q_id, $_POST['rep2'], 0]);

    $msg = "Question ajoutée !";
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h2>Ajouter une question</h2>
    <?php if(isset($msg)) echo "<div class='alert'>$msg</div>"; ?>
    
    <form method="post">
        <label>Question :</label>
        <input type="text" name="question" required>
        
        <label>Points :</label>
        <input type="number" name="points" value="1" style="width:50px">

        <label>Bonne réponse :</label>
        <input type="text" name="rep1" required>

        <label>Mauvaise réponse :</label>
        <input type="text" name="rep2" required>

        <button type="submit" name="add" class="btn">Ajouter la question</button>
    </form>
    <br>
    <a href="dashboard_prof.php" class="btn">Terminer / Retour</a>
</div>
</body>
</html>
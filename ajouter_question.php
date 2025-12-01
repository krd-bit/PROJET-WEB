<?php
session_start();
require 'db.php';

// SÉCURITÉ : Si pas d'ID de quiz, on renvoie au dashboard
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard_prof.php");
    exit();
}

$quiz_id = $_GET['id'];
$msg = "";

if (isset($_POST['add'])) {
    // 1. On insère la question
    $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, points) VALUES (?, ?, ?)");
    $stmt->execute([$quiz_id, $_POST['question'], $_POST['points']]);
    $q_id = $pdo->lastInsertId();

    // 2. On insère la BONNE réponse (is_correct = 1)
    $stmt = $pdo->prepare("INSERT INTO choices (question_id, choice_text, is_correct) VALUES (?, ?, ?)");
    $stmt->execute([$q_id, $_POST['bonne_rep'], 1]);

    // 3. On insère la MAUVAISE réponse (is_correct = 0)
    $stmt->execute([$q_id, $_POST['mauvaise_rep'], 0]);

    $msg = "Question ajoutée !";
}
?>

<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h2>Ajouter une question</h2>
    <?php if($msg) echo "<div class='alert'>$msg</div>"; ?>
    
    <form method="post">
        <label>Question :</label>
        <input type="text" name="question" required>
        <label>Points :</label>
        <input type="number" name="points" value="1" style="width:50px">
        
        <label style="color:green">Bonne réponse :</label>
        <input type="text" name="bonne_rep" required>
        
        <label style="color:red">Mauvaise réponse :</label>
        <input type="text" name="mauvaise_rep" required>

        <button type="submit" name="add" class="btn">Enregistrer la question</button>
    </form>
    <br>
    <a href="dashboard_prof.php" class="btn" style="background:#555">Terminer et Retour</a>
</div>
</body>
</html>
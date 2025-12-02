<?php
session_start();
require 'db.php';

if (!isset($_GET['id'])) { header("Location: dashboard_prof.php"); exit(); }
$quiz_id = $_GET['id'];
$msg = "";

if (isset($_POST['add'])) {
    $type = $_POST['type_question']; // 'qcm' ou 'libre'

    // 1. On insère la question avec son TYPE
    $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, points, type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$quiz_id, $_POST['question'], $_POST['points'], $type]);
    $q_id = $pdo->lastInsertId();

    // 2. Si c'est un QCM, on ajoute les choix
    if ($type == 'qcm') {
        $stmt = $pdo->prepare("INSERT INTO choices (question_id, choice_text, is_correct) VALUES (?, ?, ?)");
        $stmt->execute([$q_id, $_POST['bonne_rep'], 1]);
        $stmt->execute([$q_id, $_POST['mauvaise_rep'], 0]);
    }
    // Si c'est 'libre', on n'ajoute pas de choix dans la table choices

    $msg = "Question ($type) ajoutée !";
}
?>

<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h2>Ajouter une question</h2>
    <?php if($msg) echo "<div class='alert' style='background:#d4edda; color:green;'>$msg</div>"; ?>
    
    <form method="post">
        <label>Type de question :</label>
        <select name="type_question">
            <option value="qcm">QCM (Choix multiples)</option>
            <option value="libre">Réponse Libre (Texte)</option>
        </select>

        <label>Question :</label>
        <input type="text" name="question" placeholder="Posez votre question..." required>
        
        <label>Points :</label>
        <input type="number" name="points" value="1" style="width:60px">
        
        <div style="background:#f9f9f9; padding:10px; border:1px solid #ddd; margin-top:10px;">
            <strong>Uniquement pour QCM :</strong>
            <label style="color:green">Bonne réponse :</label>
            <input type="text" name="bonne_rep" placeholder="Réponse correcte">
            
            <label style="color:red">Mauvaise réponse :</label>
            <input type="text" name="mauvaise_rep" placeholder="Réponse fausse">
        </div>

        <button type="submit" name="add" class="btn">Enregistrer</button>
    </form>
    <br>
    <a href="publier_quiz.php?id=<?php echo $quiz_id; ?>" class="btn" style="background:#555">Terminer et Mettre en ligne</a>
</div>
</body>
</html>
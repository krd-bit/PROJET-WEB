<?php
session_start();
require 'db.php';

if (isset($_POST['ok'])) {
    $stmt = $pdo->prepare("INSERT INTO quizzes (title, author_id, status) VALUES (?, ?, 'building')");
    $stmt->execute([$_POST['titre'], $_SESSION['user']['id']]);
    $id = $pdo->lastInsertId();
    header("Location: ajouter_question.php?id=$id"); // On part ajouter les questions
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h2>Nouveau Quiz</h2>
    <form method="post">
        <input type="text" name="titre" placeholder="Titre du quiz" required>
        <button type="submit" name="ok" class="btn">Créer et ajouter des questions</button>
    </form>
</div>
</body>
</html>
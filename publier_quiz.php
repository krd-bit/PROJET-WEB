<?php
require 'db.php';
// On passe le statut en "live"
if(isset($_GET['id'])) {
    $pdo->prepare("UPDATE quizzes SET status = 'live' WHERE id = ?")->execute([$_GET['id']]);
}
header("Location: dashboard_prof.php");
?>
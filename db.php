<?php
// On se connecte à la base. Si ça plante, on arrête tout.
try {
    $pdo = new PDO("mysql:host=localhost;dbname=quizzeo_db", "root", "Abdellahi00@");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Erreur BDD : " . $e->getMessage());
}
?>
<?php
try {
    $pdo = new PDO("mysql:localhost=127.0.0.1;dbname=quizzeo_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>
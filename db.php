<?php
try {
   
    $pdo = new PDO("mysql:host=localhost;dbname=quizzeo_db;charset=utf8", "root", "");
    
    // On active les erreurs pour voir les problèmes SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
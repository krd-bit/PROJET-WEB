<?php
try {
    // CONFIGURATION SPECIALE WAMP (Port 3307)
    $host = '127.0.0.1';
    $port = '3307';      // <--- C'est la clé ! On force le port 3307
    $db   = 'quizzeo_db';
    $user = 'root';
    $pass = '';          // Essaie vide d'abord (MariaDB sur Wamp n'a souvent pas de MDP)
    
    // Si tu es SÛR que ton mot de passe est Ibrakid213%, décommente la ligne dessous :
    // $pass = 'Ibrakid213%'; 

    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (Exception $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
<?php
// On force l'affichage de toutes les erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Diagnostic Quizzeo</h1>";

try {
    // On essaie d'inclure ton fichier de connexion
    if (!file_exists('db.php')) {
        die("<h3 style='color:red'>❌ Le fichier db.php est introuvable !</h3>");
    }
    require 'db.php';
    echo "<p style='color:green'>✅ Connexion au fichier db.php réussie.</p>";

    // Test de la connexion réelle à la base
    if (isset($pdo)) {
        echo "<p style='color:green'>✅ Connexion à la base de données : RÉUSSIE</p>";
        
        // On affiche le nom de la base connectée
        $db_name = $pdo->query('SELECT DATABASE()')->fetchColumn();
        echo "<p>📂 Nom de la base connectée : <strong>$db_name</strong></p>";

        // On liste les tables qui existent vraiment
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h3>📋 Liste des tables trouvées :</h3>";
        if (empty($tables)) {
            echo "<p style='color:red; font-weight:bold; font-size:18px;'>❌ ALERTE : Ta base de données est VIDE !</p>";
            echo "<p>Tu es connecté à la bonne base, mais tu n'as pas exécuté le script SQL (les CREATE TABLE).</p>";
        } else {
            echo "<ul>";
            foreach ($tables as $t) {
                echo "<li>$t</li>";
            }
            echo "</ul>";
            
            // Vérification spécifique
            $manquantes = [];
            $obligatoires = ['users', 'quizzes', 'questions', 'choices', 'results'];
            foreach ($obligatoires as $o) {
                if (!in_array($o, $tables)) {
                    $manquantes[] = $o;
                }
            }

            if (count($manquantes) > 0) {
                echo "<p style='color:red; font-weight:bold;'>❌ Il manque ces tables : " . implode(', ', $manquantes) . "</p>";
            } else {
                echo "<p style='color:green; font-weight:bold; font-size:18px;'>✅ TOUTES LES TABLES SONT LÀ !</p>";
                echo "<p>Si tu as encore une erreur, c'est que tu ouvres mal la page (voir Étape 2).</p>";
            }
        }

    }

} catch (Exception $e) {
    echo "<h3 style='color:red'>❌ Erreur critique :</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
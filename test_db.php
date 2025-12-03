<?php
// Fichier: test_db.php
require 'db.php';

echo "<h1>Diagnostic Base de Données</h1>";

// 1. Lister les tables
echo "<h3>1. Liste des tables trouvées :</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if ($tables) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li><strong>$table</strong> : ";
            // Lister les colonnes de cette table
            $cols = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_COLUMN);
            echo implode(', ', $cols);
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color:red'>Aucune table trouvée !</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Erreur connexion BDD : " . $e->getMessage() . "</p>";
}

// 2. Voir le contenu de la table QUESTIONS
if (in_array('questions', $tables)) {
    echo "<h3>2. Contenu de la table 'questions' (5 dernières) :</h3>";
    $q = $pdo->query("SELECT * FROM questions ORDER BY id DESC LIMIT 5");
    $rows = $q->fetchAll(PDO::FETCH_ASSOC);
    if ($rows) {
        echo "<table border='1' cellpadding='5'><thead><tr>";
        foreach (array_keys($rows[0]) as $col) echo "<th>$col</th>";
        echo "</tr></thead><tbody>";
        foreach ($rows as $row) {
            echo "<tr>";
            foreach ($row as $val) echo "<td>$val</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>La table 'questions' est vide.</p>";
    }
}

echo "<h3>3. Test d'accès URL</h3>";
echo "L'URL actuelle de ce fichier est : " . $_SERVER['REQUEST_URI'];
?>
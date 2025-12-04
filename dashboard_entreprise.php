<?php
session_start();
require 'db.php';

// --- 1. SÉCURITÉ ---
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// --- 2. REQUÊTE SQL AVEC LES BONS NOMS DE COLONNES ---
// On utilise 'author_id' car c'est le nom dans ta table 'quizzes' sur ton graphique
$sql = "SELECT q.id, q.title, q.status, COUNT(r.id) as nb_reponses 
        FROM quizzes q 
        LEFT JOIN results r ON q.id = r.quiz_id 
        WHERE q.author_id = ? 
        GROUP BY q.id 
        ORDER BY q.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$mes_quizzes = $stmt->fetchAll();

// Fonction simple pour la couleur du statut
function getStatusColor($status) {
    if ($status == 'termine') return '#f4aeb4'; // Rose (Terminé)
    if ($status == 'lance') return '#9ae6b4';   // Vert (Lancé)
    return '#f6d87d';                           // Jaune (Brouillon/Autre)
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Entreprise</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Style simple pour la grille */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .quiz-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-top: 5px solid var(--purple); /* Couleur entreprise */
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--purple);
        }
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            color: #333;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

<nav>
    <a href="index.php" class="logo-brand">
        <span class="l-q">Q</span><span class="l-z">U</span><span class="l-o">I</span><span class="l-q">Z</span><span class="l-z">Z</span><span class="l-o">E</span><span class="l-z">O</span>
    </a>
    <div>
        <span>🏢 Espace Entreprise</span>
        <a href="deconnexion.php" style="margin-left:15px; text-decoration:underline;">Déconnexion</a>
    </div>
</nav>

<div class="container">
    
    <div class="header-flex">
        <h2>Mes Sondages</h2>
        <a href="creer_quiz.php" class="btn">＋ Créer un sondage</a>
    </div>

    <?php if (count($mes_quizzes) == 0): ?>
        <div class="card" style="text-align:center; padding:40px;">
            <p>Vous n'avez pas encore créé de sondage.</p>
            <a href="creer_quiz.php" class="btn">Lancer mon premier sondage</a>
        </div>
    <?php else: ?>
        
        <div class="grid-container">
            <?php foreach ($mes_quizzes as $quiz): ?>
                
                <div class="quiz-card">
                    <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom:15px;">
                        <h3 style="margin:0; font-size:1.1rem;"><?php echo htmlspecialchars($quiz['title']); ?></h3>
                        
                        <span class="badge" style="background-color: <?php echo getStatusColor($quiz['status']); ?>">
                            <?php echo $quiz['status']; ?>
                        </span>
                    </div>

                    <div style="margin-bottom:20px;">
                        <span style="color:#666; font-size:0.9rem;">Réponses collectées</span><br>
                        <span class="stat-number"><?php echo $quiz['nb_reponses']; ?></span>
                    </div>

                    <div style="display:flex; gap:10px;">
                        <a href="voir_stats.php?id=<?php echo $quiz['id']; ?>" class="btn" style="flex:1; text-align:center; font-size:0.9rem;">📊 Analyser</a>
                        <a href="ajouter_question.php?id=<?php echo $quiz['id']; ?>" class="btn" style="background:white; color:#666; border:1px solid #ccc; padding:10px;">⚙️</a>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>

</body>
</html>
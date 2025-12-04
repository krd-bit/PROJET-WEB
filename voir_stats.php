<?php
session_start();
require 'db.php';

// --- 1. SÉCURITÉ ---
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// On vérifie qu'un ID de quiz est fourni
if (!isset($_GET['id'])) {
    die("Erreur : Aucun quiz sélectionné.");
}
$quiz_id = $_GET['id'];

// --- 2. RÉCUPÉRATION DES INFOS DU QUIZ ---
// On vérifie aussi que le quiz appartient bien à l'utilisateur connecté (Sécurité)
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND author_id = ?");
$stmt->execute([$quiz_id, $user_id]);
$quiz = $stmt->fetch();

if (!$quiz) {
    die("<h3>Erreur : Ce quiz n'existe pas ou ne vous appartient pas.</h3><a href='index.php'>Retour</a>");
}

// --- 3. CALCUL DES STATISTIQUES ---

// A. Récupérer tous les résultats de ce quiz
$stmt = $pdo->prepare("SELECT * FROM results WHERE quiz_id = ? ORDER BY date_played DESC");
$stmt->execute([$quiz_id]);
$tous_les_resultats = $stmt->fetchAll();

// B. Calculs mathématiques
$nb_participants = count($tous_les_resultats);
$moyenne = 0;
$meilleur_score = 0;
$pire_score = 0;
$total_points_possibles = 0;

// On récupère le total des points possibles du quiz (somme des points des questions)
$stmt = $pdo->prepare("SELECT SUM(points) as total FROM questions WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$res_pts = $stmt->fetch();
$total_points_possibles = $res_pts['total'] ?? 10; // Valeur par défaut 10 pour éviter division par zéro

if ($nb_participants > 0) {
    $somme_scores = 0;
    $scores_tab = [];
    
    foreach ($tous_les_resultats as $r) {
        $somme_scores += $r['score'];
        $scores_tab[] = $r['score'];
    }
    
    $moyenne = $somme_scores / $nb_participants;
    $meilleur_score = max($scores_tab);
    $pire_score = min($scores_tab);
}

// C. Calcul du pourcentage de "Satisfaction" (ou Réussite)
$pourcentage_satisfaction = 0;
if ($total_points_possibles > 0) {
    $pourcentage_satisfaction = round(($moyenne / $total_points_possibles) * 100);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Stats - <?php echo htmlspecialchars($quiz['title']); ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* CSS SPÉCIFIQUE POUR LES STATS */
        
        /* Conteneur des cartes du haut */
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            border-bottom: 4px solid #eee;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 900;
            margin: 10px 0;
            color: var(--dark);
        }

        .stat-label {
            color: #888;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Couleurs spécifiques pour les bordures */
        .border-purple { border-bottom-color: var(--purple); }
        .border-pink { border-bottom-color: var(--pink); }
        .border-yellow { border-bottom-color: var(--yellow); }

        /* Barre de progression circulaire (style simple CSS) */
        .progress-container {
            background: #eee;
            border-radius: 50px;
            height: 25px;
            width: 100%;
            margin-top: 10px;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--purple), var(--pink));
            width: <?php echo $pourcentage_satisfaction; ?>%;
            transition: width 1s ease-in-out;
        }

        /* Tableau des participants */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: var(--purple); color: white; }
        tr:hover { background-color: #f9f9f9; }

    </style>
</head>
<body>

<nav>
    <a href="index.php" class="logo-brand">
        <span class="l-q">Q</span><span class="l-z">U</span><span class="l-o">I</span><span class="l-q">Z</span><span class="l-z">Z</span><span class="l-o">E</span><span class="l-z">O</span>
    </a>
    <div>
        <?php 
            $retour = (isset($_SESSION['role']) && $_SESSION['role'] == 'entreprise') ? 'dashboard_entreprise.php' : 'dashboard_prof.php';
        ?>
        <a href="<?php echo $retour; ?>" style="color:white; text-decoration:underline;">&larr; Retour au Dashboard</a>
    </div>
</nav>

<div class="container">
    
    <div class="header-flex">
        <div>
            <h2 style="margin:0; color: var(--dark);">Analyse des résultats</h2>
            <p style="margin:5px 0 0 0; color: var(--purple);">Sondage : <?php echo htmlspecialchars($quiz['title']); ?></p>
        </div>
        <div style="text-align:right;">
            <span class="badge" style="background:#ddd; padding:5px 10px; border-radius:10px;">ID: #<?php echo $quiz['id']; ?></span>
        </div>
    </div>

    <div class="stats-summary">
        
        <div class="stat-card border-purple">
            <div class="stat-label">Participants</div>
            <div class="stat-value" style="color:var(--purple);"><?php echo $nb_participants; ?></div>
            <small>Réponses totales</small>
        </div>

        <div class="stat-card border-yellow">
            <div class="stat-label">Score Moyen</div>
            <div class="stat-value" style="color:var(--yellow);"><?php echo round($moyenne, 1); ?> <span style="font-size:1rem; color:#ccc;">/ <?php echo $total_points_possibles; ?></span></div>
            
            <div style="font-size:0.8rem; text-align:left; margin-top:5px;">Taux de satisfaction : <strong><?php echo $pourcentage_satisfaction; ?>%</strong></div>
            <div class="progress-container">
                <div class="progress-bar"></div>
            </div>
        </div>

        <div class="stat-card border-pink">
            <div class="stat-label">Meilleur Résultat</div>
            <div class="stat-value" style="color:var(--pink);"><?php echo $meilleur_score; ?></div>
            <small>Points max atteints</small>
        </div>

    </div>

    <h3 style="margin-bottom:15px; color:var(--dark);">📋 Dernières réponses reçues</h3>
    
    <?php if ($nb_participants == 0): ?>
        <div class="card" style="text-align:center; color:#888;">
            Aucune donnée à analyser pour le moment.
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Utilisateur (ID)</th>
                        <th>Score</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tous_les_resultats as $res): ?>
                        <tr>
                            <td><?php echo $res['date_played'] ?? date('Y-m-d'); ?></td>
                            <td>
                                Utilisateur #<?php echo $res['user_id']; ?>
                            </td>
                            <td style="font-weight:bold;">
                                <?php echo $res['score']; ?> / <?php echo $total_points_possibles; ?>
                            </td>
                            <td>
                                <?php 
                                // Calcul visuel simple
                                $pct = ($res['score'] / $total_points_possibles) * 100;
                                if ($pct >= 80) echo '<span style="color:green;">Excellente</span>';
                                elseif ($pct >= 50) echo '<span style="color:orange;">Moyenne</span>';
                                else echo '<span style="color:red;">Faible</span>';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
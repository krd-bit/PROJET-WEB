<?php
session_start();
require 'db.php';

// Vérification de sécurité : Seules les écoles et entreprises peuvent accéder ici
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['school', 'company', 'ecole', 'entreprise'])) {
    header("Location: connexion.php");
    exit();
}

$my_id = $_SESSION['user']['id'];

// 1. Récupérer les Quiz et compter les réponses
$sql = "SELECT quizzes.*, COUNT(results.id) as nb_reponses 
        FROM quizzes 
        LEFT JOIN results ON quizzes.id = results.quiz_id 
        WHERE author_id = ? 
        GROUP BY quizzes.id";
$stmt = $pdo->prepare($sql);
$stmt->execute([$my_id]);
$quizzes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Tableau de bord - Quizzeo</title>
</head>
<body>

<nav>
    <a href="dashboard_prof.php" class="logo-brand">
        <span class="l-q">Q</span><span class="l-z">ui</span><span class="l-z">zz</span><span class="l-z">e</span><span class="l-o">o</span>
    </a>

    <div style="display:flex; align-items:center;">
        <span style="margin-right:15px; font-size:14px;">
            Espace <?php echo htmlspecialchars($_SESSION['user']['company_name'] ?? 'Prof'); ?>
        </span>
        <a href="profil.php">Profil</a>
        <a href="creer_quiz.php">Nouveau Quiz</a> 
        <a href="deconnexion.php" style="background:#f6d87d; padding:5px 10px; border-radius:15px; color:#333;">Déconnexion</a>
    </div>
</nav>

<div class="container">
    <h2>Gestion de mes Quiz</h2>
    
    <?php if(count($quizzes) == 0): ?>
        <p>Vous n'avez pas encore créé de quiz.</p>
        <a href="creer_quiz.php" class="btn">Créer mon premier quiz</a>
    <?php endif; ?>
    
    <?php foreach($quizzes as $q): ?>
        <div class="card" style="margin-bottom: 20px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: white;">
            <div style="display:flex; justify-content:space-between;">
                <h3><?php echo htmlspecialchars($q['title']); ?></h3>
                <span style="color:#555;">👥 <?php echo $q['nb_reponses']; ?> réponse(s)</span>
            </div>
            
            <p>Statut : 
                <?php 
                if($q['status']=='building') echo "<strong style='color:orange'>🚧 En construction</strong>";
                else echo "<strong style='color:green'>✅ En ligne</strong>";
                ?>
            </p>

            <div style="margin-top:10px;">
                <?php if($q['status'] == 'building'): ?>
                    <a href="ajouter_question.php?id=<?php echo $q['id']; ?>" class="btn">Modifier / Ajouter Questions</a>
                    <a href="publier_quiz.php?id=<?php echo $q['id']; ?>" class="btn" style="background:#9b8cce">Publier</a>
                
                <?php else: ?>
                    <a href="voir_resultats.php?id=<?php echo $q['id']; ?>" class="btn" style="background:#FFD93D; color:#333;">Voir Résultats</a>
                    
                    <?php 
                        // On détecte automatiquement le dossier actuel
                        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
                        $host = $_SERVER['HTTP_HOST']; // localhost
                        $path = dirname($_SERVER['PHP_SELF']); // Le dossier (ex: /mon_projet)
                        // Correction pour Windows qui met parfois des antislashs \
                        $path = str_replace('\\', '/', $path);
                        
                        $lien = $protocol . "://" . $host . $path . "/repondre.php?id=" . $q['id'];
                    ?>

                    <div style="margin-top: 15px; background: #f4f4f4; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                        <label style="font-weight:bold; font-size:0.9em; color:#555;">Lien à envoyer aux élèves :</label>
                        <div style="display:flex; gap:10px; margin-top:5px;">
                            <input type="text" value="<?php echo $lien; ?>" readonly style="width:100%; padding:5px; border:1px solid #ccc;">
                            <a href="repondre.php?id=<?php echo $q['id']; ?>" target="_blank" class="btn" style="padding: 5px 10px; font-size: 12px; height: 30px; line-height: 15px; background: #28a745;">Tester</a>
                        </div>
                    </div>

                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
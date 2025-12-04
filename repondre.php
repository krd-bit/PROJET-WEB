<?php
session_start();
require 'db.php';

// --- 1. SÉCURITÉ & DÉBOGAGE ---
// Affiche les erreurs pour le développement (à commenter en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérification de connexion
if (!isset($_SESSION['user']) && !isset($_SESSION['user_id'])) {
    die("<h3>Erreur : Vous devez être connecté pour répondre. <a href='connexion.php'>Se connecter</a></h3>");
}
$user_id = $_SESSION['user']['id'] ?? $_SESSION['user_id'];

if (!isset($_GET['id'])) die("Erreur : Aucun ID de quiz fourni dans l'URL.");
$quiz_id = $_GET['id'];

// --- 2. RÉCUPÉRATION DU QUIZ ET DES QUESTIONS ---
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->execute([$quiz_id]);
$quiz_info = $stmt->fetch();

if (!$quiz_info) die("Ce quiz n'existe pas.");

// On récupère toutes les questions
$stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll();

// --- 3. TRAITEMENT DU FORMULAIRE (QUAND ON CLIQUE SUR VALIDER) ---
if (isset($_POST['finish'])) {
    $score = 0;
    
    if (isset($_POST['rep'])) {
        foreach($_POST['rep'] as $q_id => $valeur) {
            // Si $valeur est numérique, c'est un ID de réponse (donc QCM)
            if (is_numeric($valeur)) {
                // On vérifie si la réponse est bonne et on récupère les points de la question
                // Note : On précise 'q.points' pour éviter les conflits si la table choices a aussi une colonne points
                $check = $pdo->prepare("
                    SELECT c.is_correct, q.points 
                    FROM choices c 
                    JOIN questions q ON c.question_id = q.id 
                    WHERE c.id = ?
                ");
                $check->execute([$valeur]);
                $res = $check->fetch();
                
                if ($res && $res['is_correct'] == 1) {
                    $score += $res['points'];
                }
            } else {
                // C'est du texte (Réponse libre) 
                // Pour simplifier ici, on donne les points automatiquement si le champ n'est pas vide
                if (!empty(trim($valeur))) {
                    $pts_stmt = $pdo->prepare("SELECT points FROM questions WHERE id = ?");
                    $pts_stmt->execute([$q_id]);
                    $p = $pts_stmt->fetch();
                    if($p) $score += $p['points'];
                }
            }
        }
    }

    // Enregistrement du résultat
    // Vérifier si l'utilisateur a déjà répondu pour éviter les doublons
    $verif = $pdo->prepare("SELECT id FROM results WHERE user_id = ? AND quiz_id = ?");
    $verif->execute([$user_id, $quiz_id]);
    
    if (!$verif->fetch()) {
        $ins = $pdo->prepare("INSERT INTO results (user_id, quiz_id, score) VALUES (?, ?, ?)");
        $ins->execute([$user_id, $quiz_id, $score]);
    }
    
    // Redirection vers le tableau de bord avec un message de succès
    header("Location: dashboard_user.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Répondre - <?php echo htmlspecialchars($quiz_info['title']); ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Un peu de CSS intégré pour que ce soit propre tout de suite */
        body { font-family: sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #218838; }
        textarea { width: 100%; border: 1px solid #ccc; border-radius: 4px; padding: 10px; font-family: inherit; }
    </style>
</head>
<body>

<div class="container">
    <div style="margin-bottom: 20px;">
        <a href="dashboard_user.php" style="text-decoration: none; color: #555;">&larr; Retour au tableau de bord</a>
    </div>

    <h2>Quiz : <?php echo htmlspecialchars($quiz_info['title']); ?></h2>
    
    <?php if (empty($questions)): ?>
        <div class="card" style="text-align: center; background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba;">
            <h3>Oups ! Aucune question trouvée.</h3>
            <p>Ce quiz ne contient pas encore de questions.</p>
        </div>
    <?php else: ?>
    
    <form method="post">
        <?php foreach($questions as $index => $q): ?>
            
            <?php 
            // --- C'EST ICI QUE LA MAGIE OPÈRE ---
            // On vérifie le nom de la colonne pour éviter le crash
            $mon_type = 'qcm'; // Valeur par défaut
            
            if (!empty($q['type_question'])) {
                $mon_type = $q['type_question'];
            } elseif (!empty($q['type'])) {
                $mon_type = $q['type'];
            }
            ?>

            <div class="card">
                <h3>
                    Question <?php echo $index + 1; ?> : 
                    <?php echo htmlspecialchars($q['question_text']); ?> 
                    <span style="float:right; font-size:0.8em; color:#666; background:#eee; padding:2px 8px; border-radius:10px;">
                        <?php echo $q['points']; ?> pts
                    </span>
                </h3>

                <?php if ($mon_type == 'libre'): ?>
                    
                    <textarea name="rep[<?php echo $q['id']; ?>]" rows="4" placeholder="Écrivez votre réponse ici..."></textarea>
                
                <?php else: ?>
                    <?php 
                    $ch = $pdo->prepare("SELECT * FROM choices WHERE question_id = ?");
                    $ch->execute([$q['id']]);
                    $les_choix = $ch->fetchAll();
                    ?>
                    
                    <?php if(count($les_choix) == 0): ?>
                        <p style="color:red; font-style:italic;">Erreur : Aucun choix configuré pour cette question.</p>
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                        <?php foreach($les_choix as $c): ?>
                            <label style="cursor:pointer; display:flex; align-items:center; background:#f9f9f9; padding:10px; border-radius:5px; border:1px solid #eee;">
                                <input type="radio" name="rep[<?php echo $q['id']; ?>]" value="<?php echo $c['id']; ?>" required style="margin-right:15px; transform: scale(1.2);">
                                <span><?php echo htmlspecialchars($c['choice_text']); ?></span>
                            </label>
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>
            </div>

        <?php endforeach; ?>

        <div style="text-align: center;">
            <button type="submit" name="finish" class="btn">Valider mes réponses</button>
        </div>
    </form>
    <?php endif; ?>
</div>

</body>
</html>
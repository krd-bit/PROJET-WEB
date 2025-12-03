<?php
session_start();
require 'db.php';

// --- 1. SÉCURITÉ & DÉBOGAGE ---
// Affichage des erreurs pour comprendre si ça plante (à retirer plus tard)
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

$stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll();

// --- 3. TRAITEMENT DU FORMULAIRE ---
if (isset($_POST['finish'])) {
    $score = 0;
    
    if (isset($_POST['rep'])) {
        foreach($_POST['rep'] as $q_id => $valeur) {
            // Si $valeur est numérique, c'est un ID de réponse (donc QCM)
            if (is_numeric($valeur)) {
                // Vérif bonne réponse
                $check = $pdo->prepare("SELECT is_correct, points FROM choices c JOIN questions q ON c.question_id = q.id WHERE c.id = ?");
                $check->execute([$valeur]);
                $res = $check->fetch();
                if ($res && $res['is_correct'] == 1) {
                    $score += $res['points'];
                }
            } else {
                // C'est du texte (Réponse libre) - On donne les points par défaut (simplification)
                $pts_stmt = $pdo->prepare("SELECT points FROM questions WHERE id = ?");
                $pts_stmt->execute([$q_id]);
                $p = $pts_stmt->fetch();
                if($p) $score += $p['points'];
            }
        }
    }

    // Enregistrement
    // Vérifier doublon
    $verif = $pdo->prepare("SELECT id FROM results WHERE user_id = ? AND quiz_id = ?");
    $verif->execute([$user_id, $quiz_id]);
    if (!$verif->fetch()) {
        $ins = $pdo->prepare("INSERT INTO results (user_id, quiz_id, score) VALUES (?, ?, ?)");
        $ins->execute([$user_id, $quiz_id, $score]);
    }
    
    // Redirection
    header("Location: dashboard_user.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Répondre - <?php echo htmlspecialchars($quiz_info['title']); ?></title>
</head>
<body>
<div class="container">
    <h2><?php echo htmlspecialchars($quiz_info['title']); ?></h2>
    
    <form method="post">
        <?php foreach($questions as $q): ?>
            
            <?php 
            // --- ASTUCE : DÉTECTION AUTOMATIQUE DU TYPE ---
            // On regarde si la colonne s'appelle 'type_question' OU 'type'
            $mon_type = 'qcm'; // Par défaut
            if (!empty($q['type_question'])) {
                $mon_type = $q['type_question'];
            } elseif (!empty($q['type'])) {
                $mon_type = $q['type'];
            }
            ?>

            <div class="card" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background: white;">
                <h3>
                    <?php echo htmlspecialchars($q['question_text']); ?> 
                    <small style="color:#666; font-size:0.7em;">(<?php echo $q['points']; ?> pts)</small>
                </h3>

                <?php if ($mon_type == 'libre'): ?>
                    <textarea name="rep[<?php echo $q['id']; ?>]" style="width:100%; height:80px; padding:10px; border:1px solid #ccc;" placeholder="Votre réponse..."></textarea>
                
                <?php else: ?>
                    <?php 
                    $ch = $pdo->prepare("SELECT * FROM choices WHERE question_id = ?");
                    $ch->execute([$q['id']]);
                    $les_choix = $ch->fetchAll();
                    ?>
                    
                    <?php if(count($les_choix) == 0): ?>
                        <p style="color:red;">Aucun choix trouvé pour cette question (Erreur configuration).</p>
                    <?php else: ?>
                        <?php foreach($les_choix as $c): ?>
                            <div style="margin-bottom: 5px;">
                                <label style="cursor:pointer; display:flex; align-items:center;">
                                    <input type="radio" name="rep[<?php echo $q['id']; ?>]" value="<?php echo $c['id']; ?>" required style="margin-right:10px;">
                                    <?php echo htmlspecialchars($c['choice_text']); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                <?php endif; ?>
            </div>

        <?php endforeach; ?>

        <button type="submit" name="finish" class="btn" style="margin-top:20px;">Valider le Quiz</button>
    </form>
</div>
</body>
</html>
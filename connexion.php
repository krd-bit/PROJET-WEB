<?php
session_start();
require 'db.php';

if (isset($_POST['ok'])) {
    $email = trim($_POST['email']);
    $mdp = $_POST['mdp'];

    // 1. On récupère l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 2. Vérification du mot de passe
    if ($user && password_verify($mdp, $user['password'])) {
        
        // 3. Vérification si banni
        if (isset($user['est_actif']) && $user['est_actif'] == 0) {
            $error = "Compte désactivé. Contactez l'administrateur.";
        } else {
            // Tout est bon, on connecte
            $_SESSION['user'] = $user;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // --- 4. REDIRECTION CORRIGÉE ---
            // On nettoie le rôle (minuscule et sans espace)
            $role = strtolower(trim($user['role']));

            // A. ADMIN
            if (strpos($role, 'admin') !== false) {
                header("Location: dashboard_admin.php");
            } 
            // B. ÉCOLE / PROF
            elseif (strpos($role, 'ecole') !== false || strpos($role, 'school') !== false) { 
                header("Location: dashboard_prof.php");
            } 
            // C. ENTREPRISE (On accepte 'entreprise' OU 'company')
            elseif (strpos($role, 'entre') !== false || strpos($role, 'company') !== false) {
                header("Location: dashboard_entreprise.php");
            }
            // D. PAR DÉFAUT (Utilisateur simple)
            else {
                header("Location: dashboard_user.php");
            }
            exit();
        }
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Connexion - Quizzeo</title>
</head>
<body>
<div class="container" style="text-align:center; max-width: 400px; margin-top: 50px;">
    
    <div class="logo-brand" style="font-size: 40px; background:none; display:block; margin-bottom:10px;">
        <span class="l-q">Q</span><span class="l-z">ui</span><span class="l-z">zz</span><span class="l-z">e</span><span class="l-o">o</span>
    </div>
    <p style="color:#666; margin-bottom: 20px;">Connexion à votre espace</p>
    
    <?php if(isset($error)): ?>
        <div class="alert" style="background:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:15px;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <form method="post" style="text-align:left;">
        <label>Email</label>
        <input type="email" name="email" required placeholder="votre@email.com">
        
        <label>Mot de passe</label>
        <input type="password" name="mdp" required>
        
        <button type="submit" name="ok" class="btn" style="width:100%; margin-top:10px;">Se connecter</button>
    </form>
    
    <div style="margin-top: 20px; font-size: 0.9em;">
        Pas encore de compte ? <a href="inscription.php" style="color:var(--purple); font-weight:bold;">S'inscrire</a>
    </div>
</div>
</body>
</html>
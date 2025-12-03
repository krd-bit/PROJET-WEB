<?php
session_start();
require 'db.php';

if (isset($_POST['ok'])) {
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];

    // 1. On récupère l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 2. Vérification du mot de passe
    if ($user && password_verify($mdp, $user['password'])) {
        
        // 3. VERIFICATION SI BANNI (Ajouté pour le cahier des charges)
        // On vérifie si la colonne est_actif est à 0. 
        // (On utilise !empty pour éviter les erreurs si la colonne n'existe pas encore)
        if (isset($user['est_actif']) && $user['est_actif'] == 0) {
            $error = "Votre compte a été désactivé par l'administrateur.";
        } else {
            // Tout est bon, on connecte
            $_SESSION['user'] = $user;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // 4. REDIRECTION CORRECTE (Ajouté pour gérer l'Admin)
            if ($user['role'] == 'admin') {
                header("Location: dashboard_admin.php");
            } 
            elseif ($user['role'] == 'user') {
                header("Location: dashboard_user.php");
            } 
            elseif ($user['role'] == 'ecole' || $user['role'] == 'prof') { 
                // Adapte selon comment tu as nommé le rôle prof/école dans ta BDD
                header("Location: dashboard_prof.php");
            } 
            elseif ($user['role'] == 'entreprise') {
                // Si tu as un dashboard spécifique pour entreprise
                header("Location: dashboard_prof.php"); // Ou dashboard_entreprise.php si tu l'as créé
            }
            else {
                // Par défaut
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
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Connexion - Quizzeo</title>
</head>
<body>
<div class="container" style="text-align:center;">
    
    <div class="logo-brand" style="font-size: 40px; background:none;">
        <span class="l-q">Q</span><span class="l-z">ui</span><span class="l-z">zz</span><span class="l-z">e</span><span class="l-o">o</span>
    </div>
    <p style="color:#666; margin-top:-5px;">La plateforme de quiz interactive</p>
    <hr>

    <?php if(isset($error)) echo "<div class='alert' style='background:#f8d7da; color:red; padding:10px; margin-bottom:10px;'>$error</div>"; ?>
    
    <form method="post" style="text-align:left;">
        <label>Email</label>
        <input type="email" name="email" required placeholder="Ex: admin@quizzeo.com">
        
        <label>Mot de passe</label>
        <input type="password" name="mdp" required>
        
        <button type="submit" name="ok" class="btn" style="width:100%">Se connecter</button>
    </form>
    <p><a href="inscription.php">Créer un compte</a></p>
</div>
</body>
</html>
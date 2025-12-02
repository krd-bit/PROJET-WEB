<?php
session_start();
require 'db.php';
if (isset($_POST['ok'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch();
    if ($user && password_verify($_POST['mdp'], $user['password'])) {
        $_SESSION['user'] = $user;
        if ($user['role'] == 'user') header("Location: dashboard_user.php");
        else header("Location: dashboard_prof.php");
        exit();
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container" style="text-align:center;">
    
    <div class="logo-brand" style="font-size: 40px; background:none;">
        <span class="l-q">Q</span><span class="l-z">ui</span><span class="l-z">zz</span><span class="l-z">e</span><span class="l-o">o</span>
    </div>
    <p style="color:#666; margin-top:-5px;">La plateforme de quiz interactive</p>
    <hr>

    <?php if(isset($error)) echo "<div class='alert' style='background:#f8d7da; color:red;'>$error</div>"; ?>
    
    <form method="post" style="text-align:left;">
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Mot de passe</label>
        <input type="password" name="mdp" required>
        <button type="submit" name="ok" class="btn" style="width:100%">Se connecter</button>
    </form>
    <p><a href="inscription.php">Créer un compte</a></p>
</div>
</body>
</html>
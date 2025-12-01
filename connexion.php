<?php
session_start();
require 'db.php';

if (isset($_POST['ok'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['mdp'], $user['password'])) {
        if ($user['is_active'] == 0) {
            $error = "Compte désactivé par l'admin.";
        } else {
            $_SESSION['user'] = $user;
            if ($user['role'] == 'admin') header("Location: dashboard_admin.php");
            elseif ($user['role'] == 'user') header("Location: dashboard_user.php");
            else header("Location: dashboard_prof.php"); // Ecoles et Entreprises
            exit();
        }
    } else {
        $error = "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h1>Connexion</h1>
    <?php if(isset($error)) echo "<div class='alert'>$error</div>"; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="mdp" placeholder="Mot de passe" required>
        <button type="submit" name="ok" class="btn">Se connecter</button>
    </form>
    <p><a href="inscription.php">Créer un compte</a></p>
</div>
</body>
</html>
<?php
session_start();
require 'db.php';

// Génération captcha simple
if (!isset($_SESSION['nb1'])) { $_SESSION['nb1'] = rand(1, 9); $_SESSION['nb2'] = rand(1, 9); }

if (isset($_POST['ok'])) {
    if ($_POST['captcha'] != ($_SESSION['nb1'] + $_SESSION['nb2'])) {
        $error = "Mauvais calcul !";
    } else {
        $sql = "INSERT INTO users (email, password, role, company_name) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_POST['email'], password_hash($_POST['mdp'], PASSWORD_DEFAULT), $_POST['role'], $_POST['nom']]);
        header("Location: connexion.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <h1>Inscription Quizzeo</h1>
    <?php if(isset($error)) echo "<div class='alert'>$error</div>"; ?>
    <form method="post">
        <input type="text" name="nom" placeholder="Nom (École/Entreprise/Vous)" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="mdp" placeholder="Mot de passe" required>
        <select name="role">
            <option value="school">École</option>
            <option value="company">Entreprise</option>
            <option value="user">Utilisateur Simple</option>
        </select>
        <label>Combien font <?php echo $_SESSION['nb1'] . " + " . $_SESSION['nb2']; ?> ?</label>
        <input type="number" name="captcha" required>
        <button type="submit" name="ok" class="btn">S'inscrire</button>
    </form>
    <p><a href="connexion.php">J'ai déjà un compte</a></p>
</div>
</body>
</html>
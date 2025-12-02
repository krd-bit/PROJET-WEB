<?php
session_start();
require 'db.php';

// Si pas connecté, on dégage
if (!isset($_SESSION['user'])) {
    header("Location: connexion.php");
    exit();
}

$msg = "";
$user_id = $_SESSION['user']['id'];

// Traitement de la mise à jour
if (isset($_POST['update'])) {
    $new_email = $_POST['email'];
    $new_nom = $_POST['nom'];
    
    // Si l'utilisateur a rempli le champ mot de passe, on le change
    if (!empty($_POST['mdp'])) {
        $new_mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET email = ?, company_name = ?, password = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$new_email, $new_nom, $new_mdp, $user_id]);
    } else {
        // Sinon on change juste l'email et le nom
        $sql = "UPDATE users SET email = ?, company_name = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$new_email, $new_nom, $user_id]);
    }

    // On met à jour la session
    $_SESSION['user']['email'] = $new_email;
    $_SESSION['user']['company_name'] = $new_nom;
    $msg = "Profil mis à jour avec succès !";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mon Profil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <span>Mon Profil</span>
        <?php 
        // Lien de retour intelligent selon le rôle
        $retour = ($_SESSION['user']['role'] == 'user') ? 'dashboard_user.php' : 'dashboard_prof.php';
        ?>
        <a href="<?php echo $retour; ?>">Retour au Dashboard</a>
    </nav>

    <div class="container">
        <h2>Modifier mes informations</h2>
        <?php if($msg) echo "<div class='alert' style='background:#d4edda; color:#155724;'>$msg</div>"; ?>

        <form method="post">
            <label>Nom (ou Nom de l'entreprise) :</label>
            <input type="text" name="nom" value="<?php echo htmlspecialchars($_SESSION['user']['company_name']); ?>" required>

            <label>Email :</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user']['email']); ?>" required>

            <label>Nouveau mot de passe (Laisser vide si inchangé) :</label>
            <input type="password" name="mdp" placeholder="********">

            <button type="submit" name="update" class="btn">Enregistrer les modifications</button>
        </form>
    </div>
</body>
</html>
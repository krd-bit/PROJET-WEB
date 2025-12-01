<?php
session_start();
require 'db.php';
if ($_SESSION['user']['role'] != 'admin') header("Location: connexion.php");

// Activer/Désactiver User
if (isset($_GET['ban'])) {
    $pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?")->execute([$_GET['ban']]);
    header("Location: dashboard_admin.php");
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
<nav><span>ADMINISTRATION</span><a href="deconnexion.php">Déconnexion</a></nav>
<div class="container">
    <h2>Utilisateurs</h2>
    <table>
        <tr><th>Email</th><th>Rôle</th><th>Statut</th><th>Action</th></tr>
        <?php
        $users = $pdo->query("SELECT * FROM users")->fetchAll();
        foreach($users as $u) {
            $status = $u['is_active'] ? "Actif" : "Banni";
            $btn = $u['is_active'] ? "Bannir" : "Activer";
            echo "<tr><td>{$u['email']}</td><td>{$u['role']}</td><td>$status</td>
            <td><a href='?ban={$u['id']}'>$btn</a></td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>
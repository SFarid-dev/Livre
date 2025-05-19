<?php 
    include_once '../../includes/session.inc.php';
    if (isLoggedIn()) {
        header("Location: home.php");
    }
?>
<html>
<head>
    <title>Page de connexion</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php
    include_once '../../includes/header.php';
    if (isset($_GET['error'])) {
    $error = urldecode($_GET['error']);
    echo '<p style="color: red;">' . htmlspecialchars($error) . '</p>';
    }
    ?>

<div class="login-container">
    <div class="login-card">
        <form action="../../includes/login.inc.php" method="post">
        <h2>Formulaire connexion</h2>
            <div class="input-group">
                <input class="insc" type="pseudo" placeholder="Pseudo" name="pseudo" required>
            </div>
            <div class="input-group">
                <input class="insc" type="password" placeholder="Mot de passe" name="pwd" required>
            </div>
            <div>
                <p class="connexion">Je souhaite me connecter.</p>
            </div>
            <div align="center">
                <button type="submit" name="submit">Connexion</button>
            </div>
        </form>
    <div class="forgot-password">
            <a href="#">Mot de passe oublié ?</a>
        </div>
    </div>
</div>
</body> 
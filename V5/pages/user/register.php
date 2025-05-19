<?php 
    include_once '../../includes/session.inc.php';
    if (isLoggedIn()) {
        header("Location: home.php");
    }
?>
<html>
<head>
    <title>Page d'inscription</title>
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

<div class="register-container">
    <div class="login-card">
        <form action="../../includes/register.inc.php" method="post">
        <h2>Formulaire d'inscription</h2>
            <div class="input-group">
                <input class="insc" type="pseudo" placeholder="Pseudo" name="pseudo" required>
            </div>
            <div class="input-group">
                <input class="insc" type="password" placeholder="Mot de passe" name="pwd" required>
            </div>
            <div>
                <p class="inscription">Je souhaite m'inscrire.</p>
            </div>
            <div align="center">
                <button type="submit" name="submit">inscription</button>
            </div>
        </form>
        </div>
    </div>
</div>
</body> 
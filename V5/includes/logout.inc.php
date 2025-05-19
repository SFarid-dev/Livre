<?php
include_once "session.inc.php";

// Vérifier si une action de déconnexion a été demandée
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Appeler la fonction de déconnexion
    logout();

    // Rediriger vers la page de connexion
    header("Location: ../pages/user/home.php");
    exit();
}
?>

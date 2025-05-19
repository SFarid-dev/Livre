<?php
// Démarrer la session
session_start();

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fonction pour obtenir l'ID de l'utilisateur connecté
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Fonction pour obtenir le pseudo de l'utilisateur connecté
function getUsername() {
    return $_SESSION['pseudouser'] ?? null;
}

// Fonction pour obtenir le rôle de l'utilisateur connecté
function getUserRole() {
    return $_SESSION['role'] ?? null;
}


// Fonction pour détruire la session (déconnexion)
function logout() {
    // Supprimer toutes les variables de session
    session_unset();

    // Détruire la session
    session_destroy();
}

// Exemple d'utilisation :
/*
if (isLoggedIn()) {
    echo 'Bonjour, ' . getUsername();
} else {
    echo 'Bonjour, invité';
}
*/
?>

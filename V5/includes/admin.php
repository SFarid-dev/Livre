<?php
include_once 'db.php';
include_once 'session.inc.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    header("Location: ../user/home.php");
    exit();
}

// Vérifier si l'utilisateur est un administrateur
$roleStmt = $pdo->prepare("SELECT r.rolename FROM public.\"user\" u JOIN public.\"role\" r ON u.roleuser = r.id WHERE u.id = :userId");
$roleStmt->bindParam(':userId', $_SESSION['user_id'], PDO::PARAM_INT);
$roleStmt->execute();
$userRole = $roleStmt->fetchColumn();

if ($userRole !== 'Admin') {
    // Rediriger vers la page d'accueil
    header("Location: ../user/home.php");
    exit();
}
?>
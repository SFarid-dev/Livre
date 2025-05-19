<?php
require_once 'db.php';

if (isset($_POST["submit"])) {
    $pseudo = $_POST['pseudo'];
    $password = $_POST['pwd'];

    // Vérifier si l'utilisateur existe
    $stmt = $pdo->prepare("SELECT id, pseudouser, pwduser FROM public.\"user\" WHERE pseudouser = :pseudo");
    $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $user['pwduser'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['pseudouser'] = $user['pseudouser'];
            header("Location: ../pages/user/home.php");
            exit();
        } else {
            $error = "Erreur de connexion.";
            header("Location: ../pages/user/login.php?error=" . urlencode($error));
            exit();
        }
    } else {
        $error = "Erreur de connexion.";
        header("Location: ../pages/user/login.php?error=" . urlencode($error));
        exit();
    }
}
?>

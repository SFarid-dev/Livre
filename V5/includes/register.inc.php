<?php
require_once 'db.php';

if (isset($_POST["submit"])) {
    $pseudo = $_POST['pseudo'];
    $password = password_hash($_POST['pwd'], PASSWORD_DEFAULT);
    $roleName = 'Viewer'; // Rôle par défaut

    // Vérifier si le pseudo est déjà pris
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM public.\"user\" WHERE pseudouser = :pseudo");
    $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->fetchColumn() > 0) {
        $error = "Ce pseudo est déjà pris.";
        header("Location: ../pages/user/register.php?error=" . urlencode($error));
        exit();
    } else {
        // Récupérer l'ID du rôle "Viewer"
        $roleStmt = $pdo->prepare("SELECT id FROM public.\"role\" WHERE rolename = :roleName");
        $roleStmt->bindParam(':roleName', $roleName, PDO::PARAM_STR);
        $roleStmt->execute();
        $roleId = $roleStmt->fetchColumn();

        if ($roleId) {
            // Insérer le nouvel utilisateur avec le rôle
            $stmt = $pdo->prepare("INSERT INTO public.\"user\" (pseudouser, pwduser, roleuser) VALUES (:pseudo, :password, :roleId)");
            $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':roleId', $roleId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                header("Location: ../pages/user/home.php");
                exit();
            } else {
                $error = "Erreur lors de l'inscription.";
                header("Location: ../pages/user/register.php?error=" . urlencode($error));
                exit();
            }
        } else {
            $error = "Rôle non trouvé.";
            header("Location: ../pages/user/register.php?error=" . urlencode($error));
            exit();
        }
    }
}
?>

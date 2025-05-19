<?php
// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer l'ID de l'utilisateur depuis les données POST
    $user_id = $_POST['id'];

    // Inclure le fichier de connexion à la base de données
    include_once "../../includes/db.php";

    try {
        // Préparer la requête SQL pour supprimer l'utilisateur
        $sql = "DELETE FROM public.\"user\" WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);

        // Lier le paramètre et exécuter la requête
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: dashboard.php?message=DeleteSuccess");
        } else {
            header("Location: dashboard.php?message=DeleteFail");
        }
    } catch (PDOException $e) {
        // Gérer les erreurs de suppression
        header("Location: dashboard.php?message=DeleteFail");
    }
}
?>

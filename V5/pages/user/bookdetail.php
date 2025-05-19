<?php
include_once '../../includes/session.inc.php';
if (isLoggedIn()) {
    echo 'Bonjour, ' . getUsername();
} else {
    echo 'Bonjour, invité';
}

// Connexion à la base de données
include_once '../../includes/db.php';

// Récupérer l'ID du livre à partir de l'URL
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Récupérer l'ID et le rôle de l'utilisateur connecté
$userid = $_SESSION['user_id'];
$userRole = $_SESSION['role'];

if ($bookId) {
    // Récupérer les détails du livre
    $stmt = $pdo->prepare("
        SELECT b.id, b.name, u.pseudouser, b.\"userid\"
        FROM public.\"book\" b
        JOIN public.\"user\" u ON b.\"userid\" = u.id
        WHERE b.id = :bookId
    ");
    $stmt->bindParam(':bookId', $bookId, PDO::PARAM_INT);
    $stmt->execute();

    // Récupération des résultats
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    die("ID de livre non spécifié.");
}
?>
<html>
<head>
    <title>Détails du Livre</title>
    <link rel="stylesheet" href="../../assets/css/home.css">
</head>
<header>
    <?php
    include_once '../../includes/header.php';
    include_once '../../includes/logout.inc.php';
    ?>
</header>
<body>
    <div class="book-detail">
        <!-- Afficher les détails du livre -->
        <h2>Mes Livres</h2>
        <div class="book-list">
            <?php if ($bookId): ?>
                <div class="book-card">
                    <div class="book-title">
                        <?= htmlspecialchars($book['name']) ?>
                    </div>
                    <div class="user-name">
                        Utilisateur: <?= htmlspecialchars($book['pseudouser']) ?>
                    </div>
                    <!-- Boutons de suppression et de modification -->
                    <div class="book-actions">
                        <?php if ($userRole === 'Admin' || $userRole === 'Editor' || $book['userid'] === $userid): ?>
                            <a href="editbook.php?action=edit&id=<?= htmlspecialchars($book['id']) ?>" class="book-action">Modifier</a>
                        <?php endif; ?>
                        <?php if ($userRole === 'Admin' || $book['userid'] === $userid): ?>
                            <a href="editbook.php?action=delete&id=<?= htmlspecialchars($book['id']) ?>" class="book-action">Supprimer</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

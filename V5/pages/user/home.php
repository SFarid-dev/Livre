<?php
include_once '../../includes/session.inc.php';
if (isLoggedIn()) {
    echo 'Bonjour, ' . getUsername();
} else {
    echo 'Bonjour, invité';
}

// Connexion à la base de données
include_once '../../includes/db.php';

// Récupérer l'ID et le rôle de l'utilisateur connecté
$userid = $_SESSION['user_id'];
$userRole = $_SESSION['role'];

// Récupérer les livres de l'utilisateur connecté
$stmt = $pdo->prepare("
    SELECT id, name, imageurl
    FROM public.\"book\"
    WHERE \"userid\" = :userid
");
$stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
$stmt->execute();

// Récupération des résultats
$userBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nombre de livres par page
$booksPerPage = 10;

// Page actuelle
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculer l'offset
$offset = ($currentPage - 1) * $booksPerPage;

// Compter le nombre total de livres
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM public.\"book\"");
$countStmt->execute();
$totalBooks = $countStmt->fetchColumn();
$totalPages = ceil($totalBooks / $booksPerPage);

// Récupérer les livres pour la page actuelle
$stmt = $pdo->prepare("
    SELECT b.id, b.name, u.pseudouser, b.imageurl
    FROM public.\"book\" b
    JOIN public.\"user\" u ON b.\"userid\" = u.id
    LIMIT :limit OFFSET :offset
");
$stmt->bindParam(':limit', $booksPerPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

// Récupération des résultats
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<html>
<head>
    <title>Kitems Home</title>
    <link rel="stylesheet" href="../../assets/css/home.css">
</head>
<header>
    <?php
    include_once '../../includes/header.php';
    include_once '../../includes/logout.inc.php';
    ?>
</header>
<body>
    <div class="secteur1">
        <!-- Bouton pour créer un nouveau livre -->
        <div class="book-actions">
            <a href="create_book.php" class="book-action">Créer un Livre</a>
        </div>

        <!-- Afficher les livres de l'utilisateur connecté -->
        <h2>Mes Livres</h2>
        <div class="book-list">
            <?php foreach ($userBooks as $book): ?>
                <div class="book-card">
                    <!-- Afficher l'image du livre -->
                    <div class="book-image">
                        <a href="bookdetail.php?id=<?= htmlspecialchars($book['id']) ?>">
                            <img src="<?= htmlspecialchars($book['imageurl']) ?>" alt="<?= htmlspecialchars($book['name']) ?>">
                        </a>
                    </div>
                    <!-- Afficher le titre du livre -->
                    <div class="book-title">
                        <?= htmlspecialchars($book['name']) ?>
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
            <?php endforeach; ?>
        </div>
    </div>
    <div class="secteur2">
        <!-- Afficher les livres -->
        <h2>Liste des Livres</h2>
        <div class="book-list">
            <?php foreach ($books as $book): ?>
                <div class="book-card">
                    <!-- Afficher l'image du livre -->
                    <div class="book-image">
                        <a href="bookdetail.php?id=<?= htmlspecialchars($book['id']) ?>">
                            <img src="<?= htmlspecialchars($book['imageurl']) ?>" alt="<?= htmlspecialchars($book['name']) ?>">
                        </a>
                    </div>
                    <!-- Afficher le titre du livre -->
                    <div class="book-title">
                        <?= htmlspecialchars($book['name']) ?>
                    </div>
                    <div class="user-name">
                        Utilisateur: <?= htmlspecialchars($book['pseudouser']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Liens de pagination -->
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?>">Précédent</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" <?= $i === $currentPage ? 'class="active"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?>">Suivant</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="secteur3">
        <!-- Contenu de la section 3 -->
    </div>
</body>
</html>

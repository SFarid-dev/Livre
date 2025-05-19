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

// Vérifier si l'utilisateur a le droit de modifier ou supprimer le livre
if ($userRole !== 'Admin' && $userRole !== 'Editor' && $book['userid'] !== $userid) {
    die("Vous n'avez pas le droit de modifier ou de supprimer ce livre.");
}

// Vérifier l'action demandée
$action = isset($_GET['action']) ? $_GET['action'] : 'edit';

// Gérer l'action de suppression
if ($action === 'delete') {
    // Supprimer le livre
    $stmt = $pdo->prepare("
        DELETE FROM public.\"book\"
        WHERE id = :bookId
    ");
    $stmt->bindParam(':bookId', $bookId, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: home.php");
    exit();
}

// Gérer l'action de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = $_POST['name'];

    // Mettre à jour les détails du livre
    $stmt = $pdo->prepare("
        UPDATE public.\"book\"
        SET name = :name
        WHERE id = :bookId
    ");
    $stmt->bindParam(':name', $newName, PDO::PARAM_STR);
    $stmt->bindParam(':bookId', $bookId, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: bookdetail.php?id=" . $bookId);
    exit();
}
?>
<html>
<head>
    <title>Gérer le Livre</title>
    <link rel="stylesheet" href="../../assets/css/home.css">
</head>
<header>
    <?php
    include_once '../../includes/header.php';
    include_once '../../includes/logout.inc.php';
    ?>
</header>
<body>
    <div class="editbook">
        <!-- Formulaire de modification du livre -->
        <h2>Gérer le Livre</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="name">Nom du Livre</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($book['name']) ?>" required>
            </div>
            <button type="submit">Enregistrer</button>
        </form>

        <!-- Bouton de suppression -->
        <form action="?action=delete&id=<?= htmlspecialchars($book['id']) ?>" method="post">
            <button type="submit">Supprimer</button>
        </form>
    </div>
</body>
</html>

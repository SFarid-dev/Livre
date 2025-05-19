<?php
require_once '../../includes/admin.php';
$userId = $_SESSION['user_id'];
$username = $_SESSION['pseudouser'];
$role = $_SESSION['role'];

// Nombre d'utilisateurs par page
$usersPerPage = 10;

// Page actuelle
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Colonne de tri
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'id';

// Ordre de tri
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Calculer l'offset
$offset = ($currentPage - 1) * $usersPerPage;
?>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.0/css/all.css">
</head>
<body>
    <main>
    <h1>Bienvenue, <?php echo htmlspecialchars($username); ?>!</h1>
        <div class="link_container">
            <a class="link" href="createUser.php">Ajouter un utilisateur</a>
        </div>
        <table>
            <thead>
                <?php
                include_once "../../includes/db.php";

                // Compter le nombre total d'utilisateurs
                $countStmt = $pdo->prepare("SELECT COUNT(*) FROM public.\"user\"");
                $countStmt->execute();
                $totalUsers = $countStmt->fetchColumn();
                $totalPages = ceil($totalUsers / $usersPerPage);

                // Liste des utilisateurs avec leurs rôles pour la page actuelle
                $sql = "
                    SELECT u.id, u.pseudouser, r.rolename
                    FROM public.\"user\" u
                    JOIN public.\"role\" r ON u.roleuser = r.id
                ";

                // Ajouter le tri
                if ($sortColumn === 'rolename') {
                    $sql .= " ORDER BY r.rolename $sortOrder";
                } else {
                    $sql .= " ORDER BY u.$sortColumn $sortOrder";
                }

                $sql .= " LIMIT :limit OFFSET :offset";

                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':limit', $usersPerPage, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();

                // Récupération des résultats
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <tr>
                    <th><a href="?sort=id&order=<?= $sortColumn === 'id' && $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
                    <th><a href="?sort=pseudouser&order=<?= $sortColumn === 'pseudouser' && $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">Nom</a></th>
                    <th><a href="?sort=rolename&order=<?= $sortColumn === 'rolename' && $sortOrder === 'ASC' ? 'DESC' : 'ASC' ?>">Rôle</a></th>
                    <th>Modifier</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Vérifiez si des utilisateurs sont présents
                if (empty($users)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Aucun utilisateur enregistré</td>
                    </tr>
                    <?php else: foreach ($users as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['pseudouser']) ?></td>
                        <td><?= htmlspecialchars($row['rolename']) ?></td>
                        <td>
                            <a href="createUser.php?id=<?= htmlspecialchars($row['id']) ?>">
                                <img src="../../assets/img/edit-btn.png" alt="Modifier">
                            </a>
                        </td>
                        <td>
                        <form action="deleteUser.php" method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                            <button type="submit" style="background: none; border: none;">
                                <img src="../../assets/img/delete-btn.png" alt="Supprimer">
                            </button>
                        </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>

        <!-- Liens de pagination -->
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?>&sort=<?= $sortColumn ?>&order=<?= $sortOrder ?>">Précédent</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&sort=<?= $sortColumn ?>&order=<?= $sortOrder ?>" <?= $i === $currentPage ? 'class="active"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?>&sort=<?= $sortColumn ?>&order=<?= $sortOrder ?>">Suivant</a>
            <?php endif; ?>
        </div>

        <h2>Pages Opérationnelles</h2>
        <table>
            <thead>
                <tr>
                    <th>Page</th>
                    <th>Accès</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Accueil</td>
                    <td><a href="../user/home.php">Accéder</a></td>
                </tr>
                <tr>
                    <td>Connexion</td>
                    <td><a href="../user/login.php">Accéder</a></td>
                </tr>
                <tr>
                    <td>Inscription</td>
                    <td><a href="../user/register.php">Accéder</a></td>
                </tr>
            </tbody>
        </table>

    <a href="../../includes/logout.inc.php?action=logout">Déconnexion</a>
    </main>
</body>
</html>

<?php
// Vérifier si un userId est fourni pour déterminer l'action
$userId = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : null);
$action = $userId ? 'modify' : 'add';

// Définir le titre de la page et le texte du h1 en fonction de l'action
$pageTitle = ($action === 'modify') ? "Modifier un utilisateur" : "Ajouter un utilisateur";
$headingText = ($action === 'modify') ? "Modifier un utilisateur" : "Ajouter un utilisateur";
$buttonAction = ($action === 'modify') ? "Modifier" : "Ajouter";

// Si c'est une modification, récupérer les informations de l'utilisateur
if ($action === 'modify') {
    include_once "../../includes/db.php";
    $stmt = $pdo->prepare("SELECT pseudouser, rolename FROM public.\"user\" JOIN public.\"UserRoles\" ON public.\"user\".id = public.\"UserRoles\".\"userId\" JOIN public.\"role\" ON public.\"UserRoles\".\"roleId\" = public.\"role\".id WHERE public.\"user\".id = :userId");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <form action="" method="post">
        <h1><?php echo $headingText; ?></h1>
        <input type="text" name="username" placeholder="Utilisateur" value="<?php echo $action === 'modify' ? htmlspecialchars($user['pseudouser']) : ''; ?>">
        <select id="role" name="role">
            <option value="">--Sélectionnez un rôle--</option>
            <option value="Admin" <?php echo ($action === 'modify' && $user['rolename'] === 'Admin') ? 'selected' : ''; ?>>Admin</option>
            <option value="Editor" <?php echo ($action === 'modify' && $user['rolename'] === 'Editor') ? 'selected' : ''; ?>>Editor</option>
            <option value="Viewer" <?php echo ($action === 'modify' && $user['rolename'] === 'Viewer') ? 'selected' : ''; ?>>Viewer</option>
            <option value="Contributor" <?php echo ($action === 'modify' && $user['rolename'] === 'Contributor') ? 'selected' : ''; ?>>Contributor</option>
        </select>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <input type="submit" value="<?php echo $buttonAction?>" name="send">
        <a class="link back" href="dashboard.php">Annuler</a>
    </form>
</body>
<?php
// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include_once "../../includes/db.php";
    // Récupérer les données du formulaire
    $userId = $_POST['id'] ?? null;
    $username = $_POST['username'];
    $roleName = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Vérifier si l'opération est une modification ou un ajout
    if ($userId) {
        // Préparer la requête SQL pour modifier l'utilisateur
        $sql = "UPDATE public.\"user\" SET pseudouser = :username, pwduser = :password WHERE id = :userId";
    } else {
        // Préparer la requête SQL pour ajouter un nouvel utilisateur
        $sql = "INSERT INTO public.\"user\" (pseudouser, pwduser) VALUES (:username, :password)";
    }

    // Préparer et exécuter la requête
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);

    if ($userId) {
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    }

    if ($stmt->execute()) {
        if ($userId) {
            // Mettre à jour le rôle de l'utilisateur existant
            $roleSql = "UPDATE public.\"UserRoles\" SET \"roleId\" = (SELECT id FROM public.\"role\" WHERE rolename = :roleName) WHERE \"userId\" = :userId";
        } else {
            // Récupérer l'ID de l'utilisateur nouvellement inséré
            $userId = $pdo->lastInsertId();
            // Insérer le rôle de l'utilisateur dans la table UserRoles
            $roleSql = "INSERT INTO public.\"UserRoles\" (\"userId\", \"roleId\") VALUES (:userId, (SELECT id FROM public.\"role\" WHERE rolename = :roleName))";
        }

        $roleStmt = $pdo->prepare($roleSql);
        $roleStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $roleStmt->bindParam(':roleName', $roleName, PDO::PARAM_STR);

        if ($roleStmt->execute()) {
            header("Location: dashboard.php?message=UserOperationSuccess");
        } else {
            header("Location: dashboard.php?message=RoleAssignFail");
        }
    } else {
        header("Location: dashboard.php?message=UserOperationFail");
    }
}
?>

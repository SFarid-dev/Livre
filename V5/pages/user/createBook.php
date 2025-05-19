<?php
include_once '../../includes/session.inc.php';
if (!isLoggedIn()) {
    header("Location: ../../pages/user/login.php");
    exit();
}

// Connexion à la base de données
include_once '../../includes/db.php';

// Récupérer l'ID de l'utilisateur connecté
$userid = $_SESSION['user_id'];

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];

    // Insérer le nouveau livre dans la base de données pour obtenir l'ID du livre
    $stmt = $pdo->prepare("
        INSERT INTO public.\"book\" (name, \"userid\")
        VALUES (:name, :userid)
        RETURNING id
    ");
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
    $stmt->execute();

    // Récupérer l'ID du livre
    $bookId = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

    // Gérer l'upload de l'image
    $targetDir = "../../assets/img/";
    $imageurl = "../../assets/img/0.png"; // Image par défaut

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
        $targetFile = $targetDir . $bookId . '.' . strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Vérifier si le fichier est une image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "Le fichier n'est pas une image.";
            $uploadOk = 0;
        }

        // Limiter la taille du fichier
        if ($_FILES["image"]["size"] > 500000) {
            echo "Désolé, votre fichier est trop volumineux.";
            $uploadOk = 0;
        }

        // Limiter les formats de fichier
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Désolé, seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
            $uploadOk = 0;
        }

        // Vérifier si $uploadOk est défini à 0 par une erreur
        if ($uploadOk == 0) {
            echo "Désolé, votre fichier n'a pas été uploadé.";
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                echo "Le fichier a été uploadé et renommé avec l'ID du livre.";
                $imageurl = "../../assets/img/" . $bookId . '.' . $imageFileType;
            } else {
                echo "Désolé, une erreur s'est produite lors de l'upload de votre fichier.";
            }
        }
    }

    // Mettre à jour le chemin de l'image dans la base de données
    $stmt = $pdo->prepare("
        UPDATE public.\"book\"
        SET imageurl = :imageurl
        WHERE id = :bookId
    ");
    $stmt->bindParam(':imageurl', $imageurl, PDO::PARAM_STR);
    $stmt->bindParam(':bookId', $bookId, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: bookdetail.php?id=" . $bookId);
    exit();
}
?>
<html>
<head>
    <title>Créer un Livre</title>
    <link rel="stylesheet" href="../../assets/css/home.css">
    <script>
        function validateForm() {
            var fileInput = document.getElementById('image');
            if (fileInput.files.length > 1) {
                alert("Vous ne pouvez uploader qu'un seul fichier.");
                return false;
            }
            return true;
        }
    </script>
</head>
<header>
    <?php
    include_once '../../includes/header.php';
    include_once '../../includes/logout.inc.php';
    ?>
</header>
<body>
    <div class="create-book">
        <!-- Formulaire de création de livre -->
        <h2>Créer un Livre</h2>
        <form action="" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="name">Nom du Livre</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="image">Image du Livre</label>
                <input type="file" id="image" name="image">
            </div>
            <button type="submit">Créer</button>
        </form>
    </div>
</body>
</html>

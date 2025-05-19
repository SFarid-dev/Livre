<?php
try {
    $pdo = new PDO('pgsql:host=postgres;port=5432;dbname=KitemsDB', 'postgres', 'example', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    // Établit la connexion et gère les exceptions si la connexion échoue
    //echo "Connexion à la base de données avec succès";
} catch (PDOException $e) {
    // Vérifier le code d'erreur pour gérer différents cas
    switch ($e->getCode()) {
        case '08006':
            // Connexion refusée (par exemple, mauvais mot de passe)
            echo "Erreur : Connexion refusée. Vérifiez le nom d'utilisateur et le mot de passe.";
            break;
        case '08001':
            // Impossible de se connecter au serveur
            echo "Erreur : Impossible de se connecter au serveur. Vérifiez les paramètres de connexion.";
            break;
        case '08004':
            // Connexion réussie, mais la base de données n'existe pas
            echo "Erreur : La base de données spécifiée n'existe pas.";
            break;
        case '08003':
            // Connexion interrompue
            echo "Erreur : Connexion interrompue. Vérifiez la stabilité de la connexion réseau.";
            break;
        default:
            // Autres erreurs
            echo "Erreur de connexion inattendue : " . $e->getMessage();
            break;
    }
    die("Erreur de connexion : " . $e->getMessage());
}
?>

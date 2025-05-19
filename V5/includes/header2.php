<?php
include_once "../../includes/session.inc.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="#" class="logo">Kitems</a>
        <div class="nav-links">
            <ul>
                <?php if (isLoggedIn()): ?>
                    <!-- Liens pour les utilisateurs connectés -->
                    <li><a href="../../pages/user/home.php">Accueil</a></li>
                    <li><a href="../../pages/user/profile.php">Profil</a></li>
                    <li><a href="../../includes/logout.inc.php">Déconnexion</a></li>
                    <?php if (getUserRole() === 'Admin'): ?>
                        <!-- Liens pour les administrateurs -->
                        <li><a href="../../pages/user/home.php">Accueil</a></li>
                        <li><a href="../../pages/admin/dashboard.php">Dashboard</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Liens pour les utilisateurs non connectés -->
                    <li><a href="../../pages/user/home.php">Accueil</a></li>
                    <li><a href="../../pages/user/login.php">Connexion</a></li>
                    <li><a href="../../pages/user/register.php">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <img src="../assets/img/menu-btn.png" alt="menu" class="menu">
    </nav>
    <script>
            const menuHamburger = document.querySelector(".menu")
            const navLinks = document.querySelector(".nav-links")
    
            menuHamburger.addEventListener('click',()=>{ //Récupère le clique de l'utilisateur
            navLinks.classList.toggle('menu-active') // Ajoute la class menu-active 
            })
    </script>
</body>
</html>
<?php
// IL EST CRUCIAL QUE CECI SOIT LA TOUTE PREMIERE CHOSE DANS VOTRE FICHIER (avant tout HTML)
// Si ce header.php est inclus dans d'autres fichiers, assurez-vous que session_start()
// n'est appelé qu'une seule fois au tout début de l'exécution de la page.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>

<header class="navbar">
    <div class="navbar-left">
        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i> </button>
    </div>
    <div class="navbar-center">
        <a href="index.php">
            <img src="ressources%20et%20consignes/img/logo/Logo.jpg" alt="Logo de votre site" class="logo">
        </a>
    </div>
    <div class="navbar-right">
        <div class="lang-selector">
            <button class="lang-button active">FR</button>
            <button class="lang-button">NL</button>
        </div>
        <div class="user-auth-wrapper">
            <button class="icon-button" id="userIconTrigger"><i class="fas fa-user"></i></button>
            <div id="authDropdown" class="auth-dropdown">
                <?php

                // Vérifier si l'utilisateur est actuellement connecté
                if (isset($_SESSION['LOGIN']) && $_SESSION['LOGIN'] === true) {
                    // Si l'utilisateur est connecté, afficher le bouton de déconnexion.
                    // Il doit pointer vers votre script logout.php avec l'action 'logout'.
                    echo '<a href="logout.php?action=logout" class="auth-button logout-button">Déconnexion</a>';
                } else {
                    echo '<a href="loginPage.php" class="button login-button">Se connecter</a>';
                }

                ?>

            </div>
        </div>
    </div>
</header>


<!-- La side barre sur le coté gauche -->
<nav id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" id="closeSidenav">&times;</a> <a href="index.php">Accueil</a>
    <a href="listAllProduct.php">Tous nos produits</a>
    <?php
    if (isset($_SESSION['LOGIN']) && $_SESSION['LOGIN'] === true) {
        // Si l'utilisateur est connecté, afficher le bouton de déconnexion.
        // Il doit pointer vers votre script logout.php avec l'action 'logout'.
        echo '<a href="listAllProductAdmin.php?action=logout" class="auth-button logout-button">Produits version Admin</a>';
    }
    ?>

    <a href="contactUS.php">Contactez-nous</a>

    <?php
    if (isset($_SESSION['LOGIN']) && $_SESSION['LOGIN'] === true) {
        // Si l'utilisateur est connecté, afficher le bouton de déconnexion.
        // Il doit pointer vers votre script logout.php avec l'action 'logout'.
        echo '<a href="logout.php?action=logout" class="auth-button logout-button">Déconnexion</a>';
    } else {
        echo '<a href="loginPage.php" class="button login-button">Se connecter</a>';
    }
    ?>
</nav>


<div id="sidenavOverlay" class="sidenav-overlay"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Logique existante pour le dropdown utilisateur
        const userIconTrigger = document.getElementById('userIconTrigger');
        const authDropdown = document.getElementById('authDropdown');

        userIconTrigger.addEventListener('click', function(e) {
            e.preventDefault();
            authDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function(event) {
            // S'assurer que le clic n'est pas sur le trigger ni dans le dropdown lui-même
            if (!authDropdown.contains(event.target) && !userIconTrigger.contains(event.target) && event.target.closest('#userIconTrigger') === null) {
                authDropdown.classList.remove('show');
            }
        });

        // --- NOUVELLE LOGIQUE POUR LE SIDENAV ---
        const menuToggle = document.getElementById('menuToggle');
        const closeSidenav = document.getElementById('closeSidenav');
        const mySidenav = document.getElementById('mySidenav');
        const sidenavOverlay = document.getElementById('sidenavOverlay');

        // Ouvrir le sidenav
        menuToggle.addEventListener('click', function() {
            mySidenav.style.width = "250px"; // Largeur du menu
            sidenavOverlay.style.display = "block"; // Afficher l'overlay
            sidenavOverlay.style.opacity = "1"; // Rendre l'overlay opaque
            // Optionnel: Désactiver le scroll du body quand le menu est ouvert
            document.body.style.overflow = "hidden";
        });

        // Fermer le sidenav (via le bouton X ou l'overlay)
        function closeNav() {
            mySidenav.style.width = "0"; // Réduire la largeur à 0
            sidenavOverlay.style.opacity = "0"; // Cacher l'overlay en fondu
            // Attendre la fin de la transition pour masquer complètement l'overlay
            setTimeout(() => {
                sidenavOverlay.style.display = "none";
            }, 300); // Doit correspondre à la durée de transition CSS
            document.body.style.overflow = "auto"; // Réactiver le scroll
        }

        closeSidenav.addEventListener('click', closeNav);
        sidenavOverlay.addEventListener('click', closeNav); // Fermer en cliquant sur l'overlay
    });
</script>
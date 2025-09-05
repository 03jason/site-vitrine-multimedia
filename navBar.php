<?php
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

        <div class="user-auth-wrapper">
            <button class="icon-button" id="userIconTrigger"><i class="fas fa-user"></i></button>
            <div id="authDropdown" class="auth-dropdown">
                <?php
                if (isset($_SESSION['LOGIN']) && $_SESSION['LOGIN'] === true) {
                    echo '<a href="logout.php?action=logout" class="auth-button logout-button">Déconnexion</a>';
                } else {
                    echo '<a href="loginPage.php" class="button login-button">Se connecter</a>';
                }
                ?>

            </div>
        </div>
    </div>
</header>

<nav id="mySidenav" class="sidenav">



    <?php
    if (!empty($_SESSION['LOGIN']) && $_SESSION['LOGIN'] === true) {
        $role = (!empty($_SESSION['ADMIN']) && $_SESSION['ADMIN'] === true) ? 'Gérant' : 'Assistant';
        $username = $_SESSION['USERNAME'] ?? 'Utilisateur';
        echo '<div class="user-status"> <strong>' . htmlspecialchars($username) . '</strong> (' . $role . ')</div>';
    }
    ?>



    <a href="javascript:void(0)" class="closebtn" id="closeSidenav">&times;</a> <a href="index.php">Accueil</a>
    <a href="listAllProduct.php">Tous nos produits</a>
    <?php
    if (isset($_SESSION['LOGIN']) && $_SESSION['LOGIN'] === true) {
        echo '<a href="listAllProductAdmin.php?action=logout" class="auth-button logout-button">Produits version Admin</a>';
    }
    ?>

    <a href="contactUS.php">Contactez-nous</a>

    <?php
    if (isset($_SESSION['LOGIN']) && $_SESSION['LOGIN'] === true) {
        echo '<a href="logout.php?action=logout" class="auth-button logout-button">Déconnexion</a>';
    } else {
        echo '<a href="loginPage.php" class="button login-button">Se connecter</a>';
    }
    ?>
</nav>


<div id="sidenavOverlay" class="sidenav-overlay"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userIconTrigger = document.getElementById('userIconTrigger');
        const authDropdown = document.getElementById('authDropdown');

        userIconTrigger.addEventListener('click', function(e) {
            e.preventDefault();
            authDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function(event) {
            if (!authDropdown.contains(event.target) && !userIconTrigger.contains(event.target) && event.target.closest('#userIconTrigger') === null) {
                authDropdown.classList.remove('show');
            }
        });

        const menuToggle = document.getElementById('menuToggle');
        const closeSidenav = document.getElementById('closeSidenav');
        const mySidenav = document.getElementById('mySidenav');
        const sidenavOverlay = document.getElementById('sidenavOverlay');

        menuToggle.addEventListener('click', function() {
            mySidenav.style.width = "250px";
            sidenavOverlay.style.display = "block";
            sidenavOverlay.style.opacity = "1";
            document.body.style.overflow = "hidden";
        });

        function closeNav() {
            mySidenav.style.width = "0";
            sidenavOverlay.style.opacity = "0";
            setTimeout(() => {
                sidenavOverlay.style.display = "none";
            }, 300);
            document.body.style.overflow = "auto";
        }

        closeSidenav.addEventListener('click', closeNav);
        sidenavOverlay.addEventListener('click', closeNav);
    });
</script>
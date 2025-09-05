<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Votre Site Multimédia</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php
// Inclure la barre de navigation
include 'navBar.php';

// Démarrer la session si ce n'est pas déjà fait (pour récupérer les messages flash)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<main class="login-page-main"> <div class="login-container card-shadow"> <h2 class="form-title">Connectez-vous</h2>

        <?php
        if (isset($_SESSION['login_message'])) {
            $messageClass = strpos($_SESSION['login_message'], 'succès') !== false ? 'success-message' : 'error-message';
            echo '<div class="' . $messageClass . '">' . htmlspecialchars($_SESSION['login_message']) . '</div>';
            unset($_SESSION['login_message']);
        }
        ?>

        <form action="login.php" method="POST" class="login-form">
            <div class="form-group"> <label for="username"><i class="fas fa-user"></i> Identifiant ou Email</label>
                <input type="text" id="username" name="username" required aria-label="Identifiant ou Email">


            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
                <input type="password" id="password" name="password" required aria-label="Mot de passe">
            </div>
            <button type="submit" class="btn primary-button submit-button">Se connecter</button> </form>

    </div>
</main>

<?php include 'footer.php'; ?> </body>
</html>
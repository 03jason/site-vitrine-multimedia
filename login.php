<?php

// Assurez-vous que session_start() est LA PREMIERE chose
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'connect.php';



// Récupération et nettoyage des données du formulaire POST
// trim() supprime les espaces blancs (espaces, tabulations, retours à la ligne) au début et à la fin.
// htmlspecialchars() convertit les caractères spéciaux en entités HTML pour prévenir les attaques XSS.
$user = trim(htmlspecialchars($_POST['username']));
$pass = $_POST['password']; // Le mot de passe sera hashé, donc pas besoin de htmlspecialchars ici
$motDePasseSaisiHashe = hash('sha256', $pass);


// 1. Préparer la requête SQL de manière sécurisée (avec un placeholder '?')
$stmt = $connexionDB->prepare("SELECT mot_de_passe_hash FROM admin WHERE login = ?");


// 2. Lier les paramètres
// 's' indique que le paramètre est une chaîne de caractères (string)
$stmt->bind_param("s", $user);

// 3. Exécuter la requête préparée
$stmt->execute();

// 4. Obtenir le jeu de résultats (uniquement pour les requêtes SELECT)
$resultatVerification = $stmt->get_result();

// 5. Récupérer les données
$motPasseAssocie = null; // Initialiser à null ou une valeur par défaut

if ($resultatVerification && $resultatVerification->num_rows > 0) {
    // Si une ligne est trouvée, récupérer le hachage du mot de passe
    $data = $resultatVerification->fetch_assoc();
    $motPasseAssocie = $data['mot_de_passe_hash']; // Assurez-vous que le nom de la colonne est correct
}

// 6. Fermer la requête préparée
$stmt->close();


if ($motDePasseSaisiHashe === $motPasseAssocie && $user == 'gerant') {

    $_SESSION['LOGIN'] = true ;
    $_SESSION['ADMIN'] = true ;
    header('Location: index.php');
        exit();
} elseif ($motDePasseSaisiHashe === $motPasseAssocie) {

    $_SESSION['LOGIN'] = true ;
    $_SESSION['ADMIN'] = false ;
    header('Location: index.php');
    exit();

} else {
    header("Location: loginPage.php");
    exit();
}
?>
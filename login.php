<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'connect.php';

$user = trim(htmlspecialchars($_POST['username']));
$pass = $_POST['password'];
$motDePasseSaisiHashe = hash('sha256', $pass);


$stmt = $connexionDB->prepare("SELECT mot_de_passe_hash FROM admin WHERE login = ?");

$stmt->bind_param("s", $user);

$stmt->execute();

$resultatVerification = $stmt->get_result();

$motPasseAssocie = null;

if ($resultatVerification && $resultatVerification->num_rows > 0) {
    $data = $resultatVerification->fetch_assoc();
    $motPasseAssocie = $data['mot_de_passe_hash'];
}

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
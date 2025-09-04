<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if($_SESSION['LOGIN'] === true){
    $_SESSION['LOGIN'] = false;
    $_SESSION['ADMIN'] = false ;

    session_destroy(); // Détruit la session sur le serveur
    header('Location: index.php');
    exit();
}


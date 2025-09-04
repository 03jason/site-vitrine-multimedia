<?php

require_once 'config/db.php';


// Connect to DB

$connexionDB = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($connexionDB->connect_errno) {
    echo "Problème de connexion";
}
<?php

require_once 'config/db.php';


// Connect to DB

$connexionDB = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($connexionDB->connect_errno) {
    echo "Problème de connexion";
}

if ($connexionDB->connect_errno) {
    http_response_code(500);
    exit('Problème de connexion à la base de données.');
}

// Charset UTF-8
if (!$connexionDB->set_charset('utf8mb4')) {
    http_response_code(500);
    exit('Problème de configuration du charset.');
}
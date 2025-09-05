<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require __DIR__ . '/connect.php';

if (empty($_SESSION['LOGIN'])) {
    http_response_code(403);
    exit('Non connecté');
}

$id   = (int)($_POST['id'] ?? 0);
$desc = trim($_POST['description'] ?? '');

if ($id <= 0 || $desc === '') {
    http_response_code(400);
    exit('Requête invalide');
}

$isGerant = !empty($_SESSION['ADMIN']) && $_SESSION['ADMIN'] === true;

if ($isGerant) {
    // Champs supplémentaires autorisés pour le gérant
    $nom   = trim($_POST['nom'] ?? '');
    $prix  = (float)($_POST['prix'] ?? 0);
    $qte   = (int)($_POST['quantite_en_stock'] ?? 0);
    $cat   = trim($_POST['categorie'] ?? '');
    $marque= trim($_POST['marque'] ?? '');
    $statut= $_POST['statut'] ?? 'disponible';

    if ($nom === '' || $prix <= 0 || $qte < 0 || !in_array($statut, ['disponible','en rupture'], true)) {
        http_response_code(400);
        exit('Champs invalides');
    }

    // Photo : chemin direct prioritaire
    $imagePath = trim($_POST['image_path'] ?? '');
    $photo = null;

    if ($imagePath !== '') {
        $photo = $imagePath;
    } elseif (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $tmp   = $_FILES['image_file']['tmp_name'];
        $name  = $_FILES['image_file']['name'];
        $type  = $_FILES['image_file']['type'];
        $size  = $_FILES['image_file']['size'];

        $allowed = ['image/jpeg','image/png','image/gif'];
        if (!in_array($type, $allowed)) {
            http_response_code(400);
            exit('Type d’image non autorisé');
        }
        if ($size > 5*1024*1024) {
            http_response_code(400);
            exit('Image trop volumineuse (>5Mo)');
        }

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $new = 'ressources et consignes/img/'.uniqid().'.'.$ext; // adapte si besoin
        if (!move_uploaded_file($tmp, $new)) {
            http_response_code(500);
            exit('Erreur upload image');
        }
        $photo = $new;
    }

    if ($photo !== null && $photo !== '') {
        $stmt = $connexionDB->prepare("
      UPDATE produits
         SET nom = ?, description = ?, prix = ?, quantite_en_stock = ?, categorie = ?, marque = ?, statut = ?, photo_produit = ?
       WHERE id = ?
    ");
        $stmt->bind_param("ssdissssi", $nom, $desc, $prix, $qte, $cat, $marque, $statut, $photo, $id);
    } else {
        $stmt = $connexionDB->prepare("
      UPDATE produits
         SET nom = ?, description = ?, prix = ?, quantite_en_stock = ?, categorie = ?, marque = ?, statut = ?
       WHERE id = ?
    ");
        $stmt->bind_param("ssdisssi", $nom, $desc, $prix, $qte, $cat, $marque, $statut, $id);
    }

} else {
    // Assistant : description uniquement
    $stmt = $connexionDB->prepare("UPDATE produits SET description = ? WHERE id = ?");
    $stmt->bind_param("si", $desc, $id);
}

if (!$stmt) {
    http_response_code(500);
    exit('Erreur préparation requête');
}

if (!$stmt->execute()) {
    http_response_code(500);
    exit('Erreur exécution requête');
}

$stmt->close();
header('Location: infoProduitAdmin.php?id='.$id);
exit;

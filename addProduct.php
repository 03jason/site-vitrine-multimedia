<?php
// addProduct.php (TRAITEMENT)
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// accès réservé gérant
if (empty($_SESSION['LOGIN']) || empty($_SESSION['ADMIN'])) {
    $_SESSION['message_erreur'] = "Accès non autorisé.";
    header('Location: index.php'); exit;
}

require __DIR__ . '/connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: addProductPage.php'); exit;
}

// 1) Récup/validation
$nom   = trim($_POST['nom'] ?? '');
$description = trim($_POST['description'] ?? '');
$prix  = (float)($_POST['prix'] ?? 0);
$qte   = (int)($_POST['quantite_en_stock'] ?? 0);
$categorie = trim($_POST['categorie'] ?? '');
$marque    = trim($_POST['marque'] ?? '');
$statut    = $_POST['statut'] ?? 'disponible';

// statut autorisé par la consigne
$statutOk = in_array($statut, ['disponible','en rupture'], true);

if ($nom === '' || $description === '' || $prix <= 0 || $qte < 0 || !$statutOk) {
    $_SESSION['message_erreur'] = "Champs invalides.";
    header('Location: addProductPage.php'); exit;
}

// 2) Optionnel : empêcher doublon de nom
$sql_check = "SELECT COUNT(*) as c FROM produits WHERE nom = ?";
$stmt = $connexionDB->prepare($sql_check);
$stmt->bind_param("s", $nom);
$stmt->execute();
$res = $stmt->get_result();
$exists = ($res && ($row=$res->fetch_assoc())) ? (int)$row['c'] : 0;
$stmt->close();

if ($exists > 0) {
    $_SESSION['message_erreur'] = "Un produit avec ce nom existe déjà.";
    header('Location: addProductPage.php'); exit;
}

// 3) Upload image (optionnel)
$photo = null;
if (isset($_FILES['photo_produit']) && $_FILES['photo_produit']['error'] === UPLOAD_ERR_OK) {
    $tmp  = $_FILES['photo_produit']['tmp_name'];
    $name = $_FILES['photo_produit']['name'];
    $type = $_FILES['photo_produit']['type'];
    $size = $_FILES['photo_produit']['size'];

    $allowed = ['image/jpeg','image/png','image/gif'];
    if (in_array($type, $allowed) && $size <= 5*1024*1024) {
        $dir = 'ressources et consignes/img/';
        if (!is_dir($dir)) { @mkdir($dir, 0777, true); }

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $new = $dir . uniqid('prod_') . '.' . $ext;

        if (move_uploaded_file($tmp, $new)) {
            $photo = $new; // on stocke le chemin relatif complet
        }
        // si move échoue : $photo reste null -> insertion sans image
    }
    // si type/taille non valides : on ignore (image optionnelle)
}

// 4) Insertion
if ($photo === null) {
    $sql = "INSERT INTO produits
          (nom, description, prix, quantite_en_stock, categorie, marque,
           date_ajout, evaluation_moyenne, statut, photo_produit)
          VALUES
          (?,   ?,           ?,    ?,                 ?,        ?,
           NOW(),            0,     ?,      NULL)";
    $stmt = $connexionDB->prepare($sql);
    $stmt->bind_param("ssdisss", $nom, $description, $prix, $qte, $categorie, $marque, $statut);
} else {
    $sql = "INSERT INTO produits
          (nom, description, prix, quantite_en_stock, categorie, marque,
           date_ajout, evaluation_moyenne, statut, photo_produit)
          VALUES
          (?,   ?,           ?,    ?,                 ?,        ?,
           NOW(),            0,     ?,      ?)";
    $stmt = $connexionDB->prepare($sql);
    $stmt->bind_param("ssdissss", $nom, $description, $prix, $qte, $categorie, $marque, $statut, $photo);
}

if (!$stmt) {
    $_SESSION['message_erreur'] = "Erreur préparation requête.";
    header('Location: addProductPage.php'); exit;
}

if ($stmt->execute()) {
    $_SESSION['message_succes'] = "Produit « $nom » ajouté avec succès.";
} else {
    $_SESSION['message_erreur'] = "Erreur lors de l’ajout.";
}
$stmt->close();

header('Location: addProductPage.php'); exit;

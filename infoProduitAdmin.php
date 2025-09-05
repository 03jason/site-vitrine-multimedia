<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['LOGIN']) || $_SESSION['LOGIN'] !== true) {
    $productNameForRedirect = $_GET['nom'] ?? '';
    header('Location: infoProduit.php?nom=' . urlencode($productNameForRedirect));
    exit();
}
require_once 'connect.php';

$productName = $_GET['nom'] ?? null;

if ($productName === null) {
    $_SESSION['message_erreur'] = "Nom du produit manquant. Impossible d'afficher les détails en mode Admin.";
    header('Location: listAllProduct.php');
    exit();
}

$sql = "SELECT * FROM produits WHERE nom = ?";
$stmt = $connexionDB->prepare($sql);

if ($stmt === false) {
    error_log("Erreur de préparation de la requête SQL dans infoProduitAdmin.php: " . $connexionDB->error);
    $_SESSION['message_erreur'] = "Une erreur interne est survenue lors de la récupération des détails du produit.";
    header('Location: listAllProduct.php'); // Redirection vers le catalogue public par défaut
    exit();
}

$stmt->bind_param("s", $productName);
$stmt->execute();
$result = $stmt->get_result();

$productInfo = null;
if ($result->num_rows > 0) {
    $productInfo = $result->fetch_assoc();
} else {
    $_SESSION['message_erreur'] = "Produit introuvable.";
    header('Location: listAllProduct.php');
    exit();
}

$stmt->close();
$result->free();

$nom = htmlspecialchars($productInfo['nom'] ?? 'Produit Inconnu');
$prix = htmlspecialchars(number_format($productInfo['prix'] ?? 0.00, 2, ',', ' ') . ' €');
$description = htmlspecialchars($productInfo['description'] ?? 'Description non disponible.');
$quantite_en_stock = htmlspecialchars($productInfo['quantite_en_stock'] ?? 'N/A');
$evaluation_moyenne = htmlspecialchars($productInfo['evaluation_moyenne'] ?? '0');
$categorie = htmlspecialchars($productInfo['categorie'] ?? 'Non spécifiée');
$marque = htmlspecialchars($productInfo['marque'] ?? 'Non spécifiée');
$statut = htmlspecialchars($productInfo['statut'] ?? 'Indisponible');
$date_ajout = 'N/A'; // Par défaut
if (!empty($productInfo['date_ajout'])) {
    try {
        $dateTimeObj = new DateTime($productInfo['date_ajout']);
        $date_ajout = $dateTimeObj->format('d/m/Y H:i:s'); // Format plus précis pour l'admin
    } catch (Exception $e) {
        error_log("Erreur de format de date pour le produit " . $nom . ": " . $e->getMessage());
    }
}


$photo_produit_db = $productInfo['photo_produit'] ?? '';

$baseImagePath = 'ressources et consignes/img/';
$placeholderImageUrl = $baseImagePath . 'placeholder.jpg';
$finalImageUrl = $placeholderImageUrl;

if (!empty($photo_produit_db)) {
    $possibleFilenames = [];
    $possibleFilenames[] = $photo_produit_db;
    $possibleFilenames[] = str_replace('_', ' ', $photo_produit_db);
    $normalizedFilename = preg_replace('/[_\s]+/', ' ', $photo_produit_db);
    if (!in_array($normalizedFilename, $possibleFilenames)) {
        $possibleFilenames[] = $normalizedFilename;
    }

    foreach ($possibleFilenames as $testFilename) {
        $serverFilePath = __DIR__ . '/' . $baseImagePath . $testFilename;
        $browserUrl = $baseImagePath . urlencode($testFilename);
        if (file_exists($serverFilePath)) {
            $finalImageUrl = $browserUrl;
            break;
        }
    }
}
$imageProduct = htmlspecialchars($finalImageUrl);

include 'navBar.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du produit (ADMIN) - <?= $nom ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<main class="product-detail-page-main">
    <div class="product-detail-container admin-product-detail-container"> <div class="product-image-wrapper">
            <img src="<?= $imageProduct ?>" alt="<?= $nom ?>" class="product-detail-image">
        </div>

        <div class="product-info-section">
            <h1 class="product-detail-title"><?= $nom ?></h1>
            <div class="product-detail-meta">
                <span class="product-detail-category"><i class="fas fa-tag"></i> Catégorie : <?= $categorie ?></span>
                <span class="product-detail-brand"><i class="fas fa-industry"></i> Marque : <?= $marque ?></span>
            </div>
            <div class="product-detail-rating">
                <?php
                $rating = intval($evaluation_moyenne);
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $rating) {
                        echo '<i class="fas fa-star filled"></i>';
                    } else {
                        echo '<i class="far fa-star"></i>';
                    }
                }
                ?>
                <span class="rating-text"><?= $evaluation_moyenne ?> / 5</span>
            </div>

            <p class="product-detail-description"><?= nl2br($description) ?></p>

            <div class="product-detail-price"><?= $prix ?></div>



            <div class="product-detail-date-added">
                <span class="date-label"><i class="fas fa-calendar-alt"></i> Ajouté le :</span>
                <span><?= $date_ajout ?></span>
            </div>

            <div class="product-detail-admin-actions"> <a href="modificationProduitPage.php?nom=<?= urlencode($nom) ?>" class="btn admin-edit-button">
                    <i class="fas fa-edit"></i> Modifier le produit
                </a>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
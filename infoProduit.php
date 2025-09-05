<?php
// --- ZONE PHP : Connexion DB et récupération des données du produit ---

// 1. Démarrage de la session (TOUJOURS la toute première chose dans un fichier PHP)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- LOGIQUE DE REDIRECTION VERS LA VERSION ADMIN SI L'UTILISATEUR EST ADMIN ---
// Vérifie si l'utilisateur est connecté ET s'il a le statut d'administrateur
if (isset($_SESSION['LOGIN']) && $_SESSION['LOGIN'] === true) {
    // Si connecté (assistant ou gérant), on envoie vers la page admin
    $productNameForRedirect = $_GET['nom'] ?? '';
    header('Location: infoProduitAdmin.php?nom=' . urlencode($productNameForRedirect));
    exit();
}

// --- FIN DE LA LOGIQUE DE REDIRECTION VERS LA VERSION ADMIN ---


// 2. Inclusion du fichier de connexion à la base de données
require_once 'connect.php';

// 3. Récupération et validation du NOM du produit depuis l'URL
$productName = $_GET['nom'] ?? null;

// Gérer le cas où le nom du produit est manquant
if ($productName === null) {
    $_SESSION['message_erreur'] = "Nom du produit manquant. Impossible d'afficher les détails.";
    header('Location: listAllProduct.php'); // Rediriger vers le catalogue public
    exit();
}

// 4. Préparation et exécution de la requête SQL sécurisée (requête préparée)
// La requête sélectionne toutes les colonnes pour un produit donné par son NOM
$sql = "SELECT * FROM produits WHERE nom = ?"; // *** UTILISE BIEN 'nom' ***
$stmt = $connexionDB->prepare($sql);

if ($stmt === false) {
    error_log("Erreur de préparation de la requête SQL dans infoProduit.php: " . $connexionDB->error);
    $_SESSION['message_erreur'] = "Une erreur interne est survenue lors de la récupération des détails du produit.";
    header('Location: listAllProduct.php');
    exit();
}

$stmt->bind_param("s", $productName); // *** LIE LE PARAMÈTRE 'nom' (string) ***
$stmt->execute();
$result = $stmt->get_result();

// 5. Extraction des données du produit
$productInfo = null;
if ($result->num_rows > 0) {
    $productInfo = $result->fetch_assoc();
} else {
    $_SESSION['message_erreur'] = "Produit introuvable.";
    header('Location: listAllProduct.php');
    exit();
}

// 6. Fermeture de la déclaration et du résultat
$stmt->close();
$result->free();

// 7. Préparation des variables pour l'affichage HTML
$nom = htmlspecialchars($productInfo['nom'] ?? 'Produit Inconnu');
$prix = htmlspecialchars(number_format($productInfo['prix'] ?? 0.00, 2, ',', ' ') . ' €');
$description = htmlspecialchars($productInfo['description'] ?? 'Description non disponible.');
$quantite_en_stock = htmlspecialchars($productInfo['quantite_en_stock'] ?? 'N/A');
$evaluation_moyenne = htmlspecialchars($productInfo['evaluation_moyenne'] ?? '0');
$categorie = htmlspecialchars($productInfo['categorie'] ?? 'Non spécifiée');
$marque = htmlspecialchars($productInfo['marque'] ?? 'Non spécifiée');

$photo_produit_db = $productInfo['photo_produit'] ?? ''; // Nom de l'image de la DB

// Logique pour gérer le chemin de l'image (reprise de listAllProduct.php)
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
// --- FIN ZONE PHP ---
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du produit - <?= $nom ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<main class="product-detail-page-main">
    <div class="product-detail-container">
        <div class="product-image-wrapper">
            <img src="<?= $imageProduct ?>" alt="<?= $nom ?>" class="product-detail-image">
        </div>

        <div class="product-info-section">
            <h1 class="product-detail-title"><?= $nom ?></h1>
            <div class="product-detail-meta">
                <span class="product-detail-category"><i class="fas fa-tag"></i> <?= $categorie ?></span>
                <span class="product-detail-brand"><i class="fas fa-industry"></i> <?= $marque ?></span>
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


        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
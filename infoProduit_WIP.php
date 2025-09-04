<?php
// --- ZONE PHP : Connexion DB et récupération des données du produit ---

// 1. Démarrage de la session (TOUJOURS la toute première chose dans un fichier PHP)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- LOGIQUE DE REDIRECTION : Vers la vue Admin si l'utilisateur est un admin connecté ---
// Vérifie si l'utilisateur est connecté ET s'il a le statut d'administrateur
if (isset($_SESSION['LOGIN']) && $_SESSION['LOGIN'] === true && isset($_SESSION['ADMIN']) && $_SESSION['ADMIN'] === true) {
    // Si oui, on récupère le nom du produit depuis l'URL actuelle
    $productName = $_GET['nom'] ?? '';
    // Et on redirige vers infoProduitAdmin.php en passant le même nom de produit
    header('Location: infoProduitAdmin.php?nom=' . urlencode($productName));
    exit(); // TRÈS IMPORTANT : arrête l'exécution du script après la redirection
}
// --- FIN DE LA LOGIQUE DE REDIRECTION ---


// 2. Inclusion du fichier de connexion à la base de données
require_once 'connect.php';

// 3. Récupération et validation du nom du produit depuis l'URL
$productName = $_GET['nom'] ?? null;

// Gérer le cas où le nom du produit est manquant
if ($productName === null) {
    echo "Nom du produit manquant. Impossible d'afficher les détails.";
    exit(); // Arrête l'exécution si le produit n'est pas spécifié
}

// 4. Préparation et exécution de la requête SQL sécurisée (requête préparée)
// La requête sélectionne toutes les colonnes pour un produit donné par son nom
$sql = "SELECT * FROM produits WHERE nom = ?";
$stmt = $connexionDB->prepare($sql);

// Gestion des erreurs de préparation de la requête
if ($stmt === false) {
    error_log("Erreur de préparation de la requête SQL: " . $connexionDB->error);
    echo "Une erreur interne est survenue lors de la récupération des détails du produit.";
    exit();
}

// Liaison du paramètre (s = string pour le nom du produit)
$stmt->bind_param("s", $productName);

// Exécution de la requête
$stmt->execute();

// Récupération du résultat
$result = $stmt->get_result();

// 5. Extraction des données du produit
$productInfo = null;
if ($result->num_rows > 0) {
    $productInfo = $result->fetch_assoc();
} else {
    // Gérer le cas où le produit n'est pas trouvé
    echo "Produit introuvable.";
    exit();
}

// 6. Fermeture de la déclaration et du résultat
$stmt->close();
$result->free();

// 7. Préparation des variables pour l'affichage HTML
$nom = htmlspecialchars($productInfo['nom'] ?? 'Produit Inconnu');
$prix = htmlspecialchars(number_format($productInfo['prix'] ?? 0.00, 2, ',', ' ') . ' €'); // Formatage du prix
$description = htmlspecialchars($productInfo['description'] ?? 'Description non disponible.');
$quantite_en_stock = htmlspecialchars($productInfo['quantite_en_stock'] ?? 'N/A');
$evaluation_moyenne = htmlspecialchars($productInfo['evaluation_moyenne'] ?? 'N/A');
$photo_produit_db = $productInfo['photo_produit'] ?? ''; // Nom de l'image de la DB

// Logique pour gérer le chemin de l'image (reprise de listAllProduct.php)
$baseImagePath = 'ressources et consignes/img/';
$placeholderImageUrl = $baseImagePath . 'placeholder.jpg';
$finalImageUrl = $placeholderImageUrl; // Initialiser avec l'image par défaut

if (!empty($photo_produit_db)) {
    $possibleFilenames = [];
    $possibleFilenames[] = $photo_produit_db;
    $possibleFilenames[] = str_replace('_', ' ', $photo_produit_db);
    $normalizedFilename = preg_replace('/[_\s]+/', ' ', $photo_produit_db);
    if (!in_array($normalizedFilename, $possibleFilenames)) { // Évite les doublons
        $possibleFilenames[] = $normalizedFilename;
    }

    foreach ($possibleFilenames as $testFilename) {
        $serverFilePath = __DIR__ . '/' . $baseImagePath . $testFilename; // Chemin absolu sur le serveur
        $browserUrl = $baseImagePath . urlencode($testFilename); // URL pour le navigateur
        if (file_exists($serverFilePath)) {
            $finalImageUrl = $browserUrl; // Si trouvé, c'est cette URL qu'on va utiliser
            break; // Sortir de la boucle dès qu'on a trouvé le fichier
        }
    }
}
$imageProduct = htmlspecialchars($finalImageUrl);

include 'navbar.php';
// --- FIN ZONE PHP ---
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du produit - <?= $nom ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Styles spécifiques pour cette page, si non déjà dans styles.css */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }

        .product-container {
            display: flex;
            flex-direction: row;
            max-width: 1200px;
            margin: 40px auto;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .product-image-section {
            flex: 1;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #eee;
        }

        .product-image-section img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            display: block;
        }

        .product-details-section {
            flex: 1;
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .product-title {
            font-size: 2.5em;
            margin-bottom: 10px;
            color: #333;
        }

        .product-price {
            font-size: 1.8em;
            color: #007bff;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .product-reviews {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 20px;
        }

        .product-description {
            font-size: 1em;
            line-height: 1.6;
            color: #555;
            margin-bottom: 20px;
        }

        .product-quantity,
        .product-stock {
            font-size: 0.95em;
            color: #444;
            margin-bottom: 15px;
        }

        /* Responsive design pour les petits écrans */
        @media (max-width: 768px) {
            .product-container {
                flex-direction: column;
                margin: 20px;
            }
            .product-image-section,
            .product-details-section {
                padding: 15px;
            }
            .product-title {
                font-size: 2em;
            }
            .product-price {
                font-size: 1.5em;
            }
        }
    </style>
</head>
<body>

<?php // include 'navBar.php'; ?>

<div class="product-container">
    <div class="product-image-section">
        <img src="<?= $imageProduct ?>" alt="<?= $nom ?>">
    </div>

    <div class="product-details-section">
        <h1 class="product-title"><?= $nom ?></h1>
        <div class="product-price"><?= $prix ?></div>
        <div class="product-reviews"><?= $evaluation_moyenne ?> / 5 (<?= rand(1, 100) ?> Avis)</div>

        <p class="product-description"><?= nl2br($description) ?></p>

        <div class="product-stock">Quantité en stock: <?= $quantite_en_stock ?></div>

    </div>
</div>

</body>
</html>
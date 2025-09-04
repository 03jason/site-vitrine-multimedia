<?php

// 1. Démarrage de la session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Inclusion du fichier de connexion à la base de données
require_once ('connect.php');

// --- 3. Logique de tri des produits (côté serveur) ---

// 3.1. Définition des paramètres de tri par défaut
$currentSortBy = $_GET['sort_by'] ?? 'nom';
$currentOrder = $_GET['order'] ?? 'asc';

// 3.2. Sécurité : Liste blanche des colonnes et de l'ordre
// Suppression de 'id_produit' des colonnes autorisées pour le tri
$allowedSortColumns = [
    'nom', 'description', 'prix', 'quantite_en_stock', 'categorie',
    'marque', 'date_ajout', 'evaluation_moyenne', 'statut'
];
$allowedSortOrders = ['asc', 'desc'];

// 3.3. Validation des paramètres reçus de l'URL
if (!in_array($currentSortBy, $allowedSortColumns)) {
    $currentSortBy = 'nom';
}
if (!in_array($currentOrder, $allowedSortOrders)) {
    $currentOrder = 'asc';
}

// 3.4. Construction de la requête SQL dynamique
// ATTENTION : 'id_produit' est supprimé du SELECT
$demandeProduits = "SELECT nom, description, prix, quantite_en_stock, categorie, marque, date_ajout, evaluation_moyenne, statut, photo_produit FROM produits ORDER BY " . $currentSortBy . " " . $currentOrder;
$result = $connexionDB->query($demandeProduits);

// Gestion des erreurs
if ($result === false) {
    error_log("Erreur SQL dans listAllProduct.php: " . $connexionDB->error);
    $_SESSION['message_erreur'] = "Une erreur est survenue lors du chargement des produits. Veuillez réessayer plus tard.";
}


// --- 4. Initialisation des variables pour les cartes de produits ---
$imageProduit = null;
$nomProduit = null;
$prixProduit = null;
$evaluationProduit = null;
// Les variables suivantes ne sont pas nécessaires pour la carte de base, mais gardées pour cohérence si votre carte les utilise
$quantiteProduit = null;
$categorieProduit = null;
$marqueProduit = null;
$dateAjoutProduit = null;
$statutProduit = null;
// Suppression de $productId car il n'est pas nécessaire et n'est plus récupéré
// $productId = null;

// Définition du chemin de base pour les images (le dossier sur le serveur)
$baseImagePath = 'ressources et consignes/img/';
$placeholderImageUrl = $baseImagePath . 'placeholder.jpg';

$carteUtilisee = 'carteProduitBase.php' ;

// 5. Inclusion de la barre de navigation
include 'navBar.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notre Catalogue</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
<div class="container product-list-page">
    <h1 class="page-title">Notre Catalogue de Produits</h1>

    <?php if (isset($_SESSION['ADMIN']) && $_SESSION['ADMIN'] === true): ?>
        <div class="admin-switch-button-container">
            <a href="listAllProductAdmin.php" class="btn secondary-button admin-switch-button">
                <i class="fas fa-user-cog"></i> Passer en version Admin
            </a>
        </div>
    <?php endif; ?>

    <div class="sort-filter-section">
        <form action="listAllProduct.php" method="GET" class="sort-form">
            <label for="sort_by">Trier par :</label>
            <select name="sort_by" id="sort_by" class="form-select">
                <option value="nom" <?= ($currentSortBy === 'nom') ? 'selected' : '' ?>>Nom</option>
                <option value="prix" <?= ($currentSortBy === 'prix') ? 'selected' : '' ?>>Prix</option>
                <option value="evaluation_moyenne" <?= ($currentSortBy === 'evaluation_moyenne') ? 'selected' : '' ?>>Évaluation</option>
                <option value="date_ajout" <?= ($currentSortBy === 'date_ajout') ? 'selected' : '' ?>>Date d'ajout</option>
            </select>

            <label for="order">Ordre :</label>
            <select name="order" id="order" class="form-select">
                <option value="asc" <?= ($currentOrder === 'asc') ? 'selected' : '' ?>>Ascendant</option>
                <option value="desc" <?= ($currentOrder === 'desc') ? 'selected' : '' ?>>Descendant</option>
            </select>

            <button type="submit" class="btn primary-button apply-sort-button">Appliquer</button>
        </form>
    </div>


    <div class="product-cards-container">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($infoCarteProduit = $result->fetch_assoc()): ?>
                <?php
                // --- Préparation des données pour la carte de produit (LOGIQUE NON TOUCHÉE) ---

                $filenameFromDb = $infoCarteProduit['photo_produit'] ?? '';
                $finalImageUrl = $placeholderImageUrl;

                if (!empty($filenameFromDb)) {
                    $possibleFilenames = [];
                    $possibleFilenames[] = $filenameFromDb;
                    $possibleFilenames[] = str_replace('_', ' ', $filenameFromDb);
                    $normalizedFilename = preg_replace('/[_\s]+/', ' ', $filenameFromDb);
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

                $imageProduit = htmlspecialchars($finalImageUrl);
                $nomProduit = htmlspecialchars($infoCarteProduit['nom'] ?? 'Nom par défaut');
                $prixProduit = htmlspecialchars(number_format($infoCarteProduit['prix'] ?? 0, 2, ',', ' ') . ' €');
                $evaluationProduit = htmlspecialchars($infoCarteProduit['evaluation_moyenne'] ?? '0');
                // $productId est supprimé ici
                // $productId = htmlspecialchars($infoCarteProduit['id_produit'] ?? ''); // LIGNE SUPPRIMÉE

                $dateFromDb = $infoCarteProduit['date_ajout'] ?? '';
                if (!empty($dateFromDb)) {
                    $dateTimeObj = new DateTime($dateFromDb);
                    $formattedDate = $dateTimeObj->format('d/m/Y');
                } else {
                    $formattedDate = 'Date inconnue';
                }
                $dateAjoutProduit = htmlspecialchars($formattedDate);

                // --- FIN LOGIQUE NON TOUCHÉE ---

                // Inclusion du fichier de la carte
                include $carteUtilisee;
                ?>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-products-found-message">Aucun produit n'est disponible pour le moment.</p>
        <?php endif; ?>
    </div>

    <?php
    if ($result) {
        $result->free();
    }
    ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
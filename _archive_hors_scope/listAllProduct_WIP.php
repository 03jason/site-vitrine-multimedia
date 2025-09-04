
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
$demandeProduits = "SELECT * FROM produits ORDER BY " . $currentSortBy . " " . $currentOrder;
$result = $connexionDB->query($demandeProduits);

// Gestion des erreurs
if ($result === false) {
    error_log("Erreur SQL dans listAllProduct.php: " . $connexionDB->error);
    echo "Une erreur est survenue lors du chargement des produits.";
    exit();
}

// --- 4. Initialisation des variables pour les cartes de produits ---
$imageProduit = null;
$nomProduit = null;
$prixProduit = null;
$evaluationProduit = null;
$quantiteProduit = null;
$categorieProduit = null;
$marqueProduit = null;
$dateAjoutProduit = null;
$statutProduit = null;

// Définition du chemin de base pour les images (le dossier sur le serveur)
// ASSURE-TOI QUE CE CHEMIN EST CORRECT PAR RAPPORT À L'EMPLACEMENT DE CE FICHIER PHP !
// Par exemple, si listAllProduct.php est à la racine, et les images dans /ressources et consignes/img/
// alors c'est 'ressources et consignes/img/'.
// Si listAllProduct.php est dans un sous-dossier, tu auras peut-être besoin de '../ressources et consignes/img/'.
$baseImagePath = 'ressources et consignes/img/';

// Définition du chemin pour l'image de remplacement (URL pour le navigateur)
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
    <title>Liste de tous les produits</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

<h1>Liste de tous les produits</h1>

<a href="addProductPage.php" class="auth-button login-button">Ajouter un produit</a>

<div class="product-cards-container">
    <?php while ($infoCarteProduit = $result->fetch_assoc()): ?>
        <?php
        // --- Préparation des données pour la carte de produit ---

        // Nom du fichier image tel que stocké dans la base de données
        $filenameFromDb = $infoCarteProduit['photo_produit'] ?? '';

        // Chemin de l'image à afficher (initialisé avec l'image par défaut)
        $finalImageUrl = $placeholderImageUrl;

        // --- Logique de détection intelligente du nom de fichier ---
        if (!empty($filenameFromDb)) {
            // Variations possibles du nom de fichier sur le serveur
            $possibleFilenames = [];

            // 1. Version exacte de la DB (par ex: "razer_blackwidow_v3.jpg")
            $possibleFilenames[] = $filenameFromDb;

            // 2. Version avec underscores remplacés par des espaces (par ex: "razer blackwidow v3.jpg")
            $possibleFilenames[] = str_replace('_', ' ', $filenameFromDb);

            // 3. Version avec espaces remplacés par des underscores (moins courant si la DB gère les underscores)
            // Utile si tes fichiers réels ont un mix d'espaces et d'underscores, et la DB a des espaces.
            // $possibleFilenames[] = str_replace(' ', '_', $filenameFromDb);

            // 4. Version "normalisée" : remplace toutes les séquences d'espaces ou underscores par un seul espace
            // C'est celle que nous avons ajoutée la dernière fois, utile pour des cas complexes.
            // On peut l'inclure si les deux premières ne suffisent pas.
            $normalizedFilename = preg_replace('/[_\s]+/', ' ', $filenameFromDb);
            if (!in_array($normalizedFilename, $possibleFilenames)) { // Évite les doublons
                $possibleFilenames[] = $normalizedFilename;
            }

            // Parcourir les noms de fichiers possibles et vérifier leur existence
            foreach ($possibleFilenames as $testFilename) {
                // Construction du chemin complet vers le fichier sur le serveur (pour file_exists)
                $serverFilePath = __DIR__ . '/' . $baseImagePath . $testFilename; // __DIR__ pour le chemin absolu

                // Construction de l'URL pour le navigateur (avec encodage)
                $browserUrl = $baseImagePath . urlencode($testFilename);

                // Vérifier si le fichier existe réellement sur le serveur
                if (file_exists($serverFilePath)) {
                    $finalImageUrl = $browserUrl; // Si trouvé, c'est cette URL qu'on va utiliser
                    break; // Sortir de la boucle dès qu'on a trouvé le fichier
                }
            }
        }

        // Assignation et sécurisation de l'URL finale de l'image
        $imageProduit = htmlspecialchars($finalImageUrl);

        // Assignation et sécurisation des autres variables pour la carte
        $nomProduit = htmlspecialchars($infoCarteProduit['nom'] ?? 'Nom par défaut');
        $prixProduit = htmlspecialchars($infoCarteProduit['prix'] ?? '00.00 €');
        $evaluationProduit = htmlspecialchars($infoCarteProduit['evaluation_moyenne'] ?? '5 / 5');

        // Formatage de la date pour l'affichage européen
        $dateFromDb = $infoCarteProduit['date_ajout'] ?? '';
        if (!empty($dateFromDb)) {
            $dateTimeObj = new DateTime($dateFromDb);
            $formattedDate = $dateTimeObj->format('d/m/Y');
        } else {
            $formattedDate = 'Date inconnue';
        }
        $dateAjoutProduit = htmlspecialchars($formattedDate);

        // Inclusion du fichier de la carte
        include $carteUtilisee;

        // --- DEBUGGING : À retirer une fois que tout fonctionne ---

        echo "<pre>";
        echo "Nom Produit: " . $nomProduit . "<br>";
        echo "Filename from DB: " . $filenameFromDb . "<br>";
        echo "Possible Filenames (testés): <br>";
        foreach ($possibleFilenames as $pf) {
            echo " - " . $pf . " -> URL: " . $baseImagePath . urlencode($pf) . " -> Existe: " . (file_exists(__DIR__ . '/' . $baseImagePath . $pf) ? 'OUI' : 'NON') . "<br>";
        }
        echo "URL Finale utilisée: " . $finalImageUrl . "<br>";
        echo "-------------------------------------</pre>";

        // --- FIN DEBUGGING ---
        ?>
    <?php endwhile; ?>
</div>

<?php
// 8. Libérer le jeu de résultats
if ($result) {
    $result->free();
}
?>

</body>

<footer>
</footer>
</html>
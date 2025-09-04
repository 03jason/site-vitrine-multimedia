<?php
// --- ZONE PHP : Récupération des données du produit à modifier ---

// 1. Démarrage de la session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Vérification des droits d'administrateur (inchangé)
if (!isset($_SESSION['LOGIN']) || $_SESSION['LOGIN'] !== true || !isset($_SESSION['ADMIN']) || $_SESSION['ADMIN'] !== true) {
    header('Location: loginPage.php');
    exit();
}

// 3. Inclusion du fichier de connexion à la base de données (inchangé)
require_once 'connect.php';

// 4. Récupération et validation du NOM du produit depuis l'URL
// CHANGEMENT ICI : on récupère 'nom' au lieu de 'id'
$productName = $_GET['nom'] ?? null;

if ($productName === null || empty($productName)) {
    $_SESSION['message_erreur'] = "Nom de produit manquant ou invalide pour la modification.";
    header('Location: listAllProductAdmin.php');
    exit();
}

// 5. Préparation et exécution de la requête SQL pour récupérer les infos du produit
// CHANGEMENT ICI : la condition WHERE utilise 'nom'
$sql = "SELECT *  FROM produits WHERE nom = ?";
$stmt = $connexionDB->prepare($sql);

if ($stmt === false) {
    error_log("Erreur de préparation de la requête SQL (modificationProduitPage.php): " . $connexionDB->error);
    $_SESSION['message_erreur'] = "Une erreur interne est survenue lors de la récupération des détails du produit.";
    header('Location: listAllProductAdmin.php');
    exit();
}

// CHANGEMENT ICI : on lie $productName (string)
$stmt->bind_param("s", $productName); // "s" pour string (Nom du produit)
$stmt->execute();
$result = $stmt->get_result();
$productInfo = $result->fetch_assoc();

// Gérer le cas où le produit n'est pas trouvé
if ($productInfo === null) {
    $_SESSION['message_erreur'] = "Produit introuvable pour le nom spécifié: " . htmlspecialchars($productName) . ".";
    header('Location: listAllProductAdmin.php');
    exit();
}

// 6. Fermeture de la déclaration (inchangé)
$stmt->close();

// 7. Préparation des variables pour l'affichage HTML du formulaire (inchangé, sauf que $nom vient de $productInfo)
$nom = htmlspecialchars($productInfo['nom'] ?? 'Produit Inconnu');
$prix = htmlspecialchars($productInfo['prix'] ?? 0.00);
$description = htmlspecialchars($productInfo['description'] ?? 'Description non disponible.');
$quantite_en_stock = htmlspecialchars($productInfo['quantite_en_stock'] ?? 0);
$evaluation_moyenne = htmlspecialchars($productInfo['evaluation_moyenne'] ?? 0.0);
$photo_produit_db = $productInfo['photo_produit'] ?? '';

// Logique pour gérer le chemin de l'image (inchangé)
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
        $serverFilePath = __DIR__ . 'modificationProduitPage.php/' . $baseImagePath . $testFilename;
        $browserUrl = $baseImagePath . urlencode($testFilename);
        if (file_exists($serverFilePath)) {
            $finalImageUrl = $browserUrl;
            break;
        }
    }
}
$imageProduct = htmlspecialchars($finalImageUrl);

// --- FIN ZONE PHP ---
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Produit : <?= $nom ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<?php include 'navBar.php'; ?>

<div class="container edit-product-container">
    <h1 class="page-title">Modifier le produit : <span><?= $nom ?></span></h1>

    <?php
    // Affichage des messages de succès ou d'erreur
    if (isset($_SESSION['message_succes'])): ?>
        <div class="message-success">
            <?= $_SESSION['message_succes'];
            unset($_SESSION['message_succes']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['message_erreur'])): ?>
        <div class="message-error">
            <?= $_SESSION['message_erreur'];
            unset($_SESSION['message_erreur']); ?>
        </div>
    <?php endif; ?>

    <form id="editProductForm" action="modifProduit.php" method="POST" enctype="multipart/form-data" class="product-edit-form">
        <input type="hidden" name="current_product_name" value="<?= htmlspecialchars($productInfo['nom'] ?? '') ?>">
        <input type="hidden" name="old_product_image" value="<?= htmlspecialchars($photo_produit_db ?? '') ?>">

        <div class="form-group">
            <label for="edit_nom" class="form-label">Nom du produit:</label>
            <input type="text" id="edit_nom" name="nom" class="form-input" value="<?= $nom ?>" required>
        </div>

        <div class="form-group">
            <label for="edit_prix" class="form-label">Prix (€):</label>
            <input type="number" id="edit_prix" name="prix" step="0.01" class="form-input" value="<?= $prix ?>" required>
        </div>

        <div class="form-group">
            <label for="edit_description" class="form-label">Description:</label>
            <textarea id="edit_description" name="description" rows="5" class="form-textarea" required><?= $description ?></textarea>
        </div>

        <div class="form-group">
            <label for="edit_quantite" class="form-label">Quantité en stock:</label>
            <input type="number" id="edit_quantite" name="quantite_en_stock" class="form-input" value="<?= $quantite_en_stock ?>" required>
        </div>

        <div class="form-group">
            <label for="edit_evaluation" class="form-label">Évaluation moyenne (0-5):</label>
            <input type="number" id="edit_evaluation" name="evaluation_moyenne" step="0.1" min="0" max="5" class="form-input" value="<?= $evaluation_moyenne ?>">
        </div>

        <div class="form-group image-upload-group">
            <label class="form-label">Image du produit actuelle:</label>
            <div class="current-image-preview">
                <img id="currentProductImage" src="<?= $imageProduct ?>" alt="Image actuelle du produit">
            </div>
            <label for="edit_image" class="form-label file-input-label">Changer l'image:</label>
            <input type="file" id="edit_image" name="product_image" accept="image/*" class="file-input">
        </div>

        <div class="form-actions">
            <button type="submit" class="primary-button">Sauvegarder les modifications</button>
            <a href="listAllProductAdmin.php" class="btn secondary-button">Annuler et Retour à la liste</a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
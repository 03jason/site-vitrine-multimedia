<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if (empty($_SESSION['LOGIN']) || empty($_SESSION['ADMIN'])) {
    $_SESSION['message_erreur'] = "Accès non autorisé. Vous devez être administrateur pour ajouter un produit.";
    header('Location: index.php'); exit();
}

require_once 'connect.php';
include 'navBar.php';

$message = '';
if (!empty($_SESSION['message_succes'])) {
    $message = '<p class="message success">'.htmlspecialchars($_SESSION['message_succes']).'</p>';
    unset($_SESSION['message_succes']);
} elseif (!empty($_SESSION['message_erreur'])) {
    $message = '<p class="message error">'.htmlspecialchars($_SESSION['message_erreur']).'</p>';
    unset($_SESSION['message_erreur']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Ajouter un nouveau produit</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<main class="add-product-page-main">
    <div class="add-product-container">
        <h2>Ajouter un nouveau produit</h2>

        <?php if (!empty($message)): ?>
            <div class="form-message"><?= $message ?></div>
        <?php endif; ?>

        <form action="addProduct.php" method="POST" enctype="multipart/form-data" class="add-product-form">
            <div class="form-group">
                <label for="nom">Nom du produit <span class="required">*</span></label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="5"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="prix">Prix (€) <span class="required">*</span></label>
                <input type="number" id="prix" name="prix" step="0.01" min="0" value="<?= htmlspecialchars($_POST['prix'] ?? '0.00') ?>" required>
            </div>

            <div class="form-group">
                <label for="quantite_en_stock">Quantité en stock <span class="required">*</span></label>
                <input type="number" id="quantite_en_stock" name="quantite_en_stock" min="0" value="<?= htmlspecialchars($_POST['quantite_en_stock'] ?? '0') ?>" required>
            </div>

            <div class="form-group">
                <label for="categorie">Catégorie</label>
                <input type="text" id="categorie" name="categorie" value="<?= htmlspecialchars($_POST['categorie'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="marque">Marque</label>
                <input type="text" id="marque" name="marque" value="<?= htmlspecialchars($_POST['marque'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="photo_produit">Photo du produit (optionnel)</label>
                <input type="file" id="photo_produit" name="photo_produit" accept="image/*">
                <small>Formats acceptés : JPG, JPEG, PNG, GIF — max 5 Mo</small>
            </div>

            <div class="form-group">
                <label for="statut">Statut</label>
                <select id="statut" name="statut">
                    <option value="disponible" <?= (($_POST['statut'] ?? '') == 'disponible') ? 'selected' : '' ?>>Disponible</option>
                    <option value="en rupture" <?= (($_POST['statut'] ?? '') == 'en rupture') ? 'selected' : '' ?>>En rupture</option>
                </select>
            </div>

            <button type="submit" class="btn primary-button submit-button">
                <i class="fas fa-plus-circle"></i> Ajouter le produit
            </button>
        </form>
    </div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>

<?php

require_once ('connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['LOGIN']) || $_SESSION['LOGIN'] !== true ) {
    header('Location: loginPage.php'); // Rediriger si non connecté ou non admin
    exit();
}

$currentSortBy = $_GET['sort_by'] ?? 'nom'; // Colonne par défaut
$currentOrder = $_GET['order'] ?? 'asc'; // Direction par défaut

$allowedSortColumns = ['id', 'nom', 'prix', 'quantite_en_stock', 'categorie', 'marque', 'date_ajout', 'evaluation_moyenne', 'statut', 'description', 'photo_produit']; // Permet de trier sur toutes les colonnes
if (!in_array($currentSortBy, $allowedSortColumns)) {
    $currentSortBy = 'nom';
}

$allowedOrderDirections = ['asc', 'desc'];
if (!in_array($currentOrder, $allowedOrderDirections)) {
    $currentOrder = 'asc';
}

$sql = "SELECT * FROM produits ORDER BY " . $currentSortBy . " " . strtoupper($currentOrder) . ";";

$result = $connexionDB->query($sql);

if ($result === false) {
    error_log("Erreur lors de la récupération des produits: " . $connexionDB->error);
    $products = [];
    $_SESSION['message_erreur'] = "Une erreur est survenue lors du chargement des produits. Veuillez réessayer plus tard.";
} else {
    $products = $result->fetch_all(MYSQLI_ASSOC); // Récupérer tous les résultats en une fois
}


include 'navBar.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration des produits</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div class="container admin-page-container">
    <h1 class="page-title">Gestion des produits</h1>

    <div class="admin-top-actions">
        <a href="listAllProduct.php" class="btn secondary-button admin-back-button">
            <i class="fas fa-eye"></i> Retourner au catalogue public
        </a>
        <a href="addProductPage.php" class="btn primary-button add-product-button">
            <i class="fas fa-plus-circle"></i> Ajouter un produit
        </a>
    </div>

    <div class="sort-filter-section admin-sort-filter">
        <form action="listAllProductAdmin.php" method="GET" class="sort-form">
            <label for="sort_by">Trier par :</label>
            <select name="sort_by" id="sort_by" class="form-select">
                <option value="nom" <?= ($currentSortBy === 'nom') ? 'selected' : '' ?>>Nom</option>
                <option value="prix" <?= ($currentSortBy === 'prix') ? 'selected' : '' ?>>Prix</option>
                <option value="quantite_en_stock" <?= ($currentSortBy === 'quantite_en_stock') ? 'selected' : '' ?>>Quantité</option>
                <option value="categorie" <?= ($currentSortBy === 'categorie') ? 'selected' : '' ?>>Catégorie</option>
                <option value="marque" <?= ($currentSortBy === 'marque') ? 'selected' : '' ?>>Marque</option>
                <option value="date_ajout" <?= ($currentSortBy === 'date_ajout') ? 'selected' : '' ?>>Date d'ajout</option>
                <option value="evaluation_moyenne" <?= ($currentSortBy === 'evaluation_moyenne') ? 'selected' : '' ?>>Évaluation</option>
                <option value="statut" <?= ($currentSortBy === 'statut') ? 'selected' : '' ?>>Statut</option>
                <option value="id" <?= ($currentSortBy === 'id') ? 'selected' : '' ?>>ID Produit</option>
            </select>

            <label for="order">Ordre :</label>
            <select name="order" id="order" class="form-select">
                <option value="asc" <?= ($currentOrder === 'asc') ? 'selected' : '' ?>>Ascendant</option>
                <option value="desc" <?= ($currentOrder === 'desc') ? 'selected' : '' ?>>Descendant</option>
            </select>

            <button type="submit" class="btn primary-button apply-sort-button">Appliquer le tri</button>
        </form>
    </div>

    <?php if (isset($_SESSION['message_erreur'])): ?>
        <div class="error-message">
            <?= htmlspecialchars($_SESSION['message_erreur']); ?>
            <?php unset($_SESSION['message_erreur']);  ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="admin-product-table">
            <thead>
            <tr>
                <th>Nom</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Catégorie</th>
                <th>Marque</th>
                <th>Date d'ajout</th>
                <th>Évaluation</th>
                <th>Statut</th>
                <th class="table-actions-col">Actions</th> </tr>
            </thead>
            <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="8" class="no-products-message">Aucun produit trouvé.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $infoProduit): ?>
                    <tr>
                        <td><?= htmlspecialchars($infoProduit['nom'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars(number_format($infoProduit['prix'] ?? 0, 2, ',', ' ') . ' €') ?></td>
                        <td><?= htmlspecialchars($infoProduit['quantite_en_stock'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($infoProduit['categorie'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($infoProduit['marque'] ?? 'N/A') ?></td>
                        <?php
                        $dateFromDb = $infoProduit['date_ajout'] ?? '';
                        $formattedDate = 'N/A';
                        if (!empty($dateFromDb)) {
                            try {
                                $dateTimeObj = new DateTime($dateFromDb);
                                $formattedDate = $dateTimeObj->format('d/m/Y');
                            } catch (Exception $e) {
                                error_log("Erreur de date pour le produit " . ($infoProduit['id'] ?? 'inconnu') . ": " . $e->getMessage());
                            }
                        }
                        ?>
                        <td><?= htmlspecialchars($formattedDate) ?></td>
                        <td><?= htmlspecialchars($infoProduit['evaluation_moyenne'] ?? 'N/A') ?> / 5</td>
                        <td><?= htmlspecialchars($infoProduit['statut'] ?? 'N/A') ?></td>

                        <td class="table-actions">
                            <a href="modificationProduitPage.php?nom=<?= urlencode($infoProduit['nom'] ?? '') ?>"
                               class="btn action-button edit-button" title="Modifier ce produit">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="infoProduit.php?nom=<?= urlencode($infoProduit['nom'] ?? '') ?>"
                               class="btn action-button details-button" title="Voir les détails du produit (admin)">
                                <i class="fas fa-info-circle"></i> Détails
                            </a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
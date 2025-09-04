<?php

// Assurez-vous que ces variables sont bien passées à cette page
$imageProduit = htmlspecialchars($imageProduit ?? 'placeholder.jpg');
$nomProduit = htmlspecialchars($nomProduit ?? 'Nom par défaut');
$prixProduit = htmlspecialchars($prixProduit ?? '00.00 €');
$evaluationProduit = htmlspecialchars($evaluationProduit ?? '0 / 5'); // Assurez-vous que c'est un chiffre

?>

<?php
// Cette page est incluse, elle ne doit donc pas avoir de balises <html>, <head>, etc.
// Elle reçoit les variables suivantes de la page appelante (index.php ou listAllProduct.php):
// $imageProduit, $nomProduit, $prixProduit, $evaluationProduit
?>

<div class="custom-product-card">
    <div class="product-card-image-wrapper">
        <img src="<?= $imageProduit ?>" alt="<?= $nomProduit ?>" class="product-card-image">
    </div>
    <div class="product-card-content">
        <h3 class="product-card-title"><?= $nomProduit ?></h3>
        <p class="product-card-price"><?= $prixProduit ?></p>
        <div class="product-card-rating">
            <?php
            // Affichage des étoiles basé sur l'évaluation (0 à 5)
            // Assurez-vous que $evaluationProduit est un chiffre ici (ex: 4, non "4 / 5")
            $rating = intval($evaluationProduit);
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $rating) {
                    echo '<i class="fas fa-star filled"></i>'; // Étoile pleine
                } else {
                    echo '<i class="far fa-star"></i>'; // Étoile vide (contour)
                }
            }
            ?>
        </div>
        <a href="infoProduit.php?nom=<?= urlencode($nomProduit) ?>" class="btn custom-action-button product-details-button">Détails Produit</a>
    </div>
</div>
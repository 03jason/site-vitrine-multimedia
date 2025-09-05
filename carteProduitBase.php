<?php

$imageProduit = htmlspecialchars($imageProduit ?? 'placeholder.jpg');
$nomProduit = htmlspecialchars($nomProduit ?? 'Nom par défaut');
$prixProduit = htmlspecialchars($prixProduit ?? '00.00 €');
$evaluationProduit = htmlspecialchars($evaluationProduit ?? '0 / 5');

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
            $rating = intval($evaluationProduit);
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $rating) {
                    echo '<i class="fas fa-star filled"></i>';
                } else {
                    echo '<i class="far fa-star"></i>';
                }
            }
            ?>
        </div>
        <a href="infoProduit.php?nom=<?= urlencode($nomProduit) ?>" class="btn custom-action-button product-details-button">Voir plus</a>
    </div>
</div>
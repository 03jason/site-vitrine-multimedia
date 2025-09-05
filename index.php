<?php

require_once ('connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$sqlProduitsAccueil = "SELECT * FROM produits WHERE statut = 'disponible' LIMIT 6;";
$result = $connexionDB->query($sqlProduitsAccueil);


$imageProduit = null;
$nomProduit = null;
$prixProduit = null;
$evaluationProduit = null;
$quantiteProduit = null;
$categorieProduit = null;
$marqueProduit = null;
$dateAjoutProduit = null;
$statutProduit = null;

$carteUtilisee = 'carteProduitBase.php';

include 'navBar.php';
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Référencement  -->
    <meta name="description" content="IT_SHP : votre vitrine de produits multimédias et électroniques. Découvrez notre sélection.">
    <meta name="keywords" content="technologie, électronique, multimédia, IT_SHP">
    <title>IT_SHP - Accueil</title>

</head>
<body>

<main>
    <section class="hero-section full-width-image-section" style="background-image: url('img_test/it_shop_front_page.jpg');">
        <div class="hero-content image-overlay">
            <h1>Une qualité certaine</h1>
            <p>Découvrez notre gamme de produits multimédia, soigneusement sélectionnés pour répondre à toutes vos exigences.</p>
        </div>
    </section>

    <section class="best-products-section">
        <div class="container best-products-layout">
            <div class="product-grid">
                <?php while ($infoCarteProduit = $result->fetch_assoc()): ?>
                    <?php
                    $placeholderImagePath = "ressources et consignes/img/placeholder.jpg";
                    $filenameFromDb = $infoCarteProduit['photo_produit'] ?? '';

                    $serverFilePath = __DIR__ . '/ressources et consignes/img/' . $filenameFromDb;
                    $browserUrlPath = 'ressources et consignes/img/' . rawurlencode($filenameFromDb);

                    $finalImagePath = file_exists($serverFilePath) ? $browserUrlPath : $placeholderImagePath;

                    $imageProduit = htmlspecialchars($finalImagePath);
                    $nomProduit = htmlspecialchars($infoCarteProduit['nom'] ?? 'Nom par défaut');
                    $prixProduit = htmlspecialchars(number_format($infoCarteProduit['prix'] ?? 0.00, 2, ',', ' ') . ' €');
                    $evaluationProduit = htmlspecialchars($infoCarteProduit['evaluation_moyenne'] ?? '0');

                    $dateFromDb = $infoCarteProduit['date_ajout'] ?? null;
                    $formattedDate = 'N/A';
                    if ($dateFromDb) {
                        try {
                            $dateTimeObj = new DateTime($dateFromDb);
                            $formattedDate = $dateTimeObj->format('d/m/Y');
                        } catch (Exception $e) {
                        }
                    }

                    $quantiteProduit = htmlspecialchars($infoCarteProduit['quantite_en_stock'] ?? '0');
                    $categorieProduit = htmlspecialchars($infoCarteProduit['categorie'] ?? '');
                    $marqueProduit = htmlspecialchars($infoCarteProduit['marque'] ?? '');
                    $dateAjoutProduit = htmlspecialchars($formattedDate);
                    $statutProduit = htmlspecialchars($infoCarteProduit['statut'] ?? '');

                    include $carteUtilisee;
                    ?>
                <?php endwhile; ?>

                <?php
                if ($result) {
                    $result->free();
                }
                ?>
            </div>

            <div class="best-products-sidebar">
                <h2 class="sidebar-title">Découvrez l'ensemble de nos produits</h2>
                <p class="sidebar-description">
                    Plongez dans notre catalogue complet pour trouver l'article multimédia
                    parfait qui correspond à vos besoins et à votre style de vie.
                    Qualité et innovation garanties !</p>
                <a href="listAllProduct.php" class="btn primary-button view-all-button">Voir tout le catalogue</a>
            </div>
        </div>
    </section>

    <section class="about-us-section">
        <div class="container about-us-content">
            <h2 class="section-title">À Propos de Nous</h2>
            <p><strong>IT_SHP</strong> met en avant une sélection d’équipements et d’accessoires multimédia choisis pour leur fiabilité et leur qualité.
                Notre site vitrine a pour objectif de présenter nos produits de manière claire et professionnelle</p>

            <p>Notre équipe reste à votre disposition pour répondre à vos questions et vous accompagner dans vos projets technologiques.
                Découvrez notre univers et n’hésitez pas à nous contacter pour en savoir plus.</p>
        </div>
    </section>


</main>

<?php include 'footer.php'; ?>

</body>
</html>
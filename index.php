<?php

require_once ('connect.php');

// Assurez-vous que session_start() est LA PREMIERE chose
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Remarque: $demande6Produit n'est pas utilisé directement ici,
// mais la requête $connexionDB->query("SELECT * FROM produits LIMIT 6 OFFSET 1;"); l'est.
// Il est préférable de n'avoir qu'une seule requête pour la cohérence.
// La requête récupère 6 produits à partir du deuxième (OFFSET 1)
$sqlProduitsAccueil = "SELECT * FROM produits LIMIT 6 OFFSET 1;";
$result = $connexionDB->query($sqlProduitsAccueil);


// --- Variables pour carteProduitBase.php ---
// Ces variables sont initialisées ici mais seront écrasées dans la boucle.
$imageProduit = null;
$nomProduit = null;
$prixProduit = null;
$evaluationProduit = null;
$quantiteProduit = null;
$categorieProduit = null;
$marqueProduit = null;
$dateAjoutProduit = null;
$statutProduit = null;

$carteUtilisee = 'carteProduitBase.php'; // Nom du fichier de la carte produit

include 'navBar.php';
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Vitrine JF</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                    // Logique pour déterminer le chemin de l'image du produit
                    $placeholderImagePath = "ressources et consignes/img/placeholder.jpg";
                    $filenameFromDb = $infoCarteProduit['photo_produit'] ?? '';

                    // Vérification de l'existence du fichier sur le serveur
                    $serverFilePath = __DIR__ . '/ressources et consignes/img/' . $filenameFromDb;
                    // Encodage du nom de fichier pour l'URL, surtout si des espaces ou caractères spéciaux sont présents
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
                            // En cas d'erreur de date, $formattedDate reste 'N/A'
                        }
                    }

                    $quantiteProduit = htmlspecialchars($infoCarteProduit['quantite_en_stock'] ?? '0');
                    $categorieProduit = htmlspecialchars($infoCarteProduit['categorie'] ?? '');
                    $marqueProduit = htmlspecialchars($infoCarteProduit['marque'] ?? '');
                    $dateAjoutProduit = htmlspecialchars($formattedDate);
                    $statutProduit = htmlspecialchars($infoCarteProduit['statut'] ?? '');

                    // Incluez la carte du produit
                    include $carteUtilisee;
                    ?>
                <?php endwhile; ?>

                <?php
                // N'oubliez pas de libérer le jeu de résultats après la boucle
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
            <h2 class="section-title">À Propos de Notre Entreprise</h2>
            <p>Bienvenue chez **TechPulse**, votre destination de confiance pour l'électronique grand public de pointe. Depuis notre fondation, nous nous engageons à offrir à nos clients une sélection inégalée de produits multimédia, allant des derniers smartphones et ordinateurs portables aux équipements audio et vidéo de haute performance.</p>
            <p>Notre mission est de simplifier l'accès à la technologie tout en garantissant une qualité irréprochable et un service client exceptionnel. Nous collaborons avec les marques les plus réputées pour vous apporter des innovations qui enrichissent votre quotidien, que ce soit pour le travail, le divertissement ou la communication.</p>
            <p>Chez TechPulse, nous croyons en une approche personnalisée. Notre équipe d'experts est toujours prête à vous conseiller et à vous guider pour faire le meilleur choix. Votre satisfaction est notre priorité absolue, et nous nous efforçons de construire une relation de confiance durable avec chacun de nos clients. Merci de choisir TechPulse pour vos besoins technologiques !</p>
        </div>
    </section>

</main>

<?php include 'footer.php'; ?>

</body>
</html>
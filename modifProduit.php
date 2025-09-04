<?php
// --- ZONE PHP : Traitement du formulaire de modification ---

// 1. Démarrage de la session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Vérification des droits d'administrateur (inchangé)
if (!isset($_SESSION['LOGIN']) || $_SESSION['LOGIN'] !== true || !isset($_SESSION['ADMIN']) || $_SESSION['ADMIN'] !== true) {
    $_SESSION['message_erreur'] = "Accès non autorisé.";
    header('Location: loginPage.php');
    exit();
}

// 3. Inclusion du fichier de connexion à la base de données (inchangé)
require_once 'connect.php';

// 4. Vérifier si le formulaire a été soumis via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et sécuriser les données du formulaire
    // CHANGEMENT ICI : Récupérer 'current_product_name' comme identifiant
    $currentProductName = $_POST['current_product_name'] ?? null;
    $nom = $_POST['nom'] ?? null; // C'est le NOUVEAU nom du produit
    $prix = $_POST['prix'] ?? null;
    $description = $_POST['description'] ?? null;
    $quantite_en_stock = $_POST['quantite_en_stock'] ?? null;
    $evaluation_moyenne = $_POST['evaluation_moyenne'] ?? null;
    $oldProductImageName = $_POST['old_product_image'] ?? ''; // Nom de l'image précédente (de la DB)

    // Vérification basique des données
    // CHANGEMENT ICI : validation sur $currentProductName et $nom
    if ($currentProductName === null || empty($currentProductName) || empty($nom) || !is_numeric($prix)) {
        $_SESSION['message_erreur'] = "Données du formulaire incomplètes ou invalides.";
        // Rediriger vers la page d'édition avec le nom du produit pour qu'il ne se perde pas
        // Utilisez $currentProductName pour la redirection en cas d'erreur
        header('Location: modificationProduitPage.php?nom=' . urlencode($currentProductName));
        exit();
    }

    // Chemin de base des images (inchangé)
    $uploadDirectory = 'ressources et consignes/img/';
    $newProductImageName = $oldProductImageName; // Par défaut, on garde l'ancien nom d'image

    // 5. Gérer l'upload de la nouvelle image si elle a été fournie (inchangé)
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['product_image']['tmp_name'];
        $fileName = $_FILES['product_image']['name'];
        $fileSize = $_FILES['product_image']['size'];
        $fileType = $_FILES['product_image']['type'];

        // Vérifications de base du fichier (taille, type)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 5 * 1024 * 1024; // 5 MB

        if (!in_array($fileType, $allowedTypes)) {
            $_SESSION['message_erreur'] = "Type de fichier non autorisé. Seules les images JPEG, PNG, GIF sont acceptées.";
            header('Location: modificationProduitPage.php?nom=' . urlencode($currentProductName));
            exit();
        }
        if ($fileSize > $maxFileSize) {
            $_SESSION['message_erreur'] = "Le fichier image est trop volumineux (max 5 Mo).";
            header('Location: modificationProduitPage.php?nom=' . urlencode($currentProductName));
            exit();
        }

        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newProductImageName = uniqid() . '_' . md5(time() . $fileName) . '.' . $fileExtension;
        $destPath = $uploadDirectory . $newProductImageName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            if (!empty($oldProductImageName) && file_exists($uploadDirectory . $oldProductImageName) && $oldProductImageName !== $newProductImageName) {
                if (unlink($uploadDirectory . $oldProductImageName)) {
                    error_log("Ancienne image '" . $oldProductImageName . "' supprimée avec succès.");
                } else {
                    error_log("Erreur lors de la suppression de l'ancienne image : " . $uploadDirectory . $oldProductImageName);
                }
            }
        } else {
            error_log("Erreur lors du déplacement du fichier téléchargé : " . $fileName . " Erreur code: " . $_FILES['product_image']['error']);
            $_SESSION['message_erreur'] = "Une erreur est survenue lors de l'upload de l'image. Code: " . $_FILES['product_image']['error'];
            header('Location: modificationProduitPage.php?nom=' . urlencode($currentProductName));
            exit();
        }
    } else if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploadErrorMessages = [
            UPLOAD_ERR_INI_SIZE   => 'Le fichier téléchargé excède la taille maximale autorisée par le serveur (php.ini).',
            UPLOAD_ERR_FORM_SIZE  => 'Le fichier téléchargé excède la taille maximale spécifiée dans le formulaire HTML.',
            UPLOAD_ERR_PARTIAL    => 'Le fichier n\'a été que partiellement téléchargé.',
            UPLOAD_ERR_NO_TMP_DIR => 'Un dossier temporaire est manquant.',
            UPLOAD_ERR_CANT_WRITE => 'Échec de l\'écriture du fichier sur le disque.',
            UPLOAD_ERR_EXTENSION  => 'Une extension PHP a arrêté l\'upload du fichier.'
        ];
        $errorCode = $_FILES['product_image']['error'];
        $_SESSION['message_erreur'] = "Erreur d'upload: " . ($uploadErrorMessages[$errorCode] ?? 'Erreur inconnue.');
        header('Location: modificationProduitPage.php?nom=' . urlencode($currentProductName));
        exit();
    }


    // 6. Mettre à jour les données dans la base de données
    // CHANGEMENT ICI : La condition WHERE utilise 'nom'
    $sql_update = "UPDATE produits SET nom = ?, prix = ?, description = ?, quantite_en_stock = ?, evaluation_moyenne = ?, photo_produit = ? WHERE nom = ?";
    $stmt_update = $connexionDB->prepare($sql_update);

    if ($stmt_update === false) {
        error_log("Erreur de préparation de la requête UPDATE (modifProduit.php): " . $connexionDB->error);
        $_SESSION['message_erreur'] = "Erreur interne lors de la mise à jour des données.";
        header('Location: modificationProduitPage.php?nom=' . urlencode($currentProductName));
        exit();
    }

    // Liaison des paramètres (types: s=string, d=double, i=integer)
    // CHANGEMENT ICI : Le dernier paramètre est $currentProductName (string)
    $stmt_update->bind_param(
        "sdsssis", // nom(s), prix(d), description(s), quantite(i), eval(d), photo(s), nom_original(s)
        $nom, // le nouveau nom
        $prix,
        $description,
        $quantite_en_stock,
        $evaluation_moyenne,
        $newProductImageName,
        $currentProductName // le nom original du produit, utilisé pour la condition WHERE
    );

    if ($stmt_update->execute()) {
        $_SESSION['message_succes'] = "Produit mis à jour avec succès !";
        // Si le nom du produit a été modifié, rediriger vers la nouvelle URL avec le nouveau nom
        if ($nom !== $currentProductName) {
            header('Location: modificationProduitPage.php?nom=' . urlencode($nom));
            exit();
        }
    } else {
        error_log("Erreur d'exécution de la requête UPDATE (modifProduit.php): " . $stmt_update->error);
        $_SESSION['message_erreur'] = "Une erreur est survenue lors de la mise à jour du produit: " . $stmt_update->error;
    }

    $stmt_update->close();
    $connexionDB->close();

    // Rediriger vers la page d'édition avec le même nom pour voir les changements
    // Ou vers le nouveau nom si le nom du produit a été modifié
    header('Location: modificationProduitPage.php?nom=' . urlencode($nom)); // Utilise le nouveau nom après succès
    exit();

} else {
    // Si la page est accédée directement sans soumission de formulaire POST
    $_SESSION['message_erreur'] = "Accès direct au script de traitement non autorisé.";
    header('Location: listAllProductAdmin.php');
    exit();
}
?>
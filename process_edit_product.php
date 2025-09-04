<?php
// --- ZONE PHP : Traitement du formulaire de modification ---

// 1. Démarrage de la session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Vérification des droits d'administrateur (TRÈS IMPORTANT POUR LA SÉCURITÉ)
if (!isset($_SESSION['LOGIN']) || $_SESSION['LOGIN'] !== true || !isset($_SESSION['ADMIN']) || $_SESSION['ADMIN'] !== true) {
    header('Location: loginPage.php'); // Rediriger si non autorisé
    exit();
}

// 3. Inclusion du fichier de connexion à la base de données
require_once 'connect.php';

// 4. Vérifier si le formulaire a été soumis via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et sécuriser les données du formulaire
    $productId = $_POST['product_id'] ?? null;
    $nom = $_POST['nom'] ?? null;
    $prix = $_POST['prix'] ?? null;
    $description = $_POST['description'] ?? null;
    $quantite_en_stock = $_POST['quantite_en_stock'] ?? null;
    $evaluation_moyenne = $_POST['evaluation_moyenne'] ?? null;
    $oldProductImageName = $_POST['old_product_image'] ?? ''; // Nom de l'image précédente (de la DB)

    // Chemin de base des images (DOIT CORRESPONDRE À CELUI DE VOS AUTRES SCRIPTS)
    $uploadDirectory = 'ressources et consignes/img/';
    $newProductImageName = $oldProductImageName; // Par défaut, on garde l'ancien nom d'image

    // 5. Gérer l'upload de la nouvelle image si elle a été fournie
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['product_image']['tmp_name'];
        $fileName = $_FILES['product_image']['name'];
        $fileSize = $_FILES['product_image']['size'];
        $fileType = $_FILES['product_image']['type'];

        // Extension du fichier (ex: .jpg, .png)
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Générer un nom de fichier unique pour éviter les collisions et les problèmes de cache
        // On utilise un hachage + timestamp pour un nom très unique
        $newProductImageName = md5(time() . $fileName) . '.' . $fileExtension;
        $destPath = $uploadDirectory . $newProductImageName;

        // Déplacer le fichier téléchargé
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // Suppression de l'ancienne image si une nouvelle a été téléchargée AVEC SUCCÈS
            if (!empty($oldProductImageName) && file_exists($uploadDirectory . $oldProductImageName) && $oldProductImageName !== $newProductImageName) {
                unlink($uploadDirectory . $oldProductImageName);
                error_log("Ancienne image supprimée: " . $uploadDirectory . $oldProductImageName);
            }
        } else {
            // Gérer l'erreur d'upload
            error_log("Erreur lors du déplacement du fichier téléchargé : " . $fileName);
            // Vous pouvez ajouter un message d'erreur à l'utilisateur ici
            $_SESSION['message_erreur'] = "Une erreur est survenue lors de l'upload de l'image.";
            header('Location: infoProduitAdmin.php?nom=' . urlencode($nom)); // Rediriger avec l'erreur
            exit();
        }
    }

    // 6. Mettre à jour les données dans la base de données
    $sql_update = "UPDATE produits SET nom = ?, prix = ?, description = ?, quantite_en_stock = ?, evaluation_moyenne = ?, photo_produit = ? WHERE id_produit = ?";
    $stmt_update = $connexionDB->prepare($sql_update);

    if ($stmt_update === false) {
        error_log("Erreur de préparation de la requête UPDATE: " . $connexionDB->error);
        $_SESSION['message_erreur'] = "Erreur interne lors de la mise à jour des données.";
        header('Location: infoProduitAdmin.php?nom=' . urlencode($nom));
        exit();
    }

    // Liaison des paramètres (types: s=string, d=double, i=integer)
    $stmt_update->bind_param(
        "sdssdis", // nom(s), prix(d), description(s), quantite(i), eval(d), photo(s), id(i)
        $nom,
        $prix,
        $description,
        $quantite_en_stock,
        $evaluation_moyenne,
        $newProductImageName, // Utilise le nouveau nom de fichier (ou l'ancien si pas d'upload)
        $productId
    );

    if ($stmt_update->execute()) {
        $_SESSION['message_succes'] = "Produit mis à jour avec succès !";
    } else {
        error_log("Erreur d'exécution de la requête UPDATE: " . $stmt_update->error);
        $_SESSION['message_erreur'] = "Une erreur est survenue lors de la mise à jour du produit.";
    }

    $stmt_update->close();
    $connexionDB->close(); // Fermer la connexion DB après utilisation

    // Rediriger vers la page d'administration du produit avec le nouveau nom (si le nom a changé)
    // Ou l'ID si vous préférez rester sur l'ID pour la redirection
    header('Location: infoProduitAdmin.php?nom=' . urlencode($nom));
    exit();

} else {
    // Si la page est accédée directement sans soumission de formulaire POST
    header('Location: index.php'); // Rediriger vers l'accueil ou une page appropriée
    exit();
}
?>
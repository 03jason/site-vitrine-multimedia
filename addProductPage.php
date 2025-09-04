<?php
// --- ZONE PHP : Connexion DB et Traitement du formulaire ---

// 1. Démarrage de la session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Vérification des droits d'administration
// Seuls les administrateurs doivent pouvoir accéder à cette page
if (!isset($_SESSION['LOGIN']) || $_SESSION['LOGIN'] !== true || !isset($_SESSION['ADMIN']) || $_SESSION['ADMIN'] !== true) {
    $_SESSION['message_erreur'] = "Accès non autorisé. Vous devez être administrateur pour ajouter un produit.";
    header('Location: index.php'); // Rediriger vers la page d'accueil ou de connexion
    exit();
}

// 3. Inclusion du fichier de connexion à la base de données
require_once 'connect.php';

$message = ''; // Variable pour stocker les messages de succès ou d'erreur

// 4. Traitement du formulaire lorsque la méthode est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et validation des données du formulaire
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix = floatval(str_replace(',', '.', trim($_POST['prix'] ?? '0'))); // Convertir ',' en '.' pour float
    $quantite_en_stock = intval(trim($_POST['quantite_en_stock'] ?? '0'));
    $categorie = trim($_POST['categorie'] ?? '');
    $marque = trim($_POST['marque'] ?? '');
    $evaluation_moyenne = floatval(str_replace(',', '.', trim($_POST['evaluation_moyenne'] ?? '0')));
    $statut = trim($_POST['statut'] ?? 'disponible'); // Valeur par défaut

    // Traitement de l'upload de l'image
    $photo_produit = null; // Par défaut, pas d'image
    if (isset($_FILES['photo_produit']) && $_FILES['photo_produit']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['photo_produit']['tmp_name'];
        $fileName = basename($_FILES['photo_produit']['name']); // Nom original du fichier
        $fileSize = $_FILES['photo_produit']['size'];
        $fileType = $_FILES['photo_produit']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Définir le répertoire de destination
            $uploadFileDir = './ressources et consignes/img/';
            $dest_path = $uploadFileDir . $fileName;

            // Vérifier si le fichier existe déjà, ajouter un suffixe si oui
            $i = 0;
            while (file_exists($dest_path)) {
                $i++;
                $fileNameWithoutExt = pathinfo($fileName, PATHINFO_FILENAME);
                $dest_path = $uploadFileDir . $fileNameWithoutExt . '_' . $i . '.' . $fileExtension;
                $fileName = $fileNameWithoutExt . '_' . $i . '.' . $fileExtension; // Met à jour le nom pour la DB
            }

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $photo_produit = $fileName; // Enregistrer le nom du fichier pour la DB
            } else {
                $message = '<p class="message error">Erreur lors du téléchargement de l\'image.</p>';
            }
        } else {
            $message = '<p class="message error">Type de fichier image non autorisé. Seuls JPG, JPEG, PNG, GIF sont permis.</p>';
        }
    }

    // Validation des champs (exemples simples)
    if (empty($nom)) {
        $message = '<p class="message error">Le nom du produit est obligatoire.</p>';
    } elseif ($prix <= 0) {
        $message = '<p class="message error">Le prix doit être un nombre positif.</p>';
    } elseif ($quantite_en_stock < 0) {
        $message = '<p class="message error">La quantité en stock ne peut pas être négative.</p>';
    } else {
        // Vérifier si un produit avec le même nom existe déjà
        $sql_check = "SELECT COUNT(*) FROM produits WHERE nom = ?";
        $stmt_check = $connexionDB->prepare($sql_check);
        $stmt_check->bind_param("s", $nom);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_row();
        if ($row_check[0] > 0) {
            $message = '<p class="message error">Un produit avec ce nom existe déjà. Veuillez choisir un nom unique.</p>';
        } else {
            // Préparation de la requête SQL d'insertion
            // 'date_ajout' sera automatiquement générée par la base de données (si configuré comme TIMESTAMP DEFAULT CURRENT_TIMESTAMP)
            // Sinon, nous devrons l'insérer via NOW() ou date('Y-m-d H:i:s')
            $sql_insert = "INSERT INTO produits (nom, description, prix, quantite_en_stock, categorie, marque, evaluation_moyenne, photo_produit, statut, date_ajout) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt_insert = $connexionDB->prepare($sql_insert);

            if ($stmt_insert === false) {
                error_log("Erreur de préparation de la requête SQL d'insertion dans ajoutProduitPage.php: " . $connexionDB->error);
                $message = '<p class="message error">Une erreur interne est survenue lors de l\'ajout du produit.</p>';
            } else {
                // Liaison des paramètres (s=string, d=double, i=integer)
                $stmt_insert->bind_param(
                    "ssdisdsss",
                    $nom,
                    $description,
                    $prix,
                    $quantite_en_stock,
                    $categorie,
                    $marque,
                    $evaluation_moyenne,
                    $photo_produit,
                    $statut
                );

                // Exécution de la requête
                if ($stmt_insert->execute()) {
                    $message = '<p class="message success">Produit "' . htmlspecialchars($nom) . '" ajouté avec succès !</p>';
                    // Optionnel : Réinitialiser les champs du formulaire après succès
                    $_POST = array(); // Vide les données POST
                } else {
                    error_log("Erreur d'exécution de la requête SQL d'insertion dans ajoutProduitPage.php: " . $stmt_insert->error);
                    $message = '<p class="message error">Erreur lors de l\'ajout du produit : ' . htmlspecialchars($stmt_insert->error) . '</p>';
                }
                $stmt_insert->close();
            }
        }
        $stmt_check->close();
    }
}

// 5. Fermeture de la connexion à la base de données (si elle est ouverte)
// Il est préférable de la fermer ici si vous n'avez plus besoin d'opérations DB
if (isset($connexionDB)) {
    $connexionDB->close();
}

// Inclusion de la barre de navigation
include 'navBar.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        <form action="ajoutProduitPage.php" method="POST" enctype="multipart/form-data" class="add-product-form">
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
                <label for="evaluation_moyenne">Évaluation moyenne (0-5)</label>
                <input type="number" id="evaluation_moyenne" name="evaluation_moyenne" step="0.1" min="0" max="5" value="<?= htmlspecialchars($_POST['evaluation_moyenne'] ?? '0.0') ?>">
            </div>

            <div class="form-group">
                <label for="photo_produit">Photo du produit</label>
                <input type="file" id="photo_produit" name="photo_produit" accept="image/*">
                <small>Formats acceptés : JPG, JPEG, PNG, GIF</small>
            </div>

            <div class="form-group">
                <label for="statut">Statut</label>
                <select id="statut" name="statut">
                    <option value="disponible" <?= (($_POST['statut'] ?? '') == 'disponible') ? 'selected' : '' ?>>Disponible</option>
                    <option value="indisponible" <?= (($_POST['statut'] ?? '') == 'indisponible') ? 'selected' : '' ?>>Indisponible</option>
                    <option value="bientot_disponible" <?= (($_POST['statut'] ?? '') == 'bientot_disponible') ? 'selected' : '' ?>>Bientôt disponible</option>
                </select>
            </div>

            <button type="submit" class="btn primary-button submit-button"><i class="fas fa-plus-circle"></i> Ajouter le produit</button>
        </form>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
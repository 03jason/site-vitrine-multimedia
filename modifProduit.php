<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require __DIR__ . '/connect.php';

if (empty($_SESSION['LOGIN'])) {
    http_response_code(403);
    exit('Non connecté');
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); exit('ID manquant'); }

// Récupérer le produit
$stmt = $connexionDB->prepare("
  SELECT id, nom, description, prix, quantite_en_stock, categorie, marque,
         date_ajout, evaluation_moyenne, statut, photo_produit
  FROM produits WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$p = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$p) { http_response_code(404); exit('Produit introuvable'); }

$isGerant = !empty($_SESSION['ADMIN']) && $_SESSION['ADMIN'] === true;
?>
<?php include 'navBar.php'; ?>
<h1>Modifier : <?= htmlspecialchars($p['nom'], ENT_QUOTES) ?></h1>

<form method="post" action="process_edit_product.php" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">

    <!-- Toujours modifiable -->
    <label>Description<br>
        <textarea name="description" required><?= htmlspecialchars($p['description'], ENT_QUOTES) ?></textarea>
    </label><br>

    <?php if ($isGerant): ?>
        <label>Nom<br>
            <input name="nom" required value="<?= htmlspecialchars($p['nom'], ENT_QUOTES) ?>">
        </label><br>

        <label>Prix<br>
            <input type="number" step="0.01" name="prix" required value="<?= htmlspecialchars($p['prix'], ENT_QUOTES) ?>">
        </label><br>

        <label>Quantité en stock<br>
            <input type="number" step="1" name="quantite_en_stock" required value="<?= (int)$p['quantite_en_stock'] ?>">
        </label><br>

        <label>Catégorie<br>
            <input name="categorie" value="<?= htmlspecialchars($p['categorie'], ENT_QUOTES) ?>">
        </label><br>

        <label>Marque<br>
            <input name="marque" value="<?= htmlspecialchars($p['marque'], ENT_QUOTES) ?>">
        </label><br>

        <label>Statut<br>
            <select name="statut" required>
                <option value="disponible" <?= $p['statut']==='disponible'?'selected':'' ?>>disponible</option>
                <option value="en rupture" <?= $p['statut']==='en rupture'?'selected':'' ?>>en rupture</option>
            </select>
        </label><br>

        <label>Image actuelle</label><br>
        <?php if (!empty($p['photo_produit'])): ?>
            <img src="<?= htmlspecialchars($p['photo_produit'], ENT_QUOTES) ?>" alt="" style="max-width:160px"><br>
        <?php endif; ?>

        <label>Photo (chemin direct)
            <input name="image_path" placeholder="ressources et consignes/img/xxx.jpg" value="<?= htmlspecialchars($p['photo_produit'], ENT_QUOTES) ?>">
        </label><br>

        <div>— OU upload —</div>
        <input type="file" name="image_file" accept="image/*"><br>
    <?php endif; ?>

    <button type="submit">Enregistrer</button>
</form>

<p><a href="infoProduitAdmin.php?id=<?= (int)$p['id'] ?>">Annuler</a></p>
<?php include 'footer.php'; ?>

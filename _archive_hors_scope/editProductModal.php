<div id="editProductModal" class="modal">
    <div class="modal-content">
        <span class="close-button" aria-label="Fermer la fenêtre modale">&times;</span>
        <h2 class="modal-title">Modifier le produit : <span id="modalProductName"><?= htmlspecialchars($nom ?? '') ?></span></h2>

        <form id="editProductForm" action="../process_edit_product.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="modalProductId" value="<?= htmlspecialchars($productId ?? '') ?>">
            <input type="hidden" name="old_product_image" id="modalOldProductImage" value="<?= htmlspecialchars($photo_produit_db ?? '') ?>">

            <div class="form-group">
                <label for="edit_nom" class="form-label">Nom du produit:</label>
                <input type="text" id="edit_nom" name="nom" class="form-input" value="<?= htmlspecialchars($nom ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="edit_prix" class="form-label">Prix:</label>
                <input type="number" id="edit_prix" name="prix" step="0.01" class="form-input" value="<?= htmlspecialchars($productInfo['prix'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="edit_description" class="form-label">Description:</label>
                <textarea id="edit_description" name="description" rows="5" class="form-textarea" required><?= htmlspecialchars($description ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="edit_quantite" class="form-label">Quantité en stock:</label>
                <input type="number" id="edit_quantite" name="quantite_en_stock" class="form-input" value="<?= htmlspecialchars($productInfo['quantite_en_stock'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="edit_evaluation" class="form-label">Évaluation moyenne:</label>
                <input type="number" id="edit_evaluation" name="evaluation_moyenne" step="0.1" min="0" max="5" class="form-input" value="<?= htmlspecialchars($productInfo['evaluation_moyenne'] ?? '') ?>">
            </div>

            <div class="form-group image-upload-group">
                <label class="form-label">Image du produit actuelle:</label>
                <div class="current-image-preview">
                    <img id="currentProductImage" src="<?= htmlspecialchars($imageProduct ?? '') ?>" alt="Image actuelle du produit">
                </div>
                <label for="edit_image" class="form-label file-input-label">Changer l'image:</label>
                <input type="file" id="edit_image" name="product_image" accept="image/*" class="file-input">
            </div>

            <button type="submit" class="submit-button primary-button">Sauvegarder les modifications</button>
        </form>
    </div>
</div>
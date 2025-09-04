<?php

$nomProduit = htmlspecialchars($nomProduit ?? 'Nom par défaut');
$prixProduit = htmlspecialchars($prixProduit ?? '00.00 €');
$quantiteProduit = htmlspecialchars($quantiteProduit ?? '0');
$categorieProduit = htmlspecialchars($categorieProduit ?? 'catégorie');
$marqueProduit = htmlspecialchars($marqueProduit ?? 'marque');
$dateAjoutProduit = htmlspecialchars($dateAjoutProduit ?? 'date ajout');
$evaluationProduit = htmlspecialchars($evaluationProduit ?? '5 / 5');
$statutProduit = htmlspecialchars($statutProduit ?? 'disponible');

?>


<div class="card" style="width: 18rem;">
    <div class="card-body">
        <h5 class="card-title"> <?php echo"$nomProduit" ?> </h5>
        <p class="card-text"> <?php echo "$prixProduit" ?> €</p>
        <p class="card-text"> <?php echo"$quantiteProduit" ?> en stock </p>
        <p class="card-text"> <?php echo"$categorieProduit" ?> </p>
        <p class="card-text"> <?php echo"$marqueProduit" ?> </p>
        <p class="card-text">date d'ajout :  <?php echo"$dateAjoutProduit" ?> </p>
        <p class="card-text"> <?php echo"$evaluationProduit" ?> / 5 </p>
        <p class="card-text"> <?php echo"$statutProduit" ?> </p>
        <a href="#" class="btn btn-primary">Détails</a>
    </div>
</div>


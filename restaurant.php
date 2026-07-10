<?php
session_start();
/**
 * restaurant.php
 * ------------------------------------------------------------
 * Affiche le menu du restaurant et gère le panier du client
 * directement dans la session ($_SESSION['panier']).
 * ------------------------------------------------------------
 */
$pageActive = 'restaurant';
require_once __DIR__ . '/includes/entete.php';

if (!estConnecte()) {
    rediriger('connexion.php');
}

$titrePage = 'Restaurant';

// Initialisation du panier en session s'il n'existe pas encore
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = []; // format : [plat_id => quantite]
}

// ---------- Traitement des actions sur le panier ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $platId = (int) ($_POST['plat_id'] ?? 0);

    if ($_POST['action'] === 'ajouter' && $platId) {
        $_SESSION['panier'][$platId] = ($_SESSION['panier'][$platId] ?? 0) + 1;
    } elseif ($_POST['action'] === 'retirer' && $platId) {
        if (!empty($_SESSION['panier'][$platId])) {
            $_SESSION['panier'][$platId]--;
            if ($_SESSION['panier'][$platId] <= 0) unset($_SESSION['panier'][$platId]);
        }
    } elseif ($_POST['action'] === 'vider') {
        $_SESSION['panier'] = [];
    }
    rediriger('restaurant.php');
}

// Récupération des plats
$plats = $pdo->query("SELECT * FROM plats WHERE disponible = 1 ORDER BY categorie, nom")->fetchAll();

// Calcul du total du panier
$totalPanier = 0;
$nbArticles  = 0;
if (!empty($_SESSION['panier'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['panier'])));
    $platsPanier = $pdo->query("SELECT * FROM plats WHERE id IN ($ids)")->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
    foreach ($_SESSION['panier'] as $id => $qte) {
        if (isset($platsPanier[$id])) {
            $totalPanier += $platsPanier[$id]['prix'] * $qte;
            $nbArticles  += $qte;
        }
    }
}
?>

<div class="container py-4" style="max-width:560px;">

  <a href="accueil.php" class="text-decoration-none text-dark"><i class="bi bi-arrow-left fs-4"></i></a>
  <h1 class="titre-luxe d-inline-block ms-2 align-middle">Restaurant</h1>

  <!-- Filtres par catégorie -->
  <div class="d-flex gap-3 my-4 overflow-auto">
    <button class="btn btn-luxe rounded-circle filtre-categorie actif" data-categorie="tout" style="width:60px;height:60px;"><i class="bi bi-egg-fried"></i></button>
    <div class="text-center small">
      <button class="btn btn-outline-secondary rounded-circle filtre-categorie" data-categorie="Petit-déjeuner" style="width:60px;height:60px;"><i class="bi bi-cup-hot"></i></button>
      <div>Petit-déj.</div>
    </div>
    <div class="text-center small">
      <button class="btn btn-outline-secondary rounded-circle filtre-categorie" data-categorie="Déjeuner" style="width:60px;height:60px;"><i class="bi bi-bowl"></i></button>
      <div>Déjeuner</div>
    </div>
    <div class="text-center small">
      <button class="btn btn-outline-secondary rounded-circle filtre-categorie" data-categorie="Dîner" style="width:60px;height:60px;"><i class="bi bi-egg-fried"></i></button>
      <div>Dîner</div>
    </div>
       <div class="text-center small">
      <button class="btn btn-outline-secondary rounded-circle filtre-categorie" data-categorie="Jus" style="width:60px;height:60px;"><i class="bi bi-egg-fried"></i></button>
      <div>Jus</div>
    </div>
  </div>

  <h5 class="titre-luxe mb-3">Nos plats populaires</h5>

  <?php foreach ($plats as $plat): ?>
    <div class="carte-luxe d-flex align-items-center p-2 mb-3 plat-item" data-categorie="<?= nettoyer($plat['categorie']) ?>">
      <img src="<?= $plat['image'] ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=200&q=80' ?>"
           class="rounded-3 me-3" style="width:80px;height:80px;object-fit:cover;" alt="<?= nettoyer($plat['nom']) ?>">
      <div class="flex-grow-1">
        <strong><?= nettoyer($plat['nom']) ?></strong>
        <div class="fw-bold mt-1"><?= formaterMontant($plat['prix']) ?></div>
      </div>
      <form method="POST">
        <input type="hidden" name="plat_id" value="<?= $plat['id'] ?>">
        <input type="hidden" name="action" value="ajouter">
        <button type="submit" class="btn btn-or rounded-circle" style="width:38px;height:38px;"><i class="bi bi-plus-lg"></i></button>
      </form>
    </div>
  <?php endforeach; ?>

  <?php if ($nbArticles > 0): ?>
    <a href="paiement.php?type=commande" class="btn btn-luxe w-100 py-3 d-flex justify-content-between align-items-center px-4 position-fixed start-50 translate-middle-x" style="bottom:80px;max-width:520px;width:90%;">
      <span>Panier (<?= $nbArticles ?>)</span>
      <span><?= formaterMontant($totalPanier) ?></span>
    </a>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/pied.php'; ?>

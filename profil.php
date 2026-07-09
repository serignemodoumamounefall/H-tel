<?php
/**
 * profil.php
 * ------------------------------------------------------------
 * Page profil du client : informations, réservations, commandes,
 * paramètres, déconnexion.
 * ------------------------------------------------------------
 */
$pageActive = 'profil';
require_once __DIR__ . '/includes/entete.php';

if (!estConnecte()) {
    rediriger('connexion.php');
}

$titrePage = 'Profil';

// Récupération de quelques statistiques du client
$nbReservations = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE utilisateur_id = :uid");
$nbReservations->execute(['uid' => $_SESSION['utilisateur_id']]);
$totalReservations = $nbReservations->fetchColumn();

$nbCommandes = $pdo->prepare("SELECT COUNT(*) FROM commandes WHERE utilisateur_id = :uid");
$nbCommandes->execute(['uid' => $_SESSION['utilisateur_id']]);
$totalCommandes = $nbCommandes->fetchColumn();
?>

<div class="container py-4" style="max-width:560px;">

  <?php if (isset($_GET['paiement']) && $_GET['paiement'] === 'ok'): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle"></i> Paiement effectué avec succès !</div>
  <?php endif; ?>

  <div class="text-center mb-4">
    <div class="rounded-circle bg-marine mx-auto d-flex align-items-center justify-content-center"
         style="width:90px;height:90px;background:#1B263B;">
      <i class="bi bi-person-fill text-white" style="font-size:2.5rem;"></i>
    </div>
    <h4 class="titre-luxe mt-3 mb-0"><?= nettoyer($_SESSION['nom_complet']) ?></h4>
    <p class="text-muted"><?= nettoyer($_SESSION['email']) ?></p>
  </div>

  <div class="row text-center mb-4 g-2">
    <div class="col-6">
      <div class="carte-luxe p-3">
        <div class="fs-4 fw-bold"><?= $totalReservations ?></div>
        <div class="small text-muted">Réservations</div>
      </div>
    </div>
    <div class="col-6">
      <div class="carte-luxe p-3">
        <div class="fs-4 fw-bold"><?= $totalCommandes ?></div>
        <div class="small text-muted">Commandes</div>
      </div>
    </div>
  </div>

  <div class="list-group">
    <a href="reservation.php" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3">
      <i class="bi bi-calendar-check fs-5"></i> Mes réservations
    </a>
    <a href="restaurant.php" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3">
      <i class="bi bi-bag-check fs-5"></i> Mes commandes
    </a>
    <a href="en_developpement.php" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3">
      <i class="bi bi-chat-left-text fs-5"></i> Mes demandes
    </a>
    <a href="en_developpement.php" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3">
      <i class="bi bi-bell fs-5"></i> Notifications
    </a>
    <a href="en_developpement.php" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3">
      <i class="bi bi-gear fs-5"></i> Paramètres
    </a>
    <a href="en_developpement.php" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3">
      <i class="bi bi-life-preserver fs-5"></i> Aide et support
    </a>
    <a href="deconnexion.php" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 text-danger">
      <i class="bi bi-box-arrow-right fs-5"></i> Déconnexion
    </a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/pied.php'; ?>

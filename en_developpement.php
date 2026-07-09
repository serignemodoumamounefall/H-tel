<?php
/**
 * en_developpement.php
 * ------------------------------------------------------------
 * Page générique affichée pour toute fonctionnalité pas encore
 * disponible. Le paramètre GET "fonction" permet de personnaliser
 * le message (ex : en_developpement.php?fonction=Blanchisserie).
 * ------------------------------------------------------------
 */
$pageActive = '';
require_once __DIR__ . '/includes/entete.php';

$titrePage = 'Page en développement';
$nomFonction = nettoyer($_GET['fonction'] ?? '');

// Lien de retour : on revient à la page précédente si possible, sinon à l'accueil
$lienRetour = $_SERVER['HTTP_REFERER'] ?? 'accueil.php';
?>

<div class="container py-5 text-center" style="max-width:520px;">

  <div class="mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle"
       style="width:120px;height:120px;background:#1B263B;">
    <i class="bi bi-cone-striped text-warning" style="font-size:3rem;"></i>
  </div>

  <h1 class="titre-luxe mb-2">Page en cours de développement</h1>

  <?php if ($nomFonction): ?>
    <p class="text-secondary mb-1">La fonctionnalité <strong><?= $nomFonction ?></strong> n'est pas encore disponible.</p>
  <?php else: ?>
    <p class="text-secondary mb-1">Cette fonctionnalité n'est pas encore disponible.</p>
  <?php endif; ?>

  <p class="text-muted mb-4">Notre équipe y travaille activement. Merci de votre patience, revenez bientôt !</p>

  <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
    <a href="<?= nettoyer($lienRetour) ?>" class="btn btn-luxe px-4"><i class="bi bi-arrow-left"></i> Retour</a>
    <a href="accueil.php" class="btn btn-outline-secondary px-4"><i class="bi bi-house-door"></i> Accueil</a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/pied.php'; ?>

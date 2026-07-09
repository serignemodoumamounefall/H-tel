<?php
/**
 * includes/entete.php
 * ------------------------------------------------------------
 * En-tête commun à toutes les pages "client" du site.
 * La gestion de session reste ici, côté inclusion HTML,
 * comme demandé : chaque page qui l'inclut démarre sa session.
 * ------------------------------------------------------------
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/fonctions.php';

// Nom de la page active (utilisé pour surligner le menu en cours)
$pageActive = $pageActive ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($titrePage) ? nettoyer($titrePage) . ' - Hôtel Luxury & Comfort' : 'Hôtel Luxury & Comfort' ?></title>

<!-- Bootstrap 5 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<!-- Icônes Bootstrap -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
<!-- Police Playfair (style "luxe") -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<!-- Styles personnalisés du site -->
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php if (estConnecte()): ?>
<!-- ================= NAVBAR SUPÉRIEURE (desktop / tablette) ================= -->
<nav class="navbar navbar-expand-md navbar-luxury sticky-top">
  <div class="container">
    <a class="navbar-brand" href="accueil.php">
      <span class="logo-cercle"><i class="bi bi-h-circle-fill"></i></span> HOTEL <small>Luxury &amp; Comfort</small>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menuPrincipal">
      <ul class="navbar-nav ms-auto align-items-md-center gap-2">
        <li class="nav-item"><a class="nav-link <?= $pageActive==='accueil'?'active':'' ?>" href="accueil.php"><i class="bi bi-house-door"></i> Accueil</a></li>
        <li class="nav-item"><a class="nav-link <?= $pageActive==='reservation'?'active':'' ?>" href="reservation.php"><i class="bi bi-calendar-check"></i> Réservations</a></li>
        <li class="nav-item"><a class="nav-link <?= $pageActive==='restaurant'?'active':'' ?>" href="restaurant.php"><i class="bi bi-egg-fried"></i> Services</a></li>
        <li class="nav-item"><a class="nav-link <?= $pageActive==='profil'?'active':'' ?>" href="profil.php"><i class="bi bi-person-circle"></i> Profil</a></li>
        <li class="nav-item"><a class="nav-link text-danger" href="deconnexion.php"><i class="bi bi-box-arrow-right"></i> Déconnexion</a></li>
      </ul>
    </div>
  </div>
</nav>
<?php endif; ?>

<main class="contenu-principal <?= estConnecte() ? 'pb-5 pb-md-4' : '' ?>">

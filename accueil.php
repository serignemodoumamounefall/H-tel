<?php
/**
 * accueil.php
 * ------------------------------------------------------------
 * Tableau d'accueil du client : bannière, accès rapide aux
 * services, recherche.
 * ------------------------------------------------------------
 */
$pageActive = 'accueil';
require_once __DIR__ . '/includes/entete.php';

// Sécurité : page réservée aux utilisateurs connectés
if (!estConnecte()) {
    rediriger('connexion.php');
}
if (estAdmin()) {
    rediriger('admin/tableau_de_bord.php');
}

$titrePage = 'Accueil';
$prenom = explode(' ', $_SESSION['nom_complet'])[0];
?>

<div class="container py-4">

  <h1 class="titre-luxe mb-3">Bonjour, <?= nettoyer($prenom) ?></h1>

  <!-- Barre de recherche -->
  <form class="mb-4" action="restaurant.php" method="GET">
    <div class="input-group">
      <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
      <input type="text" name="recherche" class="form-control border-start-0" placeholder="Rechercher un service">
    </div>
  </form>

  <!-- Bannière -->
  <div class="banniere-accueil mb-4" style="background-image:url('https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1200&q=80');">
    <div class="contenu-banniere">
      <h2 class="text-white">Profitez d'un séjour<br>inoubliable</h2>
      <a href="reservation.php" class="btn btn-light rounded-pill fw-bold px-4 mt-2">Découvrir</a>
    </div>
  </div>

  <!-- Nos services -->
  <h3 class="titre-luxe mb-3">Nos Services</h3>
  <div class="row g-3">
    <div class="col-4 col-md-2">
      <a href="reservation.php" class="text-decoration-none">
        <div class="carte-luxe carte-service">
          <i class="bi bi-door-closed"></i>
          <p class="small fw-semibold mt-2 mb-0 text-dark">Chambres et suites</p>
        </div>
      </a>
    </div>
    <div class="col-4 col-md-2">
      <a href="restaurant.php" class="text-decoration-none">
        <div class="carte-luxe carte-service">
          <i class="bi bi-cup-hot"></i>
          <p class="small fw-semibold mt-2 mb-0 text-dark">Restaurant</p>
        </div>
      </a>
    </div>
    <div class="col-4 col-md-2">
      <a href="en_developpement.php" class="text-decoration-none">
        <div class="carte-luxe carte-service">
          <i class="bi bi-bell"></i>
          <p class="small fw-semibold mt-2 mb-0 text-dark">Services de chambre</p>
        </div>
      </a>
    </div>
    <div class="col-4 col-md-2">
      <a href="en_developpement.php" class="text-decoration-none">
        <div class="carte-luxe carte-service">
          <i class="bi bi-water"></i>
          <p class="small fw-semibold mt-2 mb-0 text-dark">Blanchisserie</p>
        </div>
      </a>
    </div>
    <div class="col-4 col-md-2">
      <a href="en_developpement.php" class="text-decoration-none">
        <div class="carte-luxe carte-service">
          <i class="bi bi-car-front"></i>
          <p class="small fw-semibold mt-2 mb-0 text-dark">Transport</p>
        </div>
      </a>
    </div>
    <div class="col-4 col-md-2">
      <a href="profil.php" class="text-decoration-none">
        <div class="carte-luxe carte-service">
          <i class="bi bi-grid-3x3-gap"></i>
          <p class="small fw-semibold mt-2 mb-0 text-dark">Plus</p>
        </div>
      </a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/pied.php'; ?>

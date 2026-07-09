<?php
/**
 * includes/pied.php
 * ------------------------------------------------------------
 * Pied de page commun : ferme le <main>, ajoute la barre de
 * navigation basse (mobile uniquement) et les scripts JS.
 * ------------------------------------------------------------
 */
?>
</main>

<?php if (estConnecte()): ?>
<!-- ================= NAVBAR BASSE (mobile uniquement, façon application) ================= -->
<nav class="navbar-bas d-md-none">
  <a href="accueil.php" class="lien-bas <?= $pageActive==='accueil'?'actif':'' ?>">
    <i class="bi bi-house-door-fill"></i><span>Accueil</span>
  </a>
  <a href="reservation.php" class="lien-bas <?= $pageActive==='reservation'?'actif':'' ?>">
    <i class="bi bi-calendar-check-fill"></i><span>Réservations</span>
  </a>
  <a href="restaurant.php" class="lien-bas <?= $pageActive==='restaurant'?'actif':'' ?>">
    <i class="bi bi-egg-fried"></i><span>Services</span>
  </a>
  <a href="profil.php" class="lien-bas <?= $pageActive==='profil'?'actif':'' ?>">
    <i class="bi bi-person-circle"></i><span>Profil</span>
  </a>
</nav>
<?php endif; ?>

<!-- Bootstrap JS (bundle avec Popper) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>

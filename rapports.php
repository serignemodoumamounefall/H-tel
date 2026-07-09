<?php
/**
 * admin/rapports.php
 * ------------------------------------------------------------
 * Rapports synthétiques : revenus par mois, occupation, etc.
 * ------------------------------------------------------------
 */
$pageActive = 'rapports';
require_once __DIR__ . '/includes/entete_admin.php';
$titrePage = 'Rapports';

$revenusParMois = $pdo->query("
    SELECT DATE_FORMAT(date_paiement, '%Y-%m') AS mois, SUM(montant) AS total
    FROM paiements WHERE statut='Payé'
    GROUP BY mois ORDER BY mois DESC LIMIT 12
")->fetchAll();

$repartitionType = $pdo->query("
    SELECT c.type, COUNT(r.id) AS total
    FROM reservations r JOIN chambres c ON c.id = r.chambre_id
    GROUP BY c.type
")->fetchAll();
?>

<h1 class="titre-luxe mb-4">Rapports</h1>

<div class="row g-3">
  <div class="col-12 col-lg-7">
    <div class="carte-stat">
      <h6 class="fw-bold mb-3">Revenus par mois</h6>
      <canvas id="graphRevenus" height="150"></canvas>
    </div>
  </div>
  <div class="col-12 col-lg-5">
    <div class="carte-stat">
      <h6 class="fw-bold mb-3">Réservations par type de chambre</h6>
      <canvas id="graphTypes" height="150"></canvas>
    </div>
  </div>
</div>

<script>
new Chart(document.getElementById('graphRevenus'), {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_reverse(array_column($revenusParMois, 'mois'))) ?>,
    datasets: [{
      label: 'Revenus (€)',
      data: <?= json_encode(array_reverse(array_column($revenusParMois, 'total'))) ?>,
      backgroundColor: '#1B263B'
    }]
  }
});

new Chart(document.getElementById('graphTypes'), {
  type: 'pie',
  data: {
    labels: <?= json_encode(array_column($repartitionType, 'type')) ?>,
    datasets: [{
      data: <?= json_encode(array_column($repartitionType, 'total')) ?>,
      backgroundColor: ['#FFC107', '#1B263B', '#0D182A']
    }]
  }
});
</script>

<?php require_once __DIR__ . '/includes/pied_admin.php'; ?>

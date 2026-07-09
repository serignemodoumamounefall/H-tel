<?php
/**
 * admin/tableau_de_bord.php
 * ------------------------------------------------------------
 * Vue d'ensemble : statistiques, graphique des réservations,
 * répartition des chambres, activités récentes.
 * ------------------------------------------------------------
 */
$pageActive = 'tableau_de_bord';
require_once __DIR__ . '/includes/entete_admin.php';
$titrePage = 'Tableau de bord';

// ---------- Statistiques générales ----------
$totalReservations = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$chambresOccupees  = $pdo->query("SELECT COUNT(*) FROM chambres WHERE statut = 'Occupée'")->fetchColumn();
$totalClients       = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'client'")->fetchColumn();
$revenusTotal       = $pdo->query("SELECT COALESCE(SUM(montant),0) FROM paiements WHERE statut = 'Payé'")->fetchColumn();

// ---------- Réservations des 30 derniers jours (graphique) ----------
$evolution = $pdo->query("
    SELECT DATE(date_creation) AS jour, COUNT(*) AS total
    FROM reservations
    WHERE date_creation >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY DATE(date_creation)
    ORDER BY jour
")->fetchAll();
$labelsGraphique = array_map(fn($l) => date('d/m', strtotime($l['jour'])), $evolution);
$valeursGraphique = array_map(fn($l) => (int) $l['total'], $evolution);

// ---------- Répartition des chambres ----------
$repartition = $pdo->query("SELECT statut, COUNT(*) AS total FROM chambres GROUP BY statut")->fetchAll();

// ---------- Réservations récentes ----------
$recentes = $pdo->query("
    SELECT r.*, u.nom_complet, c.type FROM reservations r
    JOIN utilisateurs u ON u.id = r.utilisateur_id
    JOIN chambres c ON c.id = r.chambre_id
    ORDER BY r.date_creation DESC LIMIT 5
")->fetchAll();

// ---------- Activités récentes (simulées à partir des dernières actions) ----------
$activites = $pdo->query("
    SELECT 'reservation' AS type, code_reservation AS reference, date_creation AS quand FROM reservations
    UNION ALL
    SELECT 'paiement' AS type, CONCAT(montant,' €') AS reference, date_paiement AS quand FROM paiements
    ORDER BY quand DESC LIMIT 5
")->fetchAll();
?>

<h1 class="titre-luxe mb-4">Tableau de bord</h1>

<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="carte-stat"><div class="valeur"><?= $totalReservations ?></div><div class="text-muted">Réservations</div></div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="carte-stat"><div class="valeur"><?= $chambresOccupees ?></div><div class="text-muted">Chambres occupées</div></div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="carte-stat"><div class="valeur"><?= $totalClients ?></div><div class="text-muted">Clients</div></div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="carte-stat"><div class="valeur"><?= formaterMontant($revenusTotal) ?></div><div class="text-muted">Revenus</div></div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-12 col-lg-7">
    <div class="carte-stat">
      <h6 class="fw-bold mb-3">Réservations (30 derniers jours)</h6>
      <canvas id="graphReservations" height="140"></canvas>
    </div>
  </div>
  <div class="col-12 col-lg-5">
    <div class="carte-stat">
      <h6 class="fw-bold mb-3">Répartition des chambres</h6>
      <canvas id="graphRepartition" height="140"></canvas>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-7">
    <div class="carte-stat">
      <h6 class="fw-bold mb-3">Réservations récentes</h6>
      <?php foreach ($recentes as $r): ?>
        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
          <div>
            <i class="bi bi-person-circle me-2"></i>
            <strong><?= nettoyer($r['nom_complet']) ?></strong>
            <div class="small text-muted ms-4"><?= date('d M Y', strtotime($r['date_creation'])) ?> · <?= nettoyer($r['type']) ?></div>
          </div>
          <span class="fw-bold"><?= formaterMontant($r['montant_total']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="col-12 col-lg-5">
    <div class="carte-stat">
      <h6 class="fw-bold mb-3">Activités récentes</h6>
      <?php foreach ($activites as $a): ?>
        <div class="d-flex align-items-center gap-2 border-bottom py-2 small">
          <i class="bi bi-clock"></i>
          <?php if ($a['type'] === 'reservation'): ?>
            Nouvelle réservation <strong><?= nettoyer($a['reference']) ?></strong>
          <?php else: ?>
            Paiement reçu de <strong><?= nettoyer($a['reference']) ?></strong>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
// Graphique d'évolution des réservations
new Chart(document.getElementById('graphReservations'), {
  type: 'line',
  data: {
    labels: <?= json_encode($labelsGraphique) ?>,
    datasets: [{
      label: 'Réservations',
      data: <?= json_encode($valeursGraphique) ?>,
      borderColor: '#1B263B',
      backgroundColor: 'rgba(255,193,7,0.2)',
      tension: 0.35,
      fill: true,
    }]
  },
  options: { plugins: { legend: { display: false } } }
});

// Graphique de répartition des chambres
new Chart(document.getElementById('graphRepartition'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode(array_column($repartition, 'statut')) ?>,
    datasets: [{
      data: <?= json_encode(array_column($repartition, 'total')) ?>,
      backgroundColor: ['#dc3545', '#28a745', '#0d6efd', '#FFC107'],
    }]
  }
});
</script>

<?php require_once __DIR__ . '/includes/pied_admin.php'; ?>

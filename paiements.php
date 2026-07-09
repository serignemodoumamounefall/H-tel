<?php

/**
 * admin/paiements.php
 * ------------------------------------------------------------
 * Historique de tous les paiements (réservations + restaurant).
 * ------------------------------------------------------------
 */
$pageActive = 'paiements';
require_once __DIR__ . '/includes/entete_admin.php';
$titrePage = 'Paiements';

$paiements = $pdo->query("
    SELECT p.*, u.nom_complet,
        CASE WHEN p.reservation_id IS NOT NULL THEN 'Réservation' ELSE 'Restaurant' END AS origine
    FROM paiements p
    JOIN utilisateurs u ON u.id = p.utilisateur_id
    ORDER BY p.date_paiement DESC
")->fetchAll();

$totalEncaisse = $pdo->query("SELECT COALESCE(SUM(montant),0) FROM paiements WHERE statut='Payé'")->fetchColumn();
?>

<h1 class="titre-luxe mb-2">Paiements</h1>
<p class="text-muted mb-4">Total encaissé : <strong><?= formaterMontant($totalEncaisse) ?></strong></p>

<div class="carte-stat p-0">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>N°</th><th>Client</th><th>Origine</th><th>Méthode</th><th>Montant</th><th>Statut</th><th>Date</th></tr>
      </thead>
      <tbody>
        <?php foreach ($paiements as $p): ?>
          <tr>
            <td class="fw-bold">#<?= $p['id'] ?></td>
            <td><?= nettoyer($p['nom_complet']) ?></td>
            <td><?= nettoyer($p['origine']) ?></td>
            <td><?= nettoyer($p['methode']) ?></td>
            <td><?= formaterMontant($p['montant']) ?></td>
            <td>
              <?php $classe = $p['statut']==='Payé' ? 'badge-confirmee' : ($p['statut']==='Échoué' ? 'badge-annulee' : 'badge-attente'); ?>
              <span class="badge rounded-pill <?= $classe ?>"><?= nettoyer($p['statut']) ?></span>
            </td>
            <td><?= date('d/m/Y H:i', strtotime($p['date_paiement'])) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$paiements): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">Aucun paiement enregistré.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/pied_admin.php'; ?>

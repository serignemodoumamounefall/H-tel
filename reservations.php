<?php
/**
 * admin/reservations.php
 * ------------------------------------------------------------
 * Gestion des réservations : changement de statut (confirmer,
 * annuler) effectué directement par l'administrateur.
 * ------------------------------------------------------------
 */
$pageActive = 'reservations';
require_once __DIR__ . '/includes/entete_admin.php';
$titrePage = 'Gestion des réservations';

// ---------- Mise à jour du statut d'une réservation ----------
if (isset($_GET['statut'], $_GET['id'])) {
    $statutsAutorises = ['Confirmée', 'Annulée', 'En attente'];
    if (in_array($_GET['statut'], $statutsAutorises, true)) {
        $stmt = $pdo->prepare("UPDATE reservations SET statut = :s WHERE id = :id");
        $stmt->execute(['s' => $_GET['statut'], 'id' => (int) $_GET['id']]);

        // Si annulée, on libère la chambre
        if ($_GET['statut'] === 'Annulée') {
            $resv = $pdo->prepare("SELECT chambre_id FROM reservations WHERE id = :id");
            $resv->execute(['id' => (int) $_GET['id']]);
            if ($row = $resv->fetch()) {
                $pdo->prepare("UPDATE chambres SET statut = 'Disponible' WHERE id = :cid")->execute(['cid' => $row['chambre_id']]);
            }
        }
    }
    rediriger('reservations.php');
}

$reservations = $pdo->query("
    SELECT r.*, u.nom_complet, c.numero AS num_chambre FROM reservations r
    JOIN utilisateurs u ON u.id = r.utilisateur_id
    JOIN chambres c ON c.id = r.chambre_id
    ORDER BY r.date_creation DESC
")->fetchAll();
?>

<h1 class="titre-luxe mb-4">Gestion des réservations</h1>

<div class="carte-stat p-0">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>N°</th><th>Client</th><th>Chambre</th><th>Arrivée</th><th>Départ</th><th>Montant</th><th>Statut</th><th>Action</th></tr>
      </thead>
      <tbody>
        <?php foreach ($reservations as $r): ?>
          <tr>
            <td class="fw-bold">#<?= $r['id'] ?></td>
            <td><?= nettoyer($r['nom_complet']) ?></td>
            <td><?= nettoyer($r['num_chambre']) ?></td>
            <td><?= date('d/m/Y', strtotime($r['date_arrivee'])) ?></td>
            <td><?= date('d/m/Y', strtotime($r['date_depart'])) ?></td>
            <td><?= formaterMontant($r['montant_total']) ?></td>
            <td>
              <?php $classe = $r['statut']==='Confirmée'?'badge-confirmee':($r['statut']==='Annulée'?'badge-annulee':'badge-attente'); ?>
              <span class="badge rounded-pill <?= $classe ?>"><?= nettoyer($r['statut']) ?></span>
            </td>
            <td>
              <?php if ($r['statut'] !== 'Confirmée'): ?>
                <a href="?statut=Confirmée&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-success" title="Confirmer"><i class="bi bi-check-lg"></i></a>
              <?php endif; ?>
              <?php if ($r['statut'] !== 'Annulée'): ?>
                <a href="?statut=Annulée&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger confirmer-suppression" title="Annuler"><i class="bi bi-x-lg"></i></a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/pied_admin.php'; ?>

<?php
/**
 * admin/commandes.php
 * ------------------------------------------------------------
 * Gestion des commandes du restaurant : suivi des plats commandés
 * par les clients et changement de statut.
 * ------------------------------------------------------------
 */
$pageActive = 'commandes';
require_once __DIR__ . '/includes/entete_admin.php';
$titrePage = 'Gestion des commandes';

// ---------- Mise à jour du statut d'une commande ----------
if (isset($_GET['statut'], $_GET['id'])) {
    $statutsAutorises = ['En attente','En préparation','Livrée','Annulée'];
    if (in_array($_GET['statut'], $statutsAutorises, true)) {
        $stmt = $pdo->prepare("UPDATE commandes SET statut = :s WHERE id = :id");
        $stmt->execute(['s' => $_GET['statut'], 'id' => (int) $_GET['id']]);
    }
    rediriger('commandes.php');
}

// Récupération des commandes avec les plats associés
$commandes = $pdo->query("
    SELECT c.*, u.nom_complet,
           GROUP_CONCAT(CONCAT(p.nom, ' x', cd.quantite) SEPARATOR ', ') AS plats_commandes
    FROM commandes c
    JOIN utilisateurs u ON u.id = c.utilisateur_id
    JOIN commande_details cd ON cd.commande_id = c.id
    JOIN plats p ON p.id = cd.plat_id
    GROUP BY c.id
    ORDER BY c.date_creation DESC
")->fetchAll();
?>

<h1 class="titre-luxe mb-4">Gestion des commandes</h1>

<div class="carte-stat p-0">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>N°</th>
          <th>Client</th>
          <th>Plats commandés</th>
          <th>Montant</th>
          <th>Statut</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($commandes as $c): ?>
          <tr>
            <td class="fw-bold">#<?= $c['id'] ?></td>
            <td><?= nettoyer($c['nom_complet']) ?></td>
            <td><?= $c['plats_commandes'] ? nettoyer($c['plats_commandes']) : 'Aucun plat' ?></td>
            <td><?= formaterMontant($c['montant_total']) ?></td>
            <td>
              <?php $classe = $c['statut']==='Livrée'?'badge-confirmee':($c['statut']==='Annulée'?'badge-annulee':'badge-attente'); ?>
              <span class="badge rounded-pill <?= $classe ?>"><?= nettoyer($c['statut']) ?></span>
            </td>
            <td>
              <?php if ($c['statut'] !== 'Livrée'): ?>
                <a href="?statut=Livrée&id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-success" title="Marquer comme livrée"><i class="bi bi-check-lg"></i></a>
              <?php endif; ?>
              <?php if ($c['statut'] !== 'Annulée'): ?>
                <a href="?statut=Annulée&id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger confirmer-suppression" title="Annuler"><i class="bi bi-x-lg"></i></a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/pied_admin.php'; ?>

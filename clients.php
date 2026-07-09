<?php
/**
 * admin/clients.php
 * ------------------------------------------------------------
 * Liste des clients enregistrés avec leurs statistiques.
 * ------------------------------------------------------------
 */
$pageActive = 'clients';
require_once __DIR__ . '/includes/entete_admin.php';
$titrePage = 'Gestion des clients';

// Suppression d'un client
if (isset($_GET['supprimer'])) {
    $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = :id AND role = 'client'");
    $stmt->execute(['id' => (int) $_GET['supprimer']]);
    rediriger('clients.php');
}

$clients = $pdo->query("
    SELECT u.*,
        (SELECT COUNT(*) FROM reservations r WHERE r.utilisateur_id = u.id) AS nb_reservations,
        (SELECT COALESCE(SUM(montant),0) FROM paiements p WHERE p.utilisateur_id = u.id AND p.statut='Payé') AS total_depense
    FROM utilisateurs u WHERE u.role = 'client'
    ORDER BY u.date_creation DESC
")->fetchAll();
?>

<h1 class="titre-luxe mb-4">Gestion des clients</h1>

<div class="carte-stat p-0">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Réservations</th><th>Total dépensé</th><th>Inscrit le</th><th>Action</th></tr>
      </thead>
      <tbody>
        <?php foreach ($clients as $c): ?>
          <tr>
            <td class="fw-bold"><?= nettoyer($c['nom_complet']) ?></td>
            <td><?= nettoyer($c['email']) ?></td>
            <td><?= nettoyer($c['telephone'] ?: '—') ?></td>
            <td><?= $c['nb_reservations'] ?></td>
            <td><?= formaterMontant($c['total_depense']) ?></td>
            <td><?= date('d/m/Y', strtotime($c['date_creation'])) ?></td>
            <td><a href="?supprimer=<?= $c['id'] ?>" class="btn btn-sm btn-link text-danger confirmer-suppression"><i class="bi bi-trash"></i></a></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$clients): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">Aucun client pour le moment.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/pied_admin.php'; ?>

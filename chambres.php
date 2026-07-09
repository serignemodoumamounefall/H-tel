<?php
/**
 * admin/chambres.php
 * ------------------------------------------------------------
 * Gestion des chambres : ajout, modification, suppression.
 * Toute la logique PHP est traitée en haut de la page.
 * ------------------------------------------------------------
 */
$pageActive = 'chambres';
require_once __DIR__ . '/includes/entete_admin.php';
$titrePage = 'Gestion des chambres';

$message = '';

// ---------- Ajout d'une chambre ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ajouter') {
    $numero = nettoyer($_POST['numero']);
    $type   = $_POST['type'];
    $prix   = (float) $_POST['prix_nuit'];
    $statut = $_POST['statut'];

    $stmt = $pdo->prepare("INSERT INTO chambres (numero, type, prix_nuit, statut) VALUES (:n, :t, :p, :s)");
    $stmt->execute(['n' => $numero, 't' => $type, 'p' => $prix, 's' => $statut]);
    $message = "Chambre ajoutée avec succès.";
}

// ---------- Modification d'une chambre ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'modifier') {
    $id     = (int) $_POST['id'];
    $numero = nettoyer($_POST['numero']);
    $type   = $_POST['type'];
    $prix   = (float) $_POST['prix_nuit'];
    $statut = $_POST['statut'];

    $stmt = $pdo->prepare("UPDATE chambres SET numero=:n, type=:t, prix_nuit=:p, statut=:s WHERE id=:id");
    $stmt->execute(['n' => $numero, 't' => $type, 'p' => $prix, 's' => $statut, 'id' => $id]);
    $message = "Chambre modifiée avec succès.";
}

// ---------- Suppression d'une chambre ----------
if (isset($_GET['supprimer'])) {
    $stmt = $pdo->prepare("DELETE FROM chambres WHERE id = :id");
    $stmt->execute(['id' => (int) $_GET['supprimer']]);
    rediriger('chambres.php');
}

$chambres = $pdo->query("SELECT * FROM chambres ORDER BY numero")->fetchAll();

// On construit le HTML des modales de modification à part,
// pour pouvoir les afficher EN DEHORS du tableau (voir explication plus bas).
$modalesModification = '';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="titre-luxe mb-0">Gestion des chambres</h1>
  <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalAjouterChambre">
    <i class="bi bi-plus-lg"></i> Ajouter une chambre
  </button>
</div>

<?php if ($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>

<div class="carte-stat p-0">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>N°</th><th>Type</th><th>Prix/nuit</th><th>Statut</th><th>Action</th></tr>
      </thead>
      <tbody>
        <?php foreach ($chambres as $c): ?>
          <tr>
            <td class="fw-bold"><?= nettoyer($c['numero']) ?></td>
            <td><?= nettoyer($c['type']) ?></td>
            <td><?= formaterMontant($c['prix_nuit']) ?></td>
            <td>
              <?php
                $classe = $c['statut']==='Disponible' ? 'badge-disponible' : ($c['statut']==='Occupée' ? 'badge-occupee' : 'badge-entretien');
              ?>
              <span class="badge rounded-pill <?= $classe ?>"><?= nettoyer($c['statut']) ?></span>
            </td>
            <td>
              <button class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#modalModifier<?= $c['id'] ?>"><i class="bi bi-pencil"></i></button>
              <a href="?supprimer=<?= $c['id'] ?>" class="btn btn-sm btn-link text-danger confirmer-suppression"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
          <?php
          // IMPORTANT : on n'affiche plus la modale ici (dans le <tbody>).
          // Un <div> directement dans un <tbody>/<tr> est du HTML invalide :
          // le navigateur le déplace automatiquement hors du tableau au rendu
          // (mécanisme de "foster parenting"), ce qui casse Bootstrap et
          // fait disparaître la modale sans rien soumettre.
          // On stocke donc son HTML pour l'afficher après le tableau.
          ob_start();
          ?>
          <div class="modal fade" id="modalModifier<?= $c['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
              <form method="POST" class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Modifier la chambre <?= nettoyer($c['numero']) ?></h5>
                  <button class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                  <input type="hidden" name="action" value="modifier">
                  <input type="hidden" name="id" value="<?= $c['id'] ?>">
                  <div class="mb-2"><label class="form-label">Numéro</label>
                    <input type="text" name="numero" class="form-control" value="<?= nettoyer($c['numero']) ?>" required></div>
                  <div class="mb-2"><label class="form-label">Type</label>
                    <select name="type" class="form-select">
                      <?php foreach (['Standard','Deluxe','Suite'] as $t): ?>
                        <option <?= $t===$c['type']?'selected':'' ?>><?= $t ?></option>
                      <?php endforeach; ?>
                    </select></div>
                  <div class="mb-2"><label class="form-label">Prix/nuit (€)</label>
                    <input type="number" step="0.01" name="prix_nuit" class="form-control" value="<?= $c['prix_nuit'] ?>" required></div>
                  <div class="mb-2"><label class="form-label">Statut</label>
                    <select name="statut" class="form-select">
                      <?php foreach (['Disponible','Occupée','En entretien'] as $s): ?>
                        <option <?= $s===$c['statut']?'selected':'' ?>><?= $s ?></option>
                      <?php endforeach; ?>
                    </select></div>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Annuler</button>
                  <button class="btn btn-primary" type="submit">Enregistrer</button>
                </div>
              </form>
            </div>
          </div>
          <?php
          $modalesModification .= ob_get_clean();
          ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modales de modification (une par chambre), affichées ici, HORS du tableau -->
<?= $modalesModification ?>

<!-- Modale d'ajout -->
<div class="modal fade" id="modalAjouterChambre" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Ajouter une chambre</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="action" value="ajouter">
        <div class="mb-2"><label class="form-label">Numéro</label><input type="text" name="numero" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">Type</label>
          <select name="type" class="form-select"><option>Standard</option><option>Deluxe</option><option>Suite</option></select></div>
        <div class="mb-2"><label class="form-label">Prix/nuit (€)</label><input type="number" step="0.01" name="prix_nuit" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">Statut</label>
          <select name="statut" class="form-select"><option>Disponible</option><option>Occupée</option><option>En entretien</option></select></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Annuler</button>
        <button class="btn btn-primary" type="submit">Ajouter</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/pied_admin.php'; ?>

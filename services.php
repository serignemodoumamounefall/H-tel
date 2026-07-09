<?php
/**
 * admin/services.php
 * ------------------------------------------------------------
 * Gestion des plats du restaurant proposés sur le site client.
 * ------------------------------------------------------------
 */
$pageActive = 'services';
require_once __DIR__ . '/includes/entete_admin.php';
$titrePage = 'Gestion des services';

$message = '';
$erreur  = '';

// Dossier de destination des images (relatif à la racine du site)
define('DOSSIER_UPLOAD_PLATS', __DIR__ . '/../uploads/plats/');
define('CHEMIN_PUBLIC_PLATS', 'uploads/plats/'); // chemin utilisé côté client (dans <img src="...">)

/**
 * Traite l'upload d'une image et retourne le chemin public à stocker en BDD.
 * Retourne null si aucun fichier n'a été envoyé, false en cas d'erreur.
 */
function traiterUploadImage(string $champ): string|false|null
{
    if (empty($_FILES[$champ]) || $_FILES[$champ]['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // pas de fichier envoyé (ex: en modification sans changer l'image)
    }

    $fichier = $_FILES[$champ];

    if ($fichier['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Vérification du type MIME réel (pas seulement l'extension)
    $extensionsAutorisees = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    $typeMime = mime_content_type($fichier['tmp_name']);
    if (!isset($extensionsAutorisees[$typeMime])) {
        return false;
    }

    // Taille max : 5 Mo
    if ($fichier['size'] > 5 * 1024 * 1024) {
        return false;
    }

    if (!is_dir(DOSSIER_UPLOAD_PLATS)) {
        mkdir(DOSSIER_UPLOAD_PLATS, 0755, true);
    }

    $extension    = $extensionsAutorisees[$typeMime];
    $nomFichier   = uniqid('plat_', true) . '.' . $extension;
    $cheminAbsolu = DOSSIER_UPLOAD_PLATS . $nomFichier;

    if (!move_uploaded_file($fichier['tmp_name'], $cheminAbsolu)) {
        return false;
    }

    return CHEMIN_PUBLIC_PLATS . $nomFichier;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ajouter') {
    $cheminImage = traiterUploadImage('image');

    if ($cheminImage === false) {
        $erreur = "L'image envoyée est invalide (formats acceptés : jpg, png, webp, gif — 5 Mo max).";
    } else {
        $stmt = $pdo->prepare("INSERT INTO plats (nom, categorie, prix, image, disponible) VALUES (:n,:c,:p,:i,1)");
        $stmt->execute([
            'n' => nettoyer($_POST['nom']),
            'c' => $_POST['categorie'],
            'p' => (float) $_POST['prix'],
            'i' => $cheminImage ?? '', // chaîne vide si aucune image fournie
        ]);
        $message = "Plat ajouté avec succès.";
    }
}

if (isset($_GET['basculer'])) {
    $pdo->prepare("UPDATE plats SET disponible = 1 - disponible WHERE id = :id")->execute(['id' => (int) $_GET['basculer']]);
    rediriger('services.php');
}

if (isset($_GET['supprimer'])) {
    // On supprime aussi le fichier image associé s'il existe
    $stmt = $pdo->prepare("SELECT image FROM plats WHERE id = :id");
    $stmt->execute(['id' => (int) $_GET['supprimer']]);
    $imgASupprimer = $stmt->fetchColumn();
    if ($imgASupprimer && str_starts_with($imgASupprimer, CHEMIN_PUBLIC_PLATS)) {
        $fichierASupprimer = __DIR__ . '/../' . $imgASupprimer;
        if (is_file($fichierASupprimer)) {
            @unlink($fichierASupprimer);
        }
    }

    $pdo->prepare("DELETE FROM plats WHERE id = :id")->execute(['id' => (int) $_GET['supprimer']]);
    rediriger('services.php');
}

$plats = $pdo->query("SELECT * FROM plats ORDER BY categorie, nom")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="titre-luxe mb-0">Gestion des services (restaurant)</h1>
  <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalAjouterPlat">
    <i class="bi bi-plus-lg"></i> Ajouter un plat
  </button>
</div>

<?php if ($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>
<?php if ($erreur): ?><div class="alert alert-danger"><?= nettoyer($erreur) ?></div><?php endif; ?>

<div class="carte-stat p-0">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>Image</th>
        <th>Plat</th>
        <th>Catégorie</th>
        <th>Prix</th>
        <th>Disponibilité</th><th>Action</th></tr>
      </thead>
      <tbody>
        <?php foreach ($plats as $p): ?>
          <tr>
            <td>
              <img src="<?= $p['image'] ? '../' . nettoyer($p['image']) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=100&q=80' ?>"
                   alt="<?= nettoyer($p['nom']) ?>" style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
            </td>
            <td class="fw-bold"><?= nettoyer($p['nom']) ?></td>
            <td><?= nettoyer($p['categorie']) ?></td>
            <td><?= formaterMontant($p['prix']) ?></td>
            <td>
              <a href="?basculer=<?= $p['id'] ?>" class="badge rounded-pill text-decoration-none <?= $p['disponible'] ? 'badge-disponible' : 'badge-occupee' ?>">
                <?= $p['disponible'] ? 'Disponible' : 'Indisponible' ?>
              </a>
            </td>
            <td><a href="?supprimer=<?= $p['id'] ?>" class="btn btn-sm btn-link text-danger confirmer-suppression"><i class="bi bi-trash"></i></a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="modal fade" id="modalAjouterPlat" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Ajouter un plat</h5>
      <button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="action" value="ajouter">
        <div class="mb-2"><label class="form-label" for="nom">Nom du plat</label>
        <input type="text" name="nom" id="nom" class="form-control" required></div>

        <div class="mb-2"><label class="form-label">Catégorie</label>
          <select name="categorie" class="form-select">
            <option>Petit-déjeuner</option>
            <option>Déjeuner</option>
            <option>Dîner</option>
            <option>Jus</option>
          </select></div>

        <div class="mb-2"><label class="form-label" for="prix">Prix (€)</label>
        <input type="number" step="0.01" name="prix" id="prix" class="form-control" required></div>

        <div class="mb-2"><label class="form-label" for="image">Photo du plat</label>
        <input type="file" name="image" id="image" class="form-control" accept="image/png,image/jpeg,image/webp,image/gif" required>
        <div class="form-text">Formats acceptés : JPG, PNG, WEBP, GIF — 5 Mo max.</div></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Annuler</button>
        <button class="btn btn-primary" type="submit">Ajouter</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/pied_admin.php'; ?>

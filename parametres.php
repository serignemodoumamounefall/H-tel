<?php
/**
 * admin/parametres.php
 * ------------------------------------------------------------
 * Permet à l'administrateur de modifier ses informations et
 * son mot de passe.
 * ------------------------------------------------------------
 */
$pageActive = 'parametres';
require_once __DIR__ . '/includes/entete_admin.php';
$titrePage = 'Paramètres';

$message = '';
$erreur  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'infos') {
        $nom   = nettoyer($_POST['nom_complet']);
        $email = nettoyer($_POST['email']);

        $stmt = $pdo->prepare("UPDATE utilisateurs SET nom_complet=:n, email=:e WHERE id=:id");
        $stmt->execute(['n' => $nom, 'e' => $email, 'id' => $_SESSION['utilisateur_id']]);

        $_SESSION['nom_complet'] = $nom;
        $_SESSION['email'] = $email;
        $message = "Informations mises à jour avec succès.";
    }

    if (($_POST['action'] ?? '') === 'mot_de_passe') {
        $actuel    = $_POST['mot_de_passe_actuel'];
        $nouveau   = $_POST['nouveau_mot_de_passe'];
        $confirmer = $_POST['confirmer_mot_de_passe'];

        $stmt = $pdo->prepare("SELECT mot_de_passe FROM utilisateurs WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['utilisateur_id']]);
        $hashActuel = $stmt->fetchColumn();

        if (!password_verify($actuel, $hashActuel)) {
            $erreur = "Le mot de passe actuel est incorrect.";
        } elseif ($nouveau !== $confirmer) {
            $erreur = "Les nouveaux mots de passe ne correspondent pas.";
        } elseif (strlen($nouveau) < 6) {
            $erreur = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
        } else {
            $nouveauHash = password_hash($nouveau, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE utilisateurs SET mot_de_passe=:p WHERE id=:id")
                ->execute(['p' => $nouveauHash, 'id' => $_SESSION['utilisateur_id']]);
            $message = "Mot de passe modifié avec succès.";
        }
    }
}

$admin = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
$admin->execute(['id' => $_SESSION['utilisateur_id']]);
$admin = $admin->fetch();
?>

<h1 class="titre-luxe mb-4">Paramètres</h1>

<?php if ($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>
<?php if ($erreur): ?><div class="alert alert-danger"><?= $erreur ?></div><?php endif; ?>

<div class="row g-3">
  <div class="col-12 col-lg-6">
    <div class="carte-stat">
      <h6 class="fw-bold mb-3">Informations du compte</h6>
      <form method="POST">
        <input type="hidden" name="action" value="infos">
        <div class="mb-3"><label class="form-label">Nom complet</label>
          <input type="text" name="nom_complet" class="form-control" value="<?= nettoyer($admin['nom_complet']) ?>" required></div>
        <div class="mb-3"><label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?= nettoyer($admin['email']) ?>" required></div>
        <button type="submit" class="btn btn-luxe">Enregistrer</button>
      </form>
    </div>
  </div>

  <div class="col-12 col-lg-6">
    <div class="carte-stat">
      <h6 class="fw-bold mb-3">Changer le mot de passe</h6>
      <form method="POST">
        <input type="hidden" name="action" value="mot_de_passe">
        <div class="mb-3"><label class="form-label">Mot de passe actuel</label>
          <input type="password" name="mot_de_passe_actuel" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Nouveau mot de passe</label>
          <input type="password" name="nouveau_mot_de_passe" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Confirmer le nouveau mot de passe</label>
          <input type="password" name="confirmer_mot_de_passe" class="form-control" required></div>
        <button type="submit" class="btn btn-luxe">Modifier le mot de passe</button>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/pied_admin.php'; ?>

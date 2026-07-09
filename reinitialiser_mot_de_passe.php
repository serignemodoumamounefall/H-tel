<?php
/**
 * reinitialiser_mot_de_passe.php
 * ------------------------------------------------------------
 * Étape 2 de la réinitialisation : vérifie le jeton reçu par
 * email, puis permet au client de définir un nouveau mot de passe.
 * ------------------------------------------------------------
 */
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/fonctions.php';

if (estConnecte()) {
    rediriger(estAdmin() ? 'admin/tableau_de_bord.php' : 'accueil.php');
}

$erreur     = '';
$succes     = false;
$jeton      = $_GET['jeton'] ?? ($_POST['jeton'] ?? '');
$utilisateur = null;

// ---------- Vérification de la validité du jeton ----------
if ($jeton !== '') {
    $requete = $pdo->prepare("SELECT * FROM utilisateurs WHERE jeton_reinitialisation = :jeton AND jeton_expiration > NOW()");
    $requete->execute(['jeton' => $jeton]);
    $utilisateur = $requete->fetch();
}

if ($jeton === '' || !$utilisateur) {
    $erreur = "Ce lien de réinitialisation est invalide ou a expiré. Veuillez refaire une demande.";
}

// ---------- Traitement du nouveau mot de passe ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $utilisateur) {
    $nouveau   = $_POST['mot_de_passe'] ?? '';
    $confirmer = $_POST['confirmer_mot_de_passe'] ?? '';

    if (strlen($nouveau) < 6) {
        $erreur = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif ($nouveau !== $confirmer) {
        $erreur = "Les deux mots de passe ne correspondent pas.";
    } else {
        $hash = password_hash($nouveau, PASSWORD_DEFAULT);

        // Met à jour le mot de passe et invalide le jeton (usage unique)
        $maj = $pdo->prepare("UPDATE utilisateurs
            SET mot_de_passe = :hash, jeton_reinitialisation = NULL, jeton_expiration = NULL
            WHERE id = :id");
        $maj->execute(['hash' => $hash, 'id' => $utilisateur['id']]);

        $succes = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Réinitialiser le mot de passe - Hôtel Luxury & Comfort</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">

<style>
   body{
     margin:0;height:100vh;
     background-image: url(https://globalimagecreation.com/wp-content/uploads/2018/05/home-interior-photographer-hotel-room-photography57.jpg.webp);
     background-position: center;
     background-repeat:no-repeat;
     background-size: cover;
  }
</style>

</head>
<body>
<div class="page-auth">
  <div class="carte-auth">
    <h1 class="text-center titre-luxe mb-0">Nouveau mot de passe</h1>
    <p class="text-center text-secondary mb-4">Choisissez un nouveau mot de passe sécurisé</p>

    <?php if ($erreur): ?>
      <div class="alert alert-danger py-2"><i class="bi bi-exclamation-triangle"></i> <?= $erreur ?></div>
      <?php if (!$utilisateur): ?>
        <a href="mot_de_passe_oublie.php" class="btn btn-luxe w-100">Faire une nouvelle demande</a>
      <?php endif; ?>
    <?php endif; ?>

    <?php if ($succes): ?>
      <div class="alert alert-success py-2"><i class="bi bi-check-circle"></i> Votre mot de passe a été réinitialisé avec succès !</div>
      <a href="connexion.php" class="btn btn-luxe w-100">Se connecter</a>

    <?php elseif ($utilisateur): ?>
      <form method="POST" novalidate>
        <input type="hidden" name="jeton" value="<?= nettoyer($jeton) ?>">
        <div class="mb-3">
          <label class="form-label fw-semibold">Nouveau mot de passe</label>
          <input type="password" class="form-control" name="mot_de_passe" placeholder="6 caractères minimum" required>
        </div>
        <div class="mb-4">
          <label class="form-label fw-semibold">Confirmer le mot de passe</label>
          <input type="password" class="form-control" name="confirmer_mot_de_passe" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn btn-luxe w-100">Réinitialiser le mot de passe</button>
      </form>
    <?php endif; ?>

    <p class="text-center mt-4 mb-0">
      <a href="connexion.php" class="fw-semibold text-decoration-none">Retour à la connexion</a>
    </p>
  </div>
</div>
</body>
</html>

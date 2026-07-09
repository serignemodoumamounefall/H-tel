<?php
/**
 * mot_de_passe_oublie.php
 * ------------------------------------------------------------
 * Étape 1 de la réinitialisation : le client saisit son email.
 * Un jeton unique est généré, enregistré en base avec une durée
 * de validité d'une heure, puis un lien de réinitialisation est
 * "envoyé" (voir includes/fonctions.php::envoyerEmailReinitialisation).
 * ------------------------------------------------------------
 */
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/fonctions.php';

if (estConnecte()) {
    rediriger(estAdmin() ? 'admin/tableau_de_bord.php' : 'accueil.php');
}

$erreur  = '';
$message = '';
$lienDemo = ''; // utilisé uniquement pour la démonstration sans serveur d'emails

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = nettoyer($_POST['email'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Veuillez saisir une adresse email valide.";
    } else {
        $requete = $pdo->prepare("SELECT id, nom_complet FROM utilisateurs WHERE email = :email");
        $requete->execute(['email' => $email]);
        $utilisateur = $requete->fetch();

        // Message volontairement identique que le compte existe ou non
        // (bonne pratique de sécurité : ne pas révéler si un email est inscrit)
        $message = "Si un compte existe avec cet email, un lien de réinitialisation vient d'être envoyé.";

        if ($utilisateur) {
            $jeton      = genererJeton();
            $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $maj = $pdo->prepare("UPDATE utilisateurs SET jeton_reinitialisation = :jeton, jeton_expiration = :exp WHERE id = :id");
            $maj->execute(['jeton' => $jeton, 'exp' => $expiration, 'id' => $utilisateur['id']]);

            $lien = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] .
                    dirname($_SERVER['REQUEST_URI']) . '/reinitialiser_mot_de_passe.php?jeton=' . $jeton;

            envoyerEmailReinitialisation($email, $lien);

            // NB : en environnement de démonstration (sans serveur SMTP), on affiche
            // directement le lien à l'écran pour permettre de tester le parcours complet.
            $lienDemo = $lien;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mot de passe oublié - Hôtel Luxury & Comfort</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>

<style>
   body{
     margin:0;height:100vh;
     background-image: url(https://globalimagecreation.com/wp-content/uploads/2018/05/home-interior-photographer-hotel-room-photography57.jpg.webp);
     background-position: center;
     background-repeat:no-repeat;
     background-size: cover;
  }
</style>

<body>
<div class="page-auth">
  <div class="carte-auth">
    <a href="connexion.php" class="text-decoration-none text-dark"><i class="bi bi-arrow-left fs-4"></i></a>
    <h1 class="text-center titre-luxe mb-0 mt-2">Mot de passe oublié</h1>
    <p class="text-center text-secondary mb-4">Saisissez votre email pour recevoir un lien de réinitialisation</p>

    <?php if ($erreur): ?>
      <div class="alert alert-danger py-2"><i class="bi bi-exclamation-triangle"></i> <?= $erreur ?></div>
    <?php endif; ?>

    <?php if ($message): ?>
      <div class="alert alert-success py-2"><i class="bi bi-check-circle"></i> <?= $message ?></div>

      <?php if ($lienDemo): ?>
        <div class="alert alert-warning small">
          <i class="bi bi-info-circle"></i> <strong>Mode démonstration</strong> (aucun serveur d'envoi d'email n'est configuré) :
          cliquez sur le lien ci-dessous pour continuer la réinitialisation.<br>
          <a href="<?= nettoyer($lienDemo) ?>" class="d-block mt-2 text-break"><?= nettoyer($lienDemo) ?></a>
        </div>
      <?php endif; ?>
    <?php else: ?>

    <form method="POST" novalidate>
      <div class="mb-4">
        <label class="form-label fw-semibold">Email</label>
        <input type="email" class="form-control" name="email" placeholder="exemple@email.com" required>
      </div>
      <button type="submit" class="btn btn-luxe w-100">Envoyer le lien de réinitialisation</button>
    </form>

    <?php endif; ?>

    <p class="text-center mt-4 mb-0">
      <a href="connexion.php" class="fw-semibold text-decoration-none">Retour à la connexion</a>
    </p>
  </div>
</div>
</body>
</html>

<?php
/**
 * inscription.php
 * ------------------------------------------------------------
 * Création d'un nouveau compte client. Traitement PHP effectué
 * en haut du fichier avant l'affichage de la page.
 * ------------------------------------------------------------
 */
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/fonctions.php';

if (estConnecte()) {
    rediriger(estAdmin() ? 'admin/tableau_de_bord.php' : 'accueil.php');
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom        = nettoyer($_POST['nom_complet'] ?? '');
    $email      = nettoyer($_POST['email'] ?? '');
    $telephone  = nettoyer($_POST['telephone'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';
    $confirmer  = $_POST['confirmer_mot_de_passe'] ?? '';

    if ($nom === '' || $email === '' || $motDePasse === '') {
        $erreur = "Veuillez remplir tous les champs obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse email n'est pas valide.";
    } elseif (strlen($motDePasse) < 6) {
        $erreur = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif ($motDePasse !== $confirmer) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifie que l'email n'existe pas déjà
        $verif = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
        $verif->execute(['email' => $email]);

        if ($verif->fetch()) {
            $erreur = "Un compte existe déjà avec cet email.";
        } else {
            $hash = password_hash($motDePasse, PASSWORD_DEFAULT);
            $ajout = $pdo->prepare("INSERT INTO utilisateurs (nom_complet, email, telephone, mot_de_passe, role) VALUES (:nom, :email, :tel, :pass, 'client')");
            $ajout->execute([
                'nom'   => $nom,
                'email' => $email,
                'tel'   => $telephone,
                'pass'  => $hash,
            ]);

            rediriger('connexion.php?inscription=ok');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Créer un compte - Hôtel Luxury & Comfort</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">

<style>
    
      body{
     margin:0;
     height:140vh;
     background-image: url(https://globalimagecreation.com/wp-content/uploads/2018/05/home-interior-photographer-hotel-room-photography57.jpg.webp);
     background-position: center;
     background-repeat:no-repeat;
     background-size: cover;
  }

  @media (max-width: 767.98px) {
   body { height:100vh; }
}

 @media (max-width: 1000px) {
   body { height:100vh; }
}

</style>

</head>
<body>
<div class="page-auth">
  <div class="carte-auth">
    <h1 class="text-center titre-luxe mb-0">Créer un compte</h1>
    <p class="text-center text-secondary mb-4">Rejoignez-nous en quelques secondes</p>

    <?php if ($erreur): ?>
      <div class="alert alert-danger py-2"><i class="bi bi-exclamation-triangle"></i> <?= $erreur ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="mb-3">
        <label class="form-label fw-semibold">Nom complet</label>
        <input type="text" class="form-control" name="nom_complet" placeholder="Votre nom et prénom" required value="<?= nettoyer($_POST['nom_complet'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Email</label>
        <input type="email" class="form-control" name="email" placeholder="exemple@email.com" required value="<?= nettoyer($_POST['email'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Numéro de téléphone</label>
        <input type="text" class="form-control" name="telephone" placeholder="+221 77 000 00 00" value="<?= nettoyer($_POST['telephone'] ?? '') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Mot de passe</label>
        <input type="password" class="form-control" name="mot_de_passe" placeholder="6 caractères minimum" required>
      </div>
      <div class="mb-4">
        <label class="form-label fw-semibold">Confirmer le mot de passe</label>
        <input type="password" class="form-control" name="confirmer_mot_de_passe" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn btn-luxe w-100">Créer un compte</button>
    </form>

    <p class="text-center mt-4 mb-0">Vous avez déjà un compte ? <a href="connexion.php" class="fw-semibold">Se connecter</a></p>
  </div>
</div>
</body>
</html>

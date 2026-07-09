<?php
/**
 * connexion.php
 * ------------------------------------------------------------
 * Page de connexion. Le traitement PHP du formulaire (vérification
 * des identifiants, démarrage de session) est effectué directement
 * en haut de cette page avant l'affichage du HTML.
 * ------------------------------------------------------------
 */
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/fonctions.php';

// Si l'utilisateur est déjà connecté, on le redirige
if (estConnecte()) {
    rediriger(estAdmin() ? 'admin/tableau_de_bord.php' : 'accueil.php');
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = nettoyer($_POST['identifiant'] ?? '');
    $motDePasse  = $_POST['mot_de_passe'] ?? '';

    if ($identifiant === '' || $motDePasse === '') {
        $erreur = "Veuillez renseigner votre email/téléphone et votre mot de passe.";
    } else {
        // Recherche de l'utilisateur par email ou téléphone
        $requete = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email OR telephone = :telephone LIMIT 1");
        $requete->execute(['email' => $identifiant,
          'telephone' => $identifiant]);
        $utilisateur = $requete->fetch();

        if ($utilisateur && password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
            // Identifiants corrects : on initialise la session
            $_SESSION['utilisateur_id'] = $utilisateur['id'];
            $_SESSION['nom_complet']    = $utilisateur['nom_complet'];
            $_SESSION['email']          = $utilisateur['email'];
            $_SESSION['role']           = $utilisateur['role'];

            rediriger($utilisateur['role'] === 'admin' ? 'admin/tableau_de_bord.php' : 'accueil.php');
        } else {
            $erreur = "Email/téléphone ou mot de passe incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion - Hôtel Luxury & Comfort</title>
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

 /* .container{
    margin:0;
    height:100vh;
    background:linear-gradient(160deg,#1B263B,#0D182A);
    opacity: 0.8;
  
  }*/
</style>
</head>
<body>

    <div class="page-auth">
      <div class="carte-auth">
        <h1 class="text-center titre-luxe mb-0">Connexion</h1>
        <p class="text-center text-secondary mb-4">Bienvenue de retour !</p>

        <?php if ($erreur): ?>
          <div class="alert alert-danger py-2"><i class="bi bi-exclamation-triangle"></i> <?= $erreur ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['inscription']) && $_GET['inscription'] === 'ok'): ?>
          <div class="alert alert-success py-2"><i class="bi bi-check-circle"></i> Compte créé avec succès. Connectez-vous.</div>
        <?php endif; ?>

        <form method="POST" novalidate>
          <div class="mb-3">
            <label class="form-label fw-semibold">Email ou numéro de téléphone</label>
            <input type="text" class="form-control" name="identifiant" placeholder="exemple@email.com" required value="<?= nettoyer($_POST['identifiant'] ?? '') ?>">
          </div>
          <div class="mb-2">
            <label class="form-label fw-semibold">Mot de passe</label>
            <input type="password" class="form-control" name="mot_de_passe" placeholder="••••••••" required>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="se_souvenir" id="souvenir">
              <label class="form-check-label small" for="souvenir">Se souvenir de moi</label>
            </div>
            <a href="reinitialiser_mot_de_passe.php" class="small text-decoration-none">Mot de passe oublié ?</a>
          </div>
          <button type="submit" class="btn btn-luxe w-100">Se connecter</button>
        </form>

        <div class="text-center text-secondary my-3">Ou continuer avec</div>
        <div class="d-flex justify-content-center gap-2 mb-4">
          <button class="btn btn-outline-secondary rounded-circle p-2" type="button"><i class="bi bi-google"></i></button>
          <button class="btn btn-outline-secondary rounded-circle p-2" type="button"><i class="bi bi-facebook"></i></button>
          <button class="btn btn-outline-secondary rounded-circle p-2" type="button"><i class="bi bi-apple"></i></button>
        </div>

        <p class="text-center mb-0">Vous n'avez pas de compte ? <a href="inscription.php" class="fw-semibold">Créer un compte</a></p>
        <p class="text-center mt-2"><small class="text-muted">Compte admin de démo : admin@hotel.com / Admin123</small></p>
      </div>
    </div>

</body>
</html>

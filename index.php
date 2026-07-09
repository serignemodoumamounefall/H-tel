<?php
/**
 * index.php
 * ------------------------------------------------------------
 * Page de démarrage (splash screen). Redirige automatiquement
 * le visiteur vers l'accueil s'il est déjà connecté, sinon
 * vers la page de connexion après un court instant.
 * ------------------------------------------------------------
 */
session_start();
require_once __DIR__ . '/includes/fonctions.php';

if (estConnecte()) {
    rediriger(estAdmin() ? 'admin/tableau_de_bord.php' : 'accueil.php');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hôtel Luxury & Comfort</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Poppins:wght@500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
<style>
 body{
   margin:0;height:100vh;
     background-image: url(https://globalimagecreation.com/wp-content/uploads/2018/05/home-interior-photographer-hotel-room-photography57.jpg.webp);
     background-position: center;
     background-repeat:no-repeat;
     background-size: cover;
  }
  .container{
    margin:0;height:100vh;
   display:flex;flex-direction:column;align-items:center;justify-content:center;
    background:linear-gradient(160deg,#1B263B,#0D182A);
    opacity: 0.8;
    font-family:'Poppins',sans-serif;color:#fff;text-align:center;
  }
  .cercle-logo{
    width:120px;height:120px;border:3px solid #FFC107;border-radius:50%;
    display:flex;align-items:center;justify-content:center;margin-bottom:1rem;
  }
  .cercle-logo i{font-size:3rem;color:#FFC107;}
  h1{font-family:'Playfair Display',serif;font-weight:800;letter-spacing:4px;margin:0;}
  p.devise{color:#FFC107;letter-spacing:2px;font-size:0.85rem;text-transform:uppercase;margin-top:0.3rem;}
  .spinner-border{margin-top:2rem;color:#FFC107;}
</style>
</head>
<body>
    <div class="container">
      <div class="cercle-logo"><i class="bi bi-emoji-smile"></i></div>
      <h1>HOTEL</h1>
      <p class="devise">Luxury &amp; Comfort</p>
      <div class="spinner-border" role="status"><span class="visually-hidden">Chargement...</span></div>
    </div>
  <script>
    // Redirection automatique vers la page de connexion après 2 seconde
    setTimeout(function () {
      window.location.href = 'connexion.php';
    }, 2000);
  </script>
</body>
</html>

<?php
/**
 * reservation.php
 * ------------------------------------------------------------
 * Affiche les chambres disponibles et traite la création d'une
 * réservation. Le traitement PHP est fait en haut de la page.
 * ------------------------------------------------------------
 */
$pageActive = 'reservation';
require_once __DIR__ . '/includes/entete.php';

if (!estConnecte()) {
    rediriger('connexion.php');
}

$titrePage = 'Réserver une chambre';
$erreur = '';

// ---------- Traitement du formulaire de réservation ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chambreId      = (int) ($_POST['chambre_id'] ?? 0);
    $dateArrivee    = $_POST['date_arrivee'] ?? '';
    $dateDepart     = $_POST['date_depart'] ?? '';
    $nombreChambres = max(1, (int) ($_POST['nombre_chambres'] ?? 1));

    if (!$chambreId || !$dateArrivee || !$dateDepart) {
        $erreur = "Veuillez sélectionner une chambre et des dates valides.";
    } elseif (strtotime($dateDepart) <= strtotime($dateArrivee)) {
        $erreur = "La date de départ doit être après la date d'arrivée.";
    } else {
        $chambreReq = $pdo->prepare("SELECT * FROM chambres WHERE id = :id AND statut = 'Disponible'");
        $chambreReq->execute(['id' => $chambreId]);
        $chambre = $chambreReq->fetch();

        if (!$chambre) {
            $erreur = "Cette chambre n'est plus disponible.";
        } else {
            $nuits   = calculerNuits($dateArrivee, $dateDepart);
            $montant = $nuits * $chambre['prix_nuit'] * $nombreChambres;
            $code    = genererCodeReservation();

            $insertion = $pdo->prepare("INSERT INTO reservations
                (code_reservation, utilisateur_id, chambre_id, date_arrivee, date_depart, nombre_chambres, montant_total, statut)
                VALUES (:code, :uid, :cid, :arr, :dep, :nb, :montant, 'En attente')");
            $insertion->execute([
                'code'    => $code,
                'uid'     => $_SESSION['utilisateur_id'],
                'cid'     => $chambreId,
                'arr'     => $dateArrivee,
                'dep'     => $dateDepart,
                'nb'      => $nombreChambres,
                'montant' => $montant,
            ]);

            $idReservation = $pdo->lastInsertId();
            rediriger('paiement.php?type=reservation&id=' . $idReservation);
        }
    }
}

// Récupération des chambres disponibles à afficher
$chambres = $pdo->query("SELECT * FROM chambres ORDER BY FIELD(type,'Standard','Deluxe','Suite')")->fetchAll();
?>

<div class="container py-4" style="max-width:560px;">

  <a href="accueil.php" class="text-decoration-none text-dark"><i class="bi bi-arrow-left fs-4"></i></a>
  <h1 class="titre-luxe d-inline-block ms-2 align-middle">Réserver une chambre</h1>

  <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=900&q=80"
       class="img-fluid rounded-4 my-3" alt="Chambre d'hôtel">

  <?php if ($erreur): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= $erreur ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="row g-3 mb-3">
      <div class="col-6">
        <label class="form-label fw-semibold">Date d'arrivée</label>
        <input type="date" name="date_arrivee" class="form-control" required min="<?= date('Y-m-d') ?>" value="<?= nettoyer($_POST['date_arrivee'] ?? date('Y-m-d')) ?>">
      </div>
      <div class="col-6">
        <label class="form-label fw-semibold">Date de départ</label>
        <input type="date" name="date_depart" class="form-control" required value="<?= nettoyer($_POST['date_depart'] ?? date('Y-m-d', strtotime('+1 day'))) ?>">
      </div>
    </div>

    <label class="form-label fw-semibold">Type de chambre</label>
    <div class="mb-3">
      <?php foreach ($chambres as $i => $c): ?>
        <div class="carte-luxe choix-chambre d-flex justify-content-between align-items-center p-3 mb-2 <?= ($i===0)?'selectionne':'' ?>" style="cursor:pointer;">
          <div>
            <strong><?= nettoyer($c['type']) ?></strong>
            <div class="small text-muted">Chambre N° <?= nettoyer($c['numero']) ?> ·
              <?php if ($c['statut']==='Disponible'): ?>
                <span class="badge badge-disponible rounded-pill">Disponible</span>
              <?php elseif ($c['statut']==='Occupée'): ?>
                <span class="badge badge-occupee rounded-pill">Occupée</span>
              <?php else: ?>
                <span class="badge badge-entretien rounded-pill">En entretien</span>
              <?php endif; ?>
            </div>
          </div>
          <div class="text-end">
            <span class="fw-bold"><?= formaterMontant($c['prix_nuit']) ?>/nuit</span><br>
            <input class="form-check-input" type="radio" name="chambre_id" value="<?= $c['id'] ?>" <?= ($i===0)?'checked':'' ?> <?= $c['statut']!=='Disponible'?'disabled':'' ?>>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="carte-luxe d-flex justify-content-between align-items-center p-3 mb-4">
      <span class="fw-semibold">Nombre de chambres</span>
      <div class="selecteur-nombre d-flex align-items-center gap-2">
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle btn-moins">−</button>
        <input type="number" name="nombre_chambres" value="1" min="1" class="form-control text-center" style="width:60px;">
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle btn-plus">+</button>
      </div>
    </div>

    <button type="submit" class="btn btn-luxe w-100 py-3">Voir les disponibilités et réserver</button>
  </form>

  <hr class="my-4">
  <h5 class="titre-luxe">Mes réservations récentes</h5>
  <?php
    $mesReservations = $pdo->prepare("SELECT r.*, c.numero, c.type FROM reservations r
        JOIN chambres c ON c.id = r.chambre_id
        WHERE r.utilisateur_id = :uid ORDER BY r.date_creation DESC LIMIT 5");
    $mesReservations->execute(['uid' => $_SESSION['utilisateur_id']]);
    $liste = $mesReservations->fetchAll();
  ?>
  <?php if (!$liste): ?>
    <p class="text-muted">Vous n'avez aucune réservation pour le moment.</p>
  <?php else: ?>
    <?php foreach ($liste as $r): ?>
      <div class="carte-luxe p-3 mb-2 d-flex justify-content-between align-items-center">
        <div>
          <strong><?= nettoyer($r['type']) ?> — N° <?= nettoyer($r['numero']) ?></strong>
          <div class="small text-muted"><?= date('d/m/Y', strtotime($r['date_arrivee'])) ?> → <?= date('d/m/Y', strtotime($r['date_depart'])) ?></div>
        </div>
        <div class="text-end">
          <div class="fw-bold"><?= formaterMontant($r['montant_total']) ?></div>
          <span class="badge rounded-pill badge-<?= $r['statut']==='Confirmée'?'confirmee':($r['statut']==='Annulée'?'annulee':'attente') ?>"><?= nettoyer($r['statut']) ?></span>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/pied.php'; ?>

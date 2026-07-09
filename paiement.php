<?php
/**
 * paiement.php
 * ------------------------------------------------------------
 * Page de paiement, utilisée pour régler soit une réservation
 * de chambre, soit une commande au restaurant (panier en session).
 * ------------------------------------------------------------
 */
$pageActive = 'paiement';
require_once __DIR__ . '/includes/entete.php';

if (!estConnecte()) {
    rediriger('connexion.php');
}

$titrePage = 'Paiement';
$erreur = '';
$type = $_GET['type'] ?? 'reservation'; // 'reservation' ou 'commande'
$montantAPayer = 0;
$reservation = null;

// ---------- Détermination du montant à payer ----------
if ($type === 'reservation') {
    $idReservation = (int) ($_GET['id'] ?? 0);
    $req = $pdo->prepare("SELECT * FROM reservations WHERE id = :id AND utilisateur_id = :uid");
    $req->execute(['id' => $idReservation, 'uid' => $_SESSION['utilisateur_id']]);
    $reservation = $req->fetch();

    if (!$reservation) {
        rediriger('reservation.php');
    }
    $montantAPayer = $reservation['montant_total'];
} else { // commande restaurant
    if (empty($_SESSION['panier'])) {
        rediriger('restaurant.php');
    }
    $ids = implode(',', array_map('intval', array_keys($_SESSION['panier'])));
    $platsPanier = $pdo->query("SELECT * FROM plats WHERE id IN ($ids)")->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
    foreach ($_SESSION['panier'] as $id => $qte) {
        if (isset($platsPanier[$id])) {
            $montantAPayer += $platsPanier[$id]['prix'] * $qte;
        }
    }
}

// ---------- Traitement du paiement ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $methode = $_POST['methode'] ?? 'Carte bancaire';

    $pdo->beginTransaction();
    try {
        if ($type === 'reservation') {
            // Confirme la réservation
            $maj = $pdo->prepare("UPDATE reservations SET statut = 'Confirmée' WHERE id = :id");
            $maj->execute(['id' => $reservation['id']]);

            // Marque la chambre comme occupée
            $majChambre = $pdo->prepare("UPDATE chambres SET statut = 'Occupée' WHERE id = :id");
            $majChambre->execute(['id' => $reservation['chambre_id']]);

            $paiement = $pdo->prepare("INSERT INTO paiements (utilisateur_id, reservation_id, montant, methode, statut)
                VALUES (:uid, :rid, :montant, :methode, 'Payé')");
            $paiement->execute([
                'uid'     => $_SESSION['utilisateur_id'],
                'rid'     => $reservation['id'],
                'montant' => $montantAPayer,
                'methode' => $methode,
            ]);
        } else {
            // Création de la commande restaurant
            $commande = $pdo->prepare("INSERT INTO commandes (utilisateur_id, montant_total, statut) VALUES (:uid, :montant, 'En attente')");
            $commande->execute(['uid' => $_SESSION['utilisateur_id'], 'montant' => $montantAPayer]);
            $idCommande = $pdo->lastInsertId();

            $detailStmt = $pdo->prepare("INSERT INTO commande_details (commande_id, plat_id, quantite, prix_unitaire) VALUES (:cid, :pid, :qte, :prix)");
            foreach ($_SESSION['panier'] as $platId => $qte) {
                if (isset($platsPanier[$platId])) {
                    $detailStmt->execute([
                        'cid'  => $idCommande,
                        'pid'  => $platId,
                        'qte'  => $qte,
                        'prix' => $platsPanier[$platId]['prix'],
                    ]);
                }
            }

            $paiement = $pdo->prepare("INSERT INTO paiements (utilisateur_id, commande_id, montant, methode, statut)
                VALUES (:uid, :cid, :montant, :methode, 'Payé')");
            $paiement->execute([
                'uid'     => $_SESSION['utilisateur_id'],
                'cid'     => $idCommande,
                'montant' => $montantAPayer,
                'methode' => $methode,
            ]);

            $_SESSION['panier'] = []; // on vide le panier
        }

        $pdo->commit();
        rediriger('profil.php?paiement=ok');
    } catch (Exception $e) {
        $pdo->rollBack();
        $erreur = "Une erreur est survenue lors du paiement. Veuillez réessayer.";
    }
}
?>

<div class="container py-4" style="max-width:520px;">

  <a href="javascript:history.back()" class="text-decoration-none text-dark"><i class="bi bi-arrow-left fs-4"></i></a>
  <h1 class="titre-luxe d-inline-block ms-2 align-middle">Paiement</h1>
  <hr>

  <?php if ($erreur): ?>
    <div class="alert alert-danger"><?= $erreur ?></div>
  <?php endif; ?>

  <p class="text-muted mb-0 mt-4">Montant à payer</p>
  <h2 class="display-6 fw-bold mb-4"><?= formaterMontant($montantAPayer) ?></h2>

  <form method="POST">
    <label class="form-label fw-semibold">Méthode de paiement</label>

    <div class="carte-luxe choix-paiement d-flex align-items-center justify-content-between p-3 mb-2 selectionne" style="cursor:pointer;">
      <span><i class="bi bi-credit-card-2-front me-2"></i> Carte bancaire</span>
      <input type="radio" name="methode" value="Carte bancaire" class="form-check-input" checked>
    </div>
    <div class="carte-luxe choix-paiement d-flex align-items-center justify-content-between p-3 mb-2" style="cursor:pointer;">
      <span><i class="bi bi-phone me-2"></i> Mobile money</span>
      <input type="radio" name="methode" value="Mobile money" class="form-check-input">
    </div>
    <div class="carte-luxe choix-paiement d-flex align-items-center justify-content-between p-3 mb-4" style="cursor:pointer;">
      <span><i class="bi bi-paypal me-2"></i> PayPal</span>
      <input type="radio" name="methode" value="PayPal" class="form-check-input">
    </div>

    <label class="form-label fw-semibold">Information de la carte</label>
    <div class="mb-3">
      <label class="form-label small">Numéro de la carte</label>
      <input type="text" class="form-control" maxlength="19" placeholder="•••• •••• •••• ••••">
    </div>
    <div class="row g-3 mb-4">
      <div class="col-6">
        <label class="form-label small">Date d'expiration</label>
        <input type="text" class="form-control" placeholder="mm/aa">
      </div>
      <div class="col-6">
        <label class="form-label small">CVV</label>
        <input type="text" class="form-control" placeholder="•••" maxlength="3">
      </div>
    </div>

    <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
      <span>Montant à payer</span><span><?= formaterMontant($montantAPayer) ?></span>
    </div>

    <button type="submit" class="btn btn-luxe w-100 py-3">Payer maintenant</button>
  </form>
</div>

<?php require_once __DIR__ . '/includes/pied.php'; ?>

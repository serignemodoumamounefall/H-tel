<?php
/**
 * includes/fonctions.php
 * ------------------------------------------------------------
 * Fonctions utilitaires utilisées dans plusieurs pages du site.
 * ------------------------------------------------------------
 */

// Nettoie une chaîne reçue d'un formulaire (anti-XSS basique)
function nettoyer($valeur)
{
    return htmlspecialchars(trim($valeur), ENT_QUOTES, 'UTF-8');
}

// Vérifie si un utilisateur est connecté
function estConnecte()
{
    return isset($_SESSION['utilisateur_id']);
}

// Vérifie si l'utilisateur connecté est un administrateur
function estAdmin()
{
    return estConnecte() && $_SESSION['role'] === 'admin';
}

// Redirige vers une page donnée et arrête le script
function rediriger($url)
{
    header("Location: " . $url);
    exit;
}

// Génère un code unique pour une réservation (ex : RSV-20260630-83F2)
function genererCodeReservation()
{
    return 'RSV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
}

// Formate un montant en euros
function formaterMontant($montant)
{
    return number_format((float) $montant, 2, ',', ' ') . ' €';
}

// Calcule le nombre de nuits entre deux dates
function calculerNuits($dateArrivee, $dateDepart)
{
    $debut = new DateTime($dateArrivee);
    $fin   = new DateTime($dateDepart);
    $diff  = $debut->diff($fin)->days;
    return max(1, $diff);
}


// Génère un jeton sécurisé pour la réinitialisation de mot de passe
function genererJeton()
{
    return bin2hex(random_bytes(32));
}

/**
 * Simule l'envoi d'un email (aucun serveur SMTP configuré dans cet
 * environnement de démonstration). En production, remplacez le
 * contenu de cette fonction par un véritable envoi (PHPMailer, etc.)
 * et supprimez le retour du lien dans la page d'appel.
 */
function envoyerEmailReinitialisation($emailDestinataire, $lienReinitialisation)
{
    // Trace de l'envoi dans le journal des erreurs PHP (à titre de preuve en démo)
    error_log("Email de réinitialisation envoyé à $emailDestinataire : $lienReinitialisation");
    return true;
}

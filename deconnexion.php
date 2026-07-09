<?php
/**
 * deconnexion.php
 * ------------------------------------------------------------
 * Détruit la session en cours et redirige vers la connexion.
 * ------------------------------------------------------------
 */
session_start();
$_SESSION = [];
session_destroy();
header('Location: connexion.php');
exit;

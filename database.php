<?php
/**
 * config/database.php
 * ------------------------------------------------------------
 * Fichier de connexion à la base de données MySQL via PDO.
 * Modifiez les constantes ci-dessous selon votre environnement.
 * ------------------------------------------------------------
 */

// Paramètres de connexion à la base de données
define('DB_HOTE', 'localhost');
define('DB_NOM', 'hotel_luxury');
define('DB_UTILISATEUR', 'root');
define('DB_MOT_DE_PASSE', '');

try {
    // Création de la connexion PDO avec gestion des erreurs en exceptions
    $pdo = new PDO(
        "mysql:host=" . DB_HOTE . ";dbname=" . DB_NOM . ";charset=utf8mb4",
        DB_UTILISATEUR,
        DB_MOT_DE_PASSE,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // En cas d'échec de connexion, on arrête l'exécution proprement
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

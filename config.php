<?php
// Assurez-vous que cette ligne est au début du fichier
require_once 'database.php';

// Configurations générales de l'application
define('BASE_URL', 'https://extranet.emotionipssi.com');
define('SITE_NAME', 'E-Motion');

// Configurations de session (à appliquer avant le démarrage de la session)
function configure_session() {
    ini_set('session.gc_maxlifetime', 3600);
    ini_set('session.cookie_lifetime', 3600);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1); // Uniquement si HTTPS est utilisé
    ini_set('session.cookie_samesite', 'Lax');
}

// Autres configurations globales
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB

// Fonction de logging générale
function app_log($message, $level = 'INFO') {
    if (DEBUG_MODE) {
        error_log("[APP] [$level] $message");
    }
}

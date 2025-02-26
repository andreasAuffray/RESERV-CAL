<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Générer un token CSRF si absent
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fonction pour vérifier le token
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function generateCsrfToken() {
    // Vérifie si le token CSRF existe déjà
    if (empty($_SESSION['csrf_token'])) {
        // Génère un nouveau token CSRF
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Sécurisé et unique
    }
    return $_SESSION['csrf_token'];
}
?>



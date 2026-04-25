<?php
// On s'assure que la session est démarrée pour pouvoir la détruire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. On vide toutes les variables de session
$_SESSION = array();

// 2. On détruit la session physiquement
session_destroy();

// 3. Redirection vers l'index (qui renverra vers login car la session est vide)
header("Location: index.php?page=login");
exit;
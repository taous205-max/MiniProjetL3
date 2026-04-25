<?php


// Initialisation du modèle
$infirmierModel = new InfirmierModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Sauvegarde temporaire pour réafficher en cas d'erreur
    $_SESSION['old_username'] = $username;
    unset($_SESSION['login_debug']);

    if (empty($username) || empty($password)) {
        header("Location: index.php?page=login&error=empty");
        exit();
    }

    // Appel de la méthode d'authentification sécurisée
    $user = $infirmierModel->authenticate($username, $password);

    if ($user) {
        // Nettoyage des anciennes valeurs
        unset($_SESSION['old_username']);

        // Stockage des informations en session
        $_SESSION['user'] = $user; 
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = 'infirmier'; 
        
        // Reset des sélections précédentes pour forcer un nouveau choix médecin
        unset($_SESSION['id_medecin_choisi']);
        unset($_SESSION['nom_medecin_choisi']);
        
        // Redirection vers la suite du parcours
        header("Location: index.php?page=choix_medecin");
        exit();
        
    } else {
        // Identifiants incorrects (ou hash non valide en BDD)
        header("Location: index.php?page=login&error=invalid");
        exit();
    }
} else {
    header("Location: index.php?page=login");
    exit();
}
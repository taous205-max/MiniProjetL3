<?php
// On inclut le fichier qui crée la connexion $pdo
require_once __DIR__ . '/../../config/db.php';

// Utilisation de la variable $pdo définie dans db.php
$db = $pdo; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Sauvegarde temporaire pour réafficher les champs en cas d'erreur
    $_SESSION['old_username'] = $username;
    $_SESSION['old_password'] = $password;

    if (empty($username) || empty($password)) {
        header("Location: index.php?page=login&error=empty");
        exit();
    }

    /**
     * CORRECTION MAJEURE : 
     * On joint la table 'utilisateur' avec la table 'infirmier' 
     * pour récupérer la colonne 'id_specialite' indispensable au filtrage.
     */
    $sql = "SELECT u.*, i.id_specialite 
            FROM utilisateur u 
            LEFT JOIN infirmier i ON u.id = i.id 
            WHERE u.username = ? AND u.mot_de_passe = ?";
            
    $stmt = $db->prepare($sql);
    $stmt->execute([$username, $password]); 
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Nettoyage des anciennes valeurs de formulaire
        unset($_SESSION['old_username'], $_SESSION['old_password']);

        /**
         * Stockage complet de l'utilisateur en session.
         * Contient désormais : id, username, nom, prenom, role ET id_specialite.
         */
        $_SESSION['user'] = $user; 
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = 'infirmier'; 
        
        // On supprime un éventuel ancien choix de médecin pour forcer une nouvelle sélection propre
        unset($_SESSION['id_medecin_choisi']);
        unset($_SESSION['nom_medecin_choisi']);
        
        // Redirection vers la page de sélection (qui sera maintenant filtrée par spécialité)
        header("Location: index.php?page=choix_medecin");
        exit();
        
    } else {
        // Identifiants incorrects
        header("Location: index.php?page=login&error=invalid");
        exit();
    }
} else {
    // Si on tente d'accéder au fichier sans formulaire POST
    header("Location: index.php?page=login");
    exit();
}
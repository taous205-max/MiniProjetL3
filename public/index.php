<?php
session_start();

require_once 'C:/wamp64/www/santepro/config/db.php';
require_once __DIR__ . '/../app/Models/RendezVousModel.php';
require_once __DIR__ . '/../app/Models/MedecinModel.php';
require_once __DIR__ . '/../app/Models/TicketModel.php'; 

$page = $_GET['page'] ?? 'dashbord';

if (isset($_GET['action']) && $_GET['action'] === 'select_doctor') {
    if (isset($_GET['id'])) {
        $mModel = new MedecinModel($pdo);
        $medInfo = $mModel->getMedecinById($_GET['id']);
        $_SESSION['id_medecin_choisi'] = $_GET['id'];
        $_SESSION['nom_medecin_choisi'] = ($medInfo['nom'] ?? '') . ' ' . ($medInfo['prenom'] ?? ''); 
        header("Location: index.php?page=dashbord");
        exit;
    }
}

if (!isset($_SESSION['user']) && $page !== 'login' && $page !== 'process_login') {
    header("Location: index.php?page=login");
    exit;
}

$pages_bloquees = ['dashbord', 'presencePatient'];
if (isset($_SESSION['user']) && !isset($_SESSION['id_medecin_choisi']) && in_array($page, $pages_bloquees)) {
    header("Location: index.php?page=choix_medecin");
    exit;
}

if ($page === 'choix_medecin') {
    $medecinModel = new MedecinModel($pdo);
    $id_spec_infirmier = $_SESSION['user']['id_specialite'] ?? null;
    $medecins = $id_spec_infirmier ? $medecinModel->getMedecinsBySpecialite($id_spec_infirmier) : [];
}
elseif ($page === 'dashbord') {
    require_once __DIR__ . '/../app/controllers/TicketController.php'; 
    $rdvModel = new RendezVousModel($pdo);
    $medecinModel = new MedecinModel($pdo);
    $ticketCtrl = new TicketController($pdo); 
    $id_med_actif = $_SESSION['id_medecin_choisi'];

    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'update_doc_status') {
            $id_med_to_update = $_GET['id'] ?? $id_med_actif;
            $nouveau_statut = $_GET['status'];
            $medecinModel->updateStatus($id_med_to_update, $nouveau_statut);
            header("Location: index.php?page=dashbord");
            exit;
        }
        if ($_GET['action'] === 'call_next') {
            $ticketCtrl->appelerSuivant($id_med_actif); 
            header("Location: index.php?page=dashbord");
            exit;
        }
    }

    $total_patients = $rdvModel->countToday($id_med_actif);
    $total_presents = $rdvModel->countPresentsCumule($id_med_actif);
    $total_absents = $rdvModel->countByStatus('Absent', $id_med_actif);
    $medecin = $medecinModel->getMedecinById($id_med_actif);
    $patientActuel = $ticketCtrl->afficherTicketActuel($id_med_actif); 
    $prochainsPatients = $rdvModel->getProchainsEnAttente($id_med_actif, 3); 
}
elseif ($page === 'presencePatient') {
    require_once __DIR__ . '/../app/controllers/RendezVousController.php';
    $controller = new RendezVousController($pdo);
    $id_med_actif = $_SESSION['id_medecin_choisi'];

    if (isset($_GET['action'])) {
        $id_rdv = $_GET['id'];
        $type = $_GET['action'];
        if ($type === 'status') {
            $controller->gererActionPresence($id_rdv, 'update_statut', $_GET['valeur']);
        } elseif ($type === 'move_last') {
            $controller->gererActionPresence($id_rdv, 'update_statut', 'Retard');
        }
        header("Location: index.php?page=presencePatient");
        exit;
    }
    $rdvs = $controller->getRendezVousData($id_med_actif);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Santé Pro</title>
    <link rel="stylesheet" href="css/style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="<?php echo ($page === 'login') ? 'login-bg' : 'dashboard-body'; ?>">

<?php if ($page === 'login' || $page === 'process_login'): ?>
    <?php include __DIR__ . ($page === 'login' ? '/../app/views/infirmier/login.php' : '/../app/controllers/process_login.php'); ?>
<?php else: ?>
    <div class="main-layout">
        <aside class="sidebar"><?php include __DIR__ . '/../app/views/layouts/sidebar.php'; ?></aside>
        <main class="main-content">
            <?php 
            $viewPath = __DIR__ . '/../app/views/infirmier/' . $page . '.php';
            if ($page === 'logout') include __DIR__ . '/../app/controllers/logout.php';
            elseif (file_exists($viewPath)) include $viewPath;
            else echo "<div class='error-msg'><h2>Erreur 404</h2><p>La page [ $page ] est introuvable</p></div>";
            ?>
        </main>
    </div>
<?php endif; ?>
</body>
</html>

<?php
class DashboardController {
    private $rdvModel;
    private $medecinModel;
    private $ticketModel;
    private $ticketCtrl;

    // FUSION : Un seul constructeur avec tous les arguments et la vérification de session
    public function __construct($rdvModel, $medecinModel, $ticketModel, $ticketCtrl) {
        // 1. Vérification de sécurité immédiate
        if (!isset($_SESSION['id_medecin_choisi'])) {
            header("Location: index.php?page=choix_medecin");
            exit();
        }

        // 2. Initialisation des modèles
        $this->rdvModel = $rdvModel;
        $this->medecinModel = $medecinModel;
        $this->ticketModel = $ticketModel;
        $this->ticketCtrl = $ticketCtrl;
    }

    public function index() {
        // L'ID est garanti d'exister grâce au constructeur ci-dessus
        $id_medecin = $_SESSION['id_medecin_choisi'];

        // 2. Gérer les actions (Changement de statut)
        if (isset($_GET['action']) && $_GET['action'] === 'update_doc_status') {
            $this->updateDocStatus();
        }

        if (isset($_GET['action']) && $_GET['action'] === 'call_next') {
            $this->callNextPatient();
        }

        // 3. Récupérer les statistiques pour les cartes
        $total_patients = $this->rdvModel->countToday($id_medecin);
        $total_presents = $this->rdvModel->countPresentsCumule($id_medecin);
        $total_absents = $this->rdvModel->countByStatus('Absent', $id_medecin);

        // 4. Récupérer les infos du médecin
        $medecin = $this->medecinModel->getMedecinById($id_medecin);

        // 5. Récupérer les données des tickets
        $patientActuel = $this->ticketCtrl->afficherTicketActuel($id_medecin);
        $prochainsPatients = $this->rdvModel->getProchainsEnAttente($id_medecin, 3);

        // --- 6. Préparer les variables pour la vue ---
        $ticketCtrl = $this->ticketCtrl;
        $id_med_actif = $id_medecin;
        $pageTitle = "Tableau de Bord | Santé Pro";
        $pageCSS = "css/style_infirmier.css"; 
        
       // On remonte de InfirmierController (1) puis de controllers (2) pour atteindre APP/views
require_once __DIR__ . '/../../views/Infirmier/dashbord.php';
    }

    private function updateDocStatus() {
        $id_medecin_param = $_GET['id'] ?? $_SESSION['id_medecin_choisi'];
        $status = $_GET['status'] ?? null;
        
        if (!$id_medecin_param || !$status) {
            die("Erreur: Paramètres manquants");
        }
        
        $status = trim($status);
        $allowed = ['Présent', 'Absent'];
        if (!in_array($status, $allowed, true)) {
            die("Erreur: statut invalide");
        }
        
        $result = $this->medecinModel->updateStatus($id_medecin_param, $status);
        
        header('Location: index.php?page=dashbord');
        exit();
    }

    private function callNextPatient() {
        $id_medecin = $_SESSION['id_medecin_choisi'];
        $this->ticketCtrl->appelerSuivant($id_medecin);
        header('Location: index.php?page=dashbord');
        exit();
    }
}
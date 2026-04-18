
<?php
class DashboardController {
    private $rdvModel;
    private $medecinModel;

    public function __construct($rdvModel, $medecinModel) {
        $this->rdvModel = $rdvModel;
        $this->medecinModel = $medecinModel;
    }

    public function index() {
        // 1. Récupérer l'ID du médecin (par exemple le 20 qu'on a vu dans ta BDD)
        // Dans un vrai projet, on le récupère via la session : $_SESSION['id_medecin']
        $id_medecin = 20; 

        // 2. Gérer les actions (Changement de statut)
        if (isset($_GET['action']) && $_GET['action'] === 'update_doc_status') {
            $id_medecin_param = $_GET['id'];
            $status = $_GET['status'];
            $this->medecinModel->updateStatus($id_medecin_param, $status);
            header('Location: index.php?page=dashboard');
            exit();
        }

        // 3. Récupérer les statistiques pour les cartes
        // On suppose que tu as ces méthodes dans ton RendezVousModel
        $total_patients = $this->rdvModel->countToday($id_medecin);
        $total_presents = $this->rdvModel->countByStatus('Présent', $id_medecin);
        $total_absents = $this->rdvModel->countByStatus('Absent', $id_medecin);

        // 4. Récupérer les infos du médecin
        $medecin = $this->medecinModel->getMedecinById($id_medecin);

        // 5. Charger la vue
        require_once 'app/views/infirmier/dashbord.php';
    }

    public function index_consultation() { // Note: Changé le nom car deux index() créent une erreur PHP
        $id_medecin = 20; 

        // Actions (Appel suivant)
        if (isset($_GET['action']) && $_GET['action'] === 'call_next') {
            $this->rdvModel->callNextPatient($id_medecin);
            header('Location: index.php?page=dashbord');
            exit();
        }

        // 1. Récupérer le patient "En consultation" chez ce médecin
        $patientActuel = $this->rdvModel->getPatientEnConsultation($id_medecin);

        // 2. Récupérer les 3 PROCHAINS patients (Statut 'Présent')
        // Modifiez votre modèle pour ajouter "LIMIT 3" dans la requête
        $prochainsPatients = $this->rdvModel->getProchainsEnAttente($id_medecin, 3);
    }
}
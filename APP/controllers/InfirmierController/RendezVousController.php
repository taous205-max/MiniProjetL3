<?php

class RendezVousController {
    private $model;

    public function __construct($rendezVousModel) {
        $this->model = $rendezVousModel;
    }

    public function index() {
        $id_med_actif = isset($_SESSION['id_medecin_choisi']) ? (int)$_SESSION['id_medecin_choisi'] : null;

        if (!$id_med_actif) {
            header('Location: index.php?page=choix_medecin');
            exit();
        }

        // Gestion du changement de statut
        if (isset($_GET['action']) && $_GET['action'] === 'status') {
            if (isset($_GET['id']) && isset($_GET['valeur'])) {
                $id_rdv = (int)$_GET['id'];
                $nouveau_statut = $_GET['valeur']; 

                // Appel de la méthode interne
                $this->gererActionPresence($id_rdv, 'update_statut', $nouveau_statut);
                
                header('Location: index.php?page=presencePatient');
                exit();
            }
        }

        $rdvs = $this->model->getTodayRendezVous($id_med_actif);
        $pageCSS = 'css/style_infirmier.css'; 

        $viewPath = __DIR__ . '/../../views/Infirmier/presencePatient.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("Erreur : La vue 'presencePatient.php' est introuvable.");
        }
    }

    // Cette méthode doit impérativement être AVANT la dernière accolade de la classe
    public function gererActionPresence($id_rdv, $actionType, $valeur = null) {
        if ($actionType === 'update_statut' && $valeur !== null) {
            return $this->model->updateStatut($id_rdv, $valeur);
        }
        return false;
    }

} // FIN DE LA CLASSE
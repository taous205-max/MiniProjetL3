<?php
class RendezVousController {
    private $model;
    
    public function __construct($pdo) {
        require_once __DIR__ . '/../models/RendezVousModel.php';
        $this->model = new RendezVousModel($pdo);
    }
    public function getRendezVousData($id_medecin) {
        // Utilise id_medecin pour filtrer les résultats dans le modèle
        return $this->model->getTodayRendezVous($id_medecin);
    }
    public function gererActionPresence($id_rdv, $actionType, $valeur = null) {
        if ($actionType === 'update_statut') {
            // $id_rdv reste id_rdv (clé primaire du rendez-vous)
            return $this->model->updateStatut($id_rdv, $valeur);
        }
    }
}
<?php
class RendezVousModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getTodayRendezVous($id_medecin) {
        $sql = "SELECT r.id_rdv, t.code_ticket, up.nom, up.prenom, r.periode, r.statut, 
                       um.nom as nom_medecin, s.nom_specialite
                FROM rendez_vous r
                JOIN ticket t ON r.id_rdv = t.id_rdv
                /* On lie le patient via id_patient */
                JOIN utilisateur up ON r.id_patient = up.id
                /* On lie le médecin via id_medecin */
                JOIN medecin m ON r.id_medecin = m.id_medecin
                JOIN utilisateur um ON m.id_medecin = um.id
                JOIN specialite s ON m.id_specialite = s.id_specialite
                WHERE r.date = CURDATE() AND r.id_medecin = :id_med
                ORDER BY CASE WHEN r.statut = 'Retard' THEN 1 ELSE 0 END ASC, t.code_ticket ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_med' => $id_medecin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatut($id_rdv, $nouveau_statut) {
        $sql = "UPDATE rendez_vous SET statut = :statut WHERE id_rdv = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['statut' => $nouveau_statut, 'id' => $id_rdv]);
    }

    public function countToday($id_medecin) {
        $sql = "SELECT COUNT(*) FROM rendez_vous WHERE date = CURDATE() AND id_medecin = :id_med";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_med' => $id_medecin]);
        return $stmt->fetchColumn();
    }

    public function countPresentsCumule($id_medecin) {
        $sql = "SELECT COUNT(*) FROM rendez_vous WHERE date = CURDATE() AND id_medecin = :id_med 
                AND statut IN ('Présent', 'Chez le médecin', 'Terminé')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_med' => $id_medecin]);
        return $stmt->fetchColumn();
    }

    public function countByStatus($status, $id_medecin) {
        $sql = "SELECT COUNT(*) FROM rendez_vous WHERE date = CURDATE() AND statut = :statut AND id_medecin = :id_med";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['statut' => $status, 'id_med' => $id_medecin]);
        return $stmt->fetchColumn();
    }

    /**
     * Récupère les 3 prochains patients présents
     */
    public function getProchainsEnAttente($id_medecin, $limit = 3) {
        $sql = "SELECT t.code_ticket, u.nom, u.prenom 
                FROM rendez_vous r 
                JOIN ticket t ON r.id_rdv = t.id_rdv 
                JOIN utilisateur u ON r.id_patient = u.id 
                WHERE r.id_medecin = :id 
                AND r.statut = 'Présent' 
                AND r.date = CURDATE()
                ORDER BY r.id_rdv ASC 
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql); 
        $stmt->bindValue(':id', $id_medecin, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
<div class="dashboard-container">
    <h2 class="dashboard-title">Tableau de bord infirmier</h2>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <span class="stat-label">Patients aujourd'hui</span>
                <strong class="stat-number"><?php echo $total_patients ?? 0; ?></strong>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <span class="stat-label">Présents</span>
                <strong class="stat-number"><?php echo $total_presents ?? 0; ?></strong>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-red"><i class="fas fa-times-circle"></i></div>
            <div class="stat-info">
                <span class="stat-label">Absents</span>
                <strong class="stat-number"><?php echo $total_absents ?? 0; ?></strong>
            </div>
        </div>
    </div>

    <div class="main-grid">
        <div class="list-card">
            <div class="pilotage-header">
                <h3>Gestion de la file d'attente</h3>
                <span class="live-badge"><i class="fas fa-circle"></i> EN DIRECT</span>
            </div>

            <div class="pilotage-content">
                <div class="focus-section">
                    <div class="focus-label">EN CONSULTATION</div>
                    <?php 
                        // Le contrôleur renvoie des données uniquement si statut = 'Chez le médecin'
                        if (!empty($patientActuel)): 
                    ?>
                        <div class="focus-patient-name" style="font-size: 2.8rem; font-weight: 800; color: #00b4d8; margin-top: 20px; text-align: center;">
                            <?php 
                                $nom = $patientActuel['nom'] ?? '';
                                $prenom = $patientActuel['prenom'] ?? '';
                                echo htmlspecialchars(strtoupper(trim($nom . ' ' . $prenom))); 
                            ?>
                        </div>
                    <?php else: ?>
                        <div class="focus-empty" style="text-align: center; color: #94a3b8; padding: 40px 0;">
                            <i class="fas fa-user-md" style="font-size: 3rem; margin-bottom: 10px; display: block;"></i>
                            <p>En attente du médecin...</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="pilotage-divider"></div>

                <div class="next-section">
                    <div class="next-label">Prochains à traiter (Top 3)</div>
                    <div class="next-list">
                        <?php if (!empty($prochainsPatients)): ?>
                            <?php 
                            $top3 = array_slice($prochainsPatients, 0, 3);
                            foreach ($top3 as $p): 
                            ?>
                                <div class="next-row" style="display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #f1f5f9;">
                                    <span class="next-tkt" style="font-weight: 700; color: #00b4d8;"><?php echo htmlspecialchars($p['code_ticket']); ?></span>
                                    <span class="next-name" style="color: #475569;"><?php echo htmlspecialchars($p['nom'] ?? 'Sans nom'); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-next" style="padding: 20px; text-align: center; color: #cbd5e1;">
                                <p>File d'attente vide</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="side-stack">
            <div class="status-card">
                <h3>Présence médecin</h3>
                <p class="doc-name" style="font-weight: 700; margin-bottom: 10px;">
                    Dr. <?php echo htmlspecialchars($medecin['nom'] ?? '...'); ?>
                </p>
                <div class="btn-group">
                    <button class="btn btn-present <?php echo (($medecin['status'] ?? '') === 'Présent') ? 'active' : ''; ?>" 
                            onclick="changerStatusMedecin(<?php echo $id_med_actif; ?>, 'Présent')">
                        Présent
                    </button>
                    <button class="btn btn-absent <?php echo (($medecin['status'] ?? '') === 'Absent') ? 'active' : ''; ?>" 
                            onclick="changerStatusMedecin(<?php echo $id_med_actif; ?>, 'Absent')">
                        Absent
                    </button>
                </div>
            </div>

            <div class="ticket-card">
                <h3>Ticket actuel</h3> 
                <div class="ticket-display" style="background: #f8fafc; border-radius: 10px; padding: 20px; margin: 15px 0; text-align: center;">
                    <span class="ticket-number" style="font-size: 3.5rem; font-weight: 900; color: #1e293b;">
                        <?php 
                            // Correction : On utilise la fonction qui récupère le numéro 
                            // même si le patient est encore en statut 'Présent'
                            echo htmlspecialchars($ticketCtrl->afficherNumeroSeul($id_med_actif)); 
                        ?>
                    </span>
                </div>
                <p class="ticket-hint" style="font-size: 0.9rem; color: #64748b; text-align: center; margin-bottom: 20px;">
                    <?php echo !empty($patientActuel) ? "Patient en consultation..." : "Appelez le patient suivant"; ?>
                </p>
                
                <button class="btn btn-call" onclick="appelerSuivant()" style="width: 100%; padding: 15px; font-weight: 700; text-transform: uppercase;">
                    <i class="fas fa-bullhorn" style="margin-right: 8px;"></i> APPELER SUIVANT
                </button>
            </div>
        </div>
    </div>
</div>

<script src="public/js/infirmierJS/dashbord.js"></script>

<div class="dashboard-container">
    <div class="header-section">
        <div class="section-header">
            <h5>Suivi en temps réel</h5>
            <h2>Gestion de la présence des patients</h2>
        </div>
        <div class="search-box">
             <i class="fas fa-search"></i>
             <input type="text" id="searchInput" placeholder="Rechercher un patient...">
        </div>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Ticket</th>
                    <th>Patient</th>
                    <th>Période</th>
                    <th>Médecin</th>
                    <th>Statut</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rdvs)): foreach ($rdvs as $rdv): ?>
                <tr>
                    <td><span class="ticket-badge"><?php echo $rdv['code_ticket']; ?></span></td>
                    <td><span class="patient-name"><?php echo htmlspecialchars($rdv['nom'] . ' ' . $rdv['prenom']); ?></span></td>
                    <td>
                        <?php $p = $rdv['periode'] ?? 'Matin'; ?>
                        <span class="period-tag <?php echo (trim($p) === 'Matin') ? 'morning' : 'afternoon'; ?>">
                            <?php echo $p; ?>
                        </span>
                    </td>
                    <td>Dr. <?php echo htmlspecialchars($rdv['nom_medecin'] ?? 'Inconnu'); ?></td>
                    <td>
                        <?php 
                            $s = $rdv['statut'];
                            $class = ($s == 'Présent') ? 'present' : (($s == 'Absent' || $s == 'Retard') ? 'absent' : 'waiting');
                        ?>
                        <span class="status-label <?php echo $class; ?>"><?php echo $s; ?></span>
                    </td>
                    <td class="text-center">
                        <button class="btn-action btn-present-sm" onclick="executerAction(<?php echo $rdv['id_rdv']; ?>, 'status', 'Présent')"><i class="fas fa-check"></i></button>
                        <button class="btn-action btn-absent-sm" onclick="executerAction(<?php echo $rdv['id_rdv']; ?>, 'status', 'Absent')"><i class="fas fa-times"></i></button>
                        <button class="btn-action btn-reorder-sm" onclick="executerAction(<?php echo $rdv['id_rdv']; ?>, 'move_last')">Fin</button>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="6" class="text-center">Aucun patient trouvé.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function executerAction(id, type, valeur = '') {
    if (confirm("Confirmer l'action ?")) {
        // L'id passé ici est l'id_rdv
        window.location.href = `index.php?page=presencePatient&action=${type}&id=${id}&valeur=${valeur}`;
    }
}
// Filtrage simple
document.getElementById('searchInput').addEventListener('keyup', function() {
    let f = this.value.toLowerCase();
    document.querySelectorAll('.table tbody tr').forEach(row => {
        let n = row.querySelector('.patient-name').textContent.toLowerCase();
        row.style.display = n.includes(f) ? "" : "none";
    });
});
</script>
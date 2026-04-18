<div class="selection-wrapper">
    <div class="selection-header-clean">
        <h1>Sélection du Service</h1>
        <p>Veuillez choisir le médecin pour la session actuelle</p>
    </div>

    <div class="doctor-cards-container">
        <?php foreach ($medecins as $med): ?>
            <div class="mini-doctor-card">
                <div class="card-top">
                    <div class="doc-avatar">
                        <i class="fas fa-user-md"></i>
                    </div>
                </div>
                <div class="card-body">
                    <h3>Dr. <?php echo htmlspecialchars($med['nom'] . ' ' . $med['prenom']); ?></h3>
                    <span class="specialty-tag"><?php echo htmlspecialchars($med['nom_specialite']); ?></span>
                </div>
                <div class="card-footer">
                    <a href="index.php?action=select_doctor&id=<?php echo $med['id_medecin']; ?>" class="btn-select">
                        Choisir <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php
$current_page = $_GET['page'] ?? 'dashbord';
// On vérifie si un médecin a été sélectionné en session
$is_med_chosen = isset($_SESSION['id_medecin_choisi']);
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-heartbeat"></i>
        <span>SANTÉ PRO</span>
    </div>

    <nav class="menu">
        <a href="index.php?page=choix_medecin"
           class="nav-link <?php echo ($current_page == 'choix_medecin') ? 'active' : ''; ?>">
            <i class="fas fa-user-md"></i>
            <span>Choix du médecin</span>
        </a>

        <a href="<?php echo $is_med_chosen ? 'index.php?page=dashbord' : '#'; ?>"
           class="nav-link <?php echo ($current_page == 'dashbord') ? 'active' : ''; ?> <?php echo !$is_med_chosen ? 'disabled-link' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Tableau de bord</span>
        </a>

        <a href="<?php echo $is_med_chosen ? 'index.php?page=presencePatient' : '#'; ?>"
           class="nav-link <?php echo ($current_page == 'presencePatient') ? 'active' : ''; ?> <?php echo !$is_med_chosen ? 'disabled-link' : ''; ?>">
            <i class="fas fa-user-check"></i>
            <span>Présence patient</span>
        </a>
    </nav>

    <div class="logout">
        <a href="index.php?page=logout" class="nav-link logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Déconnexion</span>
        </a>
    </div>
</aside>
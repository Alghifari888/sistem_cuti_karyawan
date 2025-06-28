<?php
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}

$currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!-- Panggil file CSS eksternal -->
<link rel="stylesheet" href="layout/sidebar.css">



<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="fade-slide" style="animation-delay: 0.2s;">
            <h6 class="sidebar-heading">Navigasi</h6>
            <ul class="nav flex-column">
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <li class="nav-item fade-slide" style="animation-delay: 0.3s;">
                        <a class="nav-link <?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>" href="index.php?page=dashboard">
                            <i class="bi bi-house-door-fill"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item fade-slide" style="animation-delay: 0.4s;">
                        <a class="nav-link <?php echo in_array($currentPage, ['user_list', 'user_tambah', 'user_edit']) ? 'active' : ''; ?>" href="index.php?page=user_list">
                            <i class="bi bi-people"></i> Data Karyawan
                        </a>
                    </li>
                    <li class="nav-item fade-slide" style="animation-delay: 0.5s;">
                        <a class="nav-link <?php echo in_array($currentPage, ['cuti_semua', 'cuti_verifikasi']) ? 'active' : ''; ?>" href="index.php?page=cuti_semua">
                            <i class="bi bi-calendar2-event-fill"></i> Manajemen Cuti
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item fade-slide" style="animation-delay: 0.3s;">
                        <a class="nav-link <?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>" href="index.php?page=dashboard">
                            <i class="bi bi-house-door-fill"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item fade-slide" style="animation-delay: 0.4s;">
                        <a class="nav-link <?php echo ($currentPage == 'cuti_ajukan') ? 'active' : ''; ?>" href="index.php?page=cuti_ajukan">
                            <i class="bi bi-send-fill"></i> Ajukan Cuti
                        </a>
                    </li>
                    <li class="nav-item fade-slide" style="animation-delay: 0.5s;">
                        <a class="nav-link <?php echo ($currentPage == 'cuti_riwayat') ? 'active' : ''; ?>" href="index.php?page=cuti_riwayat">
                            <i class="bi bi-clock-history"></i> Riwayat Cuti
                        </a>
                    </li>
                    <li class="nav-item fade-slide" style="animation-delay: 0.6s;">
                        <a class="nav-link <?php echo ($currentPage == 'profil') ? 'active' : ''; ?>" href="index.php?page=profil">
                            <i class="bi bi-person-badge"></i> Profil Saya
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const fadeEls = document.querySelectorAll('.fade-slide');
    fadeEls.forEach((el, i) => {
        el.style.animationDelay = (i * 0.1) + "s";
        el.classList.add("fade-slide");
    });
});
</script>

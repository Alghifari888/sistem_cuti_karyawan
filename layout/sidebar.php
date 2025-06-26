<?php
// Pastikan tidak ada yang bisa mengakses file ini secara langsung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}

// Ambil halaman saat ini dari URL untuk menandai menu aktif
$currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Selamat Datang</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-person-fill"></i>
                    <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>
                </a>
            </li>
             <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-award-fill"></i>
                    Role: <?php echo ucfirst(htmlspecialchars($_SESSION['role'])); ?>
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Menu</span>
        </h6>
        <ul class="nav flex-column">
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <!-- Menu untuk Admin -->
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>" href="index.php?page=dashboard">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($currentPage, ['user_list', 'user_tambah', 'user_edit']) ? 'active' : ''; ?>" href="index.php?page=user_list">
                        <i class="bi bi-people-fill"></i> Data Karyawan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($currentPage, ['cuti_semua', 'cuti_verifikasi']) ? 'active' : ''; ?>" href="index.php?page=cuti_semua">
                        <i class="bi bi-calendar2-check-fill"></i> Data Cuti
                    </a>
                </li>
            <?php else: ?>
                <!-- Menu untuk User -->
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>" href="index.php?page=dashboard">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'cuti_ajukan') ? 'active' : ''; ?>" href="index.php?page=cuti_ajukan">
                        <i class="bi bi-send-plus-fill"></i> Ajukan Cuti
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'cuti_riwayat') ? 'active' : ''; ?>" href="index.php?page=cuti_riwayat">
                        <i class="bi bi-clock-history"></i> Riwayat Cuti
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'profil') ? 'active' : ''; ?>" href="index.php?page=profil">
                       <i class="bi bi-person-badge-fill"></i> Profil Saya
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

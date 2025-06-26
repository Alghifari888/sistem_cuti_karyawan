<?php
// index.php

// 1. Mulai Session (Harus paling atas)
session_start();

// 2. Hubungkan ke Database
require_once 'config/db.php';

// 3. Cek Login
// Jika tidak ada session user_id, paksa kembali ke halaman login.
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// 4. Ambil data penting dari Session
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// =================================================================
// STRUKTUR HALAMAN YANG BENAR (HANYA PANGGIL SEKALI-SEKALI)
// =================================================================

// 5. Panggil Header (HANYA SATU KALI)
include 'layout/header.php';

// 6. Panggil Sidebar (HANYA SATU KALI)
include 'layout/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <?php
            // Logika untuk menentukan judul halaman
            $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
            switch ($page) {
                case 'dashboard': echo 'Dashboard'; break;
                case 'user_list': echo 'Data Karyawan'; break;
                case 'user_tambah': echo 'Tambah Karyawan'; break;
                case 'user_edit': echo 'Edit Karyawan'; break;
                case 'cuti_semua': echo 'Semua Data Cuti'; break;
                case 'cuti_verifikasi': echo 'Verifikasi Pengajuan Cuti'; break;
                case 'cuti_ajukan': echo 'Formulir Pengajuan Cuti'; break;
                case 'cuti_riwayat': echo 'Riwayat Pengajuan Cuti'; break;
                case 'profil': echo 'Profil Saya'; break;
                default: echo 'Halaman Tidak Dikenali'; break;
            }
            ?>
        </h1>
    </div>

    <?php
    // 8. Logika untuk memuat file konten halaman (Routing)
    $allowed_admin_pages = ['dashboard', 'user_list', 'user_tambah', 'user_edit', 'cuti_semua', 'cuti_verifikasi'];
    $allowed_user_pages = ['dashboard', 'cuti_ajukan', 'cuti_riwayat', 'profil'];

    $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

    $is_allowed = false;
    if ($role == 'admin' && in_array($page, $allowed_admin_pages)) {
        $is_allowed = true;
    } elseif ($role == 'user' && in_array($page, $allowed_user_pages)) {
        $is_allowed = true;
    }

    if ($is_allowed) {
        if ($page == 'dashboard') {
            if ($role == 'admin') {
                include 'pages/dashboard_admin.php';
            } else {
                include 'pages/dashboard_user.php';
            }
        } else {
            $page_file = "pages/{$page}.php";
            if (file_exists($page_file)) {
                include $page_file;
            } else {
                echo "<div class='alert alert-danger'>Halaman <strong>'{$page}.php'</strong> tidak ditemukan di dalam folder 'pages'.</div>";
            }
        }
    } else {
        echo "<div class='alert alert-warning'>Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.</div>";
    }
    ?>
</main>
<?php
// 9. Panggil Footer (HANYA SATU KALI)
include 'layout/footer.php';
?>

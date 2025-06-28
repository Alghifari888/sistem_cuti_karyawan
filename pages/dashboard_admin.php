<?php
// Proteksi akses langsung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}

// Cek session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_login = $_SESSION['user_id'];
$query_user = "SELECT nama_lengkap, jabatan FROM users WHERE id = $id_login";
$result_user = mysqli_query($koneksi, $query_user);
$data_user = mysqli_fetch_assoc($result_user);
$nama_login = $data_user['nama_lengkap'];
$jabatan_login = $data_user['jabatan'];

// Query data
$query_total_karyawan = "SELECT COUNT(id) as total FROM users WHERE role = 'user'";
$result_total_karyawan = mysqli_query($koneksi, $query_total_karyawan);
$total_karyawan = mysqli_fetch_assoc($result_total_karyawan)['total'];

$bulan_ini = date('m');
$tahun_ini = date('Y');
$query_cuti_bulan_ini = "SELECT COUNT(id) as total FROM pengajuan_cuti WHERE MONTH(tanggal_pengajuan) = '$bulan_ini' AND YEAR(tanggal_pengajuan) = '$tahun_ini'";
$result_cuti_bulan_ini = mysqli_query($koneksi, $query_cuti_bulan_ini);
$cuti_bulan_ini = mysqli_fetch_assoc($result_cuti_bulan_ini)['total'];

$query_cuti_disetujui = "SELECT COUNT(id) as total FROM pengajuan_cuti WHERE status = 'Disetujui'";
$result_cuti_disetujui = mysqli_query($koneksi, $query_cuti_disetujui);
$cuti_disetujui = mysqli_fetch_assoc($result_cuti_disetujui)['total'];

$query_cuti_diajukan = "SELECT COUNT(id) as total FROM pengajuan_cuti WHERE status = 'Diajukan'";
$result_cuti_diajukan = mysqli_query($koneksi, $query_cuti_diajukan);
$cuti_diajukan = mysqli_fetch_assoc($result_cuti_diajukan)['total'];

$query_recent_cuti = "
    SELECT pc.id, u.nama_lengkap, pc.tanggal_mulai, pc.tanggal_selesai, pc.status 
    FROM pengajuan_cuti pc 
    JOIN users u ON pc.user_id = u.id 
    ORDER BY pc.tanggal_pengajuan DESC 
    LIMIT 5";
$result_recent_cuti = mysqli_query($koneksi, $query_recent_cuti);
?>

<!-- Tambahkan Link CSS -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="pages/css/dashboard.css">

<!-- Heading dan identitas -->
<div class="mb-4">
    <h2 class="fw-bold fade-slide" style="animation-delay: 0.2s;">Dashboard</h2>
    <h5 class="fw-semibold text-secondary fade-slide" style="animation-delay: 0.4s;">
        Selamat datang, <?= htmlspecialchars($nama_login); ?>
        <span class="text-muted">— <?= htmlspecialchars($jabatan_login); ?></span>
    </h5>
</div>

<!-- Kartu Statistik -->
<div class="row g-4 mb-4">
    <?php
    $cards = [
        ['title' => 'Total Karyawan', 'value' => $total_karyawan, 'icon' => 'bi-people-fill'],
        ['title' => 'Pengajuan Bulan Ini', 'value' => $cuti_bulan_ini, 'icon' => 'bi-calendar-plus-fill'],
        ['title' => 'Cuti Disetujui', 'value' => $cuti_disetujui, 'icon' => 'bi-check-circle-fill'],
        ['title' => 'Menunggu Konfirmasi', 'value' => $cuti_diajukan, 'icon' => 'bi-hourglass-split'],
    ];
    foreach ($cards as $card) {
        echo '<div class="col-12 col-sm-6 col-lg-3">
            <div class="card card-gold">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-2">'.$card['title'].'</h5>
                        <h3 class="mb-0">'.$card['value'].'</h3>
                    </div>
                    <div class="avatar">
                        <div class="avatar-title">
                            <i class="bi '.$card['icon'].'"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }
    ?>
</div>

<!-- Tabel Pengajuan Cuti Terbaru -->
<div class="card">
    <div class="card-header bg-white border-0">
        <h5 class="card-title mb-0">Pengajuan Cuti Terbaru</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result_recent_cuti) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while($row = mysqli_fetch_assoc($result_recent_cuti)): ?>
                            <tr class="table-row-animate">
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['tanggal_mulai'])); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['tanggal_selesai'])); ?></td>
                                <td>
                                    <?php
                                    $status = $row['status'];
                                    $badge_class = ($status == 'Disetujui') ? 'bg-success' : (($status == 'Ditolak') ? 'bg-danger' : 'bg-warning');
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">Belum ada pengajuan cuti.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Script animasi -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('animate');
        }, index * 150);
    });

    const rows = document.querySelectorAll('.table-row-animate');
    rows.forEach((row, i) => {
        setTimeout(() => {
            row.style.opacity = '1';
            row.style.transform = 'scale(1)';
        }, i * 100);
    });

    const fadeSlideEls = document.querySelectorAll('.fade-slide');
    fadeSlideEls.forEach((el, index) => {
        setTimeout(() => {
            el.style.animationDelay = (index * 0.2) + "s";
            el.classList.add('fade-slide');
        }, index * 200);
    });
});
</script>

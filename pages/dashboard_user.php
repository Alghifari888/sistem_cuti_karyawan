<?php
// pages/dashboard_user.php

// Pastikan tidak ada yang bisa mengakses file ini secara langsung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}

$user_id = $_SESSION['user_id'];

// Query untuk mengambil statistik cuti user yang sedang login
// 1. Total Cuti Diajukan
$query_total = "SELECT COUNT(id) as total FROM pengajuan_cuti WHERE user_id = ?";
$stmt_total = mysqli_prepare($koneksi, $query_total);
mysqli_stmt_bind_param($stmt_total, 'i', $user_id);
mysqli_stmt_execute($stmt_total);
$result_total = mysqli_stmt_get_result($stmt_total);
$total_cuti = mysqli_fetch_assoc($result_total)['total'];

// 2. Cuti Disetujui
$query_disetujui = "SELECT COUNT(id) as total FROM pengajuan_cuti WHERE user_id = ? AND status = 'Disetujui'";
$stmt_disetujui = mysqli_prepare($koneksi, $query_disetujui);
mysqli_stmt_bind_param($stmt_disetujui, 'i', $user_id);
mysqli_stmt_execute($stmt_disetujui);
$result_disetujui = mysqli_stmt_get_result($stmt_disetujui);
$cuti_disetujui = mysqli_fetch_assoc($result_disetujui)['total'];

// 3. Cuti Ditolak
$query_ditolak = "SELECT COUNT(id) as total FROM pengajuan_cuti WHERE user_id = ? AND status = 'Ditolak'";
$stmt_ditolak = mysqli_prepare($koneksi, $query_ditolak);
mysqli_stmt_bind_param($stmt_ditolak, 'i', $user_id);
mysqli_stmt_execute($stmt_ditolak);
$result_ditolak = mysqli_stmt_get_result($stmt_ditolak);
$cuti_ditolak = mysqli_fetch_assoc($result_ditolak)['total'];

// 4. Cuti Menunggu Konfirmasi
$query_diajukan = "SELECT COUNT(id) as total FROM pengajuan_cuti WHERE user_id = ? AND status = 'Diajukan'";
$stmt_diajukan = mysqli_prepare($koneksi, $query_diajukan);
mysqli_stmt_bind_param($stmt_diajukan, 'i', $user_id);
mysqli_stmt_execute($stmt_diajukan);
$result_diajukan = mysqli_stmt_get_result($stmt_diajukan);
$cuti_diajukan = mysqli_fetch_assoc($result_diajukan)['total'];

// Query untuk mengambil 5 riwayat pengajuan cuti terbaru milik user
$query_recent_cuti = "
    SELECT tanggal_mulai, tanggal_selesai, alasan, status, tanggal_pengajuan
    FROM pengajuan_cuti 
    WHERE user_id = ?
    ORDER BY tanggal_pengajuan DESC 
    LIMIT 5";
$stmt_recent = mysqli_prepare($koneksi, $query_recent_cuti);
mysqli_stmt_bind_param($stmt_recent, 'i', $user_id);
mysqli_stmt_execute($stmt_recent);
$result_recent_cuti = mysqli_stmt_get_result($stmt_recent);
?>

<div class="alert alert-info" role="alert">
    <h4 class="alert-heading">Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>!</h4>
    <p>Ini adalah halaman dashboard Anda. Di sini Anda dapat melihat ringkasan pengajuan cuti Anda dan mengajukan cuti baru dengan mudah.</p>
    <hr>
    <p class="mb-0">Gunakan menu di samping untuk navigasi. Untuk mengajukan cuti, silakan klik menu "Ajukan Cuti".</p>
</div>

<div class="row g-4 mb-4">
    <!-- Card Total Cuti -->
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-2">Total Pengajuan</h5>
                        <h3 class="mb-0"><?php echo $total_cuti; ?></h3>
                    </div>
                    <div class="avatar">
                        <div class="avatar-title rounded-circle bg-primary text-white fs-2">
                            <i class="bi bi-journal-album"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card Disetujui -->
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-2">Disetujui</h5>
                        <h3 class="mb-0"><?php echo $cuti_disetujui; ?></h3>
                    </div>
                    <div class="avatar">
                        <div class="avatar-title rounded-circle bg-success text-white fs-2">
                             <i class="bi bi-check-circle-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card Ditolak -->
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-2">Ditolak</h5>
                        <h3 class="mb-0"><?php echo $cuti_ditolak; ?></h3>
                    </div>
                    <div class="avatar">
                        <div class="avatar-title rounded-circle bg-danger text-white fs-2">
                           <i class="bi bi-x-circle-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card Menunggu -->
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-2">Menunggu</h5>
                        <h3 class="mb-0"><?php echo $cuti_diajukan; ?></h3>
                    </div>
                    <div class="avatar">
                        <div class="avatar-title rounded-circle bg-warning text-white fs-2">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="card-title mb-0">5 Riwayat Pengajuan Cuti Terakhir Anda</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tgl Pengajuan</th>
                        <th>Tgl Mulai Cuti</th>
                        <th>Tgl Selesai Cuti</th>
                        <th>Alasan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result_recent_cuti) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result_recent_cuti)): ?>
                            <tr>
                                <td><?php echo date('d M Y, H:i', strtotime($row['tanggal_pengajuan'])); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['tanggal_mulai'])); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['tanggal_selesai'])); ?></td>
                                <td><?php echo htmlspecialchars($row['alasan']); ?></td>
                                <td>
                                    <?php
                                    $status = $row['status'];
                                    $badge_class = '';
                                    if ($status == 'Disetujui') {
                                        $badge_class = 'bg-success';
                                    } elseif ($status == 'Ditolak') {
                                        $badge_class = 'bg-danger';
                                    } else {
                                        $badge_class = 'bg-warning';
                                    }
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Anda belum pernah mengajukan cuti.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="text-end mt-3">
             <a href="index.php?page=cuti_riwayat" class="btn btn-sm btn-outline-primary">Lihat Semua Riwayat</a>
        </div>
    </div>
</div>
<style>
.avatar {
    width: 60px;
    height: 60px;
    display: inline-flex;
    align-items-center;
    justify-content: center;
    margin-left: 1rem;
}
.avatar .avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

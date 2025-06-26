<?php
// pages/dashboard_admin.php

// Pastikan tidak ada yang bisa mengakses file ini secara langsung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}

// Query untuk mengambil statistik
// 1. Jumlah Karyawan
$query_total_karyawan = "SELECT COUNT(id) as total FROM users WHERE role = 'user'";
$result_total_karyawan = mysqli_query($koneksi, $query_total_karyawan);
$total_karyawan = mysqli_fetch_assoc($result_total_karyawan)['total'];

// 2. Jumlah Pengajuan Cuti (Bulan Ini)
$bulan_ini = date('m');
$tahun_ini = date('Y');
$query_cuti_bulan_ini = "SELECT COUNT(id) as total FROM pengajuan_cuti WHERE MONTH(tanggal_pengajuan) = '$bulan_ini' AND YEAR(tanggal_pengajuan) = '$tahun_ini'";
$result_cuti_bulan_ini = mysqli_query($koneksi, $query_cuti_bulan_ini);
$cuti_bulan_ini = mysqli_fetch_assoc($result_cuti_bulan_ini)['total'];

// 3. Jumlah Cuti Disetujui
$query_cuti_disetujui = "SELECT COUNT(id) as total FROM pengajuan_cuti WHERE status = 'Disetujui'";
$result_cuti_disetujui = mysqli_query($koneksi, $query_cuti_disetujui);
$cuti_disetujui = mysqli_fetch_assoc($result_cuti_disetujui)['total'];

// 4. Jumlah Cuti Menunggu Persetujuan
$query_cuti_diajukan = "SELECT COUNT(id) as total FROM pengajuan_cuti WHERE status = 'Diajukan'";
$result_cuti_diajukan = mysqli_query($koneksi, $query_cuti_diajukan);
$cuti_diajukan = mysqli_fetch_assoc($result_cuti_diajukan)['total'];

// Query untuk mengambil 5 pengajuan cuti terbaru
$query_recent_cuti = "
    SELECT pc.id, u.nama_lengkap, pc.tanggal_mulai, pc.tanggal_selesai, pc.status 
    FROM pengajuan_cuti pc 
    JOIN users u ON pc.user_id = u.id 
    ORDER BY pc.tanggal_pengajuan DESC 
    LIMIT 5";
$result_recent_cuti = mysqli_query($koneksi, $query_recent_cuti);

?>

<div class="row g-4 mb-4">
    <!-- Card Total Karyawan -->
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-2">Total Karyawan</h5>
                        <h3 class="mb-0"><?php echo $total_karyawan; ?></h3>
                    </div>
                    <div class="avatar">
                        <div class="avatar-title rounded-circle bg-primary text-white fs-2">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card Pengajuan Bulan Ini -->
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-2">Pengajuan Bulan Ini</h5>
                        <h3 class="mb-0"><?php echo $cuti_bulan_ini; ?></h3>
                    </div>
                    <div class="avatar">
                        <div class="avatar-title rounded-circle bg-info text-white fs-2">
                            <i class="bi bi-calendar-plus-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card Cuti Disetujui -->
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-2">Cuti Disetujui</h5>
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
    <!-- Card Menunggu Persetujuan -->
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-2">Menunggu Konfirmasi</h5>
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
        <h5 class="card-title mb-0">Pengajuan Cuti Terbaru</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
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
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['tanggal_mulai'])); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['tanggal_selesai'])); ?></td>
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
                            <td colspan="5" class="text-center">Belum ada pengajuan cuti.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.avatar {
    width: 60px;
    height: 60px;
    display: inline-flex;
    align-items: center;
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

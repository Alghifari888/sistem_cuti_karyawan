<?php
// pages/cuti_riwayat.php

// Pastikan tidak ada yang bisa mengakses file ini secara langsung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}

// Pastikan yang mengakses adalah user
if ($_SESSION['role'] != 'user') {
    echo "<div class='alert alert-danger'>Halaman ini hanya untuk karyawan.</div>";
    return;
}

$user_id = $_SESSION['user_id'];

// Query untuk mengambil semua riwayat cuti milik user yang sedang login
$query = "SELECT id, tanggal_pengajuan, tanggal_mulai, tanggal_selesai, alasan, status, catatan_admin 
          FROM pengajuan_cuti 
          WHERE user_id = ? 
          ORDER BY tanggal_pengajuan DESC";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<?php 
// Tampilkan notifikasi jika ada dari halaman sebelumnya
if (isset($_SESSION['pesan'])) {
    $alert_class = (strpos(strtolower($_SESSION['pesan']), 'gagal') !== false || strpos(strtolower($_SESSION['pesan']), 'error') !== false) 
                   ? 'alert-danger' 
                   : 'alert-success';
    
    echo "<div class='alert {$alert_class} alert-dismissible fade show' role='alert'>
            {$_SESSION['pesan']}
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
    unset($_SESSION['pesan']);
}
?>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="card-title mb-0">Riwayat Pengajuan Cuti Saya</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">No</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Alasan Cuti</th>
                        <th>Status</th>
                        <th>Catatan Admin</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr id="cuti-row-<?php echo $row['id']; ?>">
                                <td class="text-center"><?php echo $no++; ?></td>
                                <td><?php echo date('d M Y, H:i', strtotime($row['tanggal_pengajuan'])); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['tanggal_mulai'])); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['tanggal_selesai'])); ?></td>
                                <td style="min-width: 200px;"><?php echo htmlspecialchars($row['alasan']); ?></td>
                                <td class="text-center">
                                    <?php
                                    $status = $row['status'];
                                    $badge_class = '';
                                    if ($status == 'Disetujui') {
                                        $badge_class = 'bg-success';
                                    } elseif ($status == 'Ditolak') {
                                        $badge_class = 'bg-danger';
                                    } else {
                                        $badge_class = 'bg-warning text-dark';
                                    }
                                    ?>
                                    <span id="status-badge-<?php echo $row['id']; ?>" class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($row['catatan_admin'] ?? 'Tidak ada catatan.'); ?></td>
                                <td class="text-center">
                                    <?php if ($row['status'] == 'Diajukan'): ?>
                                        <a href="proses/cuti_batal.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin membatalkan pengajuan cuti ini?');" title="Batalkan">
                                            <i class="bi bi-x-lg"></i> Batalkan
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Anda belum memiliki riwayat pengajuan cuti. Silakan ajukan cuti baru.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
         <div class="mt-3">
            <a href="index.php?page=cuti_ajukan" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Buat Pengajuan Cuti Baru</a>
        </div>
    </div>
</div>
<?php mysqli_stmt_close($stmt); ?>
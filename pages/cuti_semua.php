<?php
// pages/cuti_semua.php

// Pastikan tidak ada yang bisa mengakses file ini secara langsung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}

// Pastikan yang mengakses adalah admin
if ($_SESSION['role'] != 'admin') {
    echo "<div class='alert alert-danger'>Halaman ini hanya untuk admin.</div>";
    return;
}

// Query untuk mengambil semua data pengajuan cuti dan menggabungkannya dengan data user
$query = "
    SELECT 
        pc.id, 
        pc.tanggal_pengajuan, 
        pc.tanggal_mulai, 
        pc.tanggal_selesai, 
        pc.alasan, 
        pc.status,
        pc.catatan_admin,
        u.nik,
        u.nama_lengkap 
    FROM 
        pengajuan_cuti pc
    JOIN 
        users u ON pc.user_id = u.id
    ORDER BY 
        CASE pc.status
            WHEN 'Diajukan' THEN 1
            WHEN 'Disetujui' THEN 2
            WHEN 'Ditolak' THEN 3
        END, pc.tanggal_pengajuan DESC
";

$result = mysqli_query($koneksi, $query);

?>

<?php 
// Tampilkan notifikasi jika ada dari halaman proses verifikasi
if (isset($_SESSION['pesan'])) {
    $alert_class = (strpos(strtolower($_SESSION['pesan']), 'gagal') !== false || strpos(strtolower($_SESSION['pesan']), 'error') !== false) 
                   ? 'alert-danger' 
                   : 'alert-success';
    
    echo "<div class='alert {$alert_class} alert-dismissible fade show' role='alert'>
            {$_SESSION['pesan']}
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
    unset($_SESSION['pesan']); // Hapus session setelah ditampilkan
}
?>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="card-title mb-0">Semua Data Pengajuan Cuti Karyawan</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">No</th>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Tanggal Cuti</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="text-center"><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nik']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                <td><?php echo date('d M Y, H:i', strtotime($row['tanggal_pengajuan'])); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['tanggal_mulai'])) . ' - ' . date('d M Y', strtotime($row['tanggal_selesai'])); ?></td>
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
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?page=cuti_verifikasi&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info" title="Lihat Detail & Verifikasi">
                                        <i class="bi bi-search"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data pengajuan cuti dari karyawan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php mysqli_close($koneksi); ?>

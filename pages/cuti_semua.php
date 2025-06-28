<?php
// pages/cuti_semua.php

// Cegah akses langsung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}

// Hanya admin yang boleh mengakses
if ($_SESSION['role'] != 'admin') {
    echo "<div class='alert alert-danger'>Halaman ini hanya untuk admin.</div>";
    return;
}

// Ambil semua data pengajuan cuti
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
    FROM pengajuan_cuti pc
    JOIN users u ON pc.user_id = u.id
    ORDER BY 
        CASE pc.status
            WHEN 'Diajukan' THEN 1
            WHEN 'Disetujui' THEN 2
            WHEN 'Ditolak' THEN 3
        END, pc.tanggal_pengajuan DESC
";
$result = mysqli_query($koneksi, $query);
?>

<!-- Google Font & Bootstrap Icons -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Hubungkan file CSS -->
<link rel="stylesheet" href="pages/css/cuti_semua.css">

<!-- Notifikasi -->
<?php 
if (isset($_SESSION['pesan'])) {
    $alert_class = (stripos($_SESSION['pesan'], 'gagal') !== false || stripos($_SESSION['pesan'], 'error') !== false) ? 'alert-danger' : 'alert-success';
    echo "<div class='alert {$alert_class} alert-dismissible fade show' role='alert'>
            {$_SESSION['pesan']}
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
    unset($_SESSION['pesan']);
}
?>

<!-- Card Tabel -->
<div class="card shadow-sm">
    <div class="card-header bg-white border-0">
        <h5 class="card-title mb-0">📋 Semua Data Pengajuan Cuti Karyawan</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Pengajuan</th>
                        <th>Tanggal Cuti</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="table-row-animate">
                                <td class="text-center"><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nik']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                <td><?php echo date('d M Y, H:i', strtotime($row['tanggal_pengajuan'])); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['tanggal_mulai'])) . ' - ' . date('d M Y', strtotime($row['tanggal_selesai'])); ?></td>
                                <td class="text-center">
                                    <?php
                                    $status = $row['status'];
                                    $badge_class = match ($status) {
                                        'Disetujui' => 'bg-success',
                                        'Ditolak' => 'bg-danger',
                                        default => 'bg-warning text-dark',
                                    };
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?page=cuti_verifikasi&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-search"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center">Belum ada data pengajuan cuti dari karyawan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Script Animasi -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const rows = document.querySelectorAll('.table-row-animate');
    rows.forEach((row, i) => {
        setTimeout(() => {
            row.style.opacity = '1';
        }, i * 120);
    });
});
</script>

<?php mysqli_close($koneksi); ?>

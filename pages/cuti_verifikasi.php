<?php
// pages/cuti_verifikasi.php

// Pastikan tidak ada yang bisa mengakses file ini secara langsung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}

// Pastikan yang mengakses adalah admin
if ($_SESSION['role'] != 'admin') {
    echo "<div class='alert alert-danger'>Halaman ini hanya untuk admin.</div>";
    return;
}

// Cek apakah ada ID pengajuan yang valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>Permintaan tidak valid. ID pengajuan tidak ditemukan.</div>";
    return;
}

$id_pengajuan = $_GET['id'];

// Query untuk mengambil detail pengajuan cuti beserta data karyawannya
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
        u.nama_lengkap,
        u.jabatan,
        u.tanggal_bergabung
    FROM 
        pengajuan_cuti pc
    JOIN 
        users u ON pc.user_id = u.id
    WHERE 
        pc.id = ?
";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_pengajuan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cuti = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Jika pengajuan tidak ditemukan
if (!$cuti) {
    echo "<div class='alert alert-danger'>Data pengajuan cuti tidak ditemukan.</div>";
    return;
}

// Hitung durasi cuti
$tgl_mulai = new DateTime($cuti['tanggal_mulai']);
$tgl_selesai = new DateTime($cuti['tanggal_selesai']);
$durasi = $tgl_selesai->diff($tgl_mulai)->days + 1;
?>

<div class="card shadow-sm animate__animated animate__fadeIn">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Detail & Verifikasi Pengajuan Cuti</h5>
        <a href="index.php?page=cuti_semua" class="btn btn-sm text-white" style="background: linear-gradient(135deg, #f66d6d, #e74c3c); border: none;">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Kolom Detail Karyawan -->
            <div class="col-md-6">
                <h6>Data Karyawan</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td style="width: 150px;"><strong>NIK</strong></td>
                        <td>: <?php echo htmlspecialchars($cuti['nik']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Nama Lengkap</strong></td>
                        <td>: <?php echo htmlspecialchars($cuti['nama_lengkap']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Jabatan</strong></td>
                        <td>: <?php echo htmlspecialchars($cuti['jabatan']); ?></td>
                    </tr>
                     <tr>
                        <td><strong>Tanggal Bergabung</strong></td>
                        <td>: <?php echo date('d F Y', strtotime($cuti['tanggal_bergabung'])); ?></td>
                    </tr>
                </table>
            </div>
            <!-- Kolom Detail Cuti -->
            <div class="col-md-6">
                <h6>Data Pengajuan Cuti</h6>
                 <table class="table table-sm table-borderless">
                    <tr>
                        <td style="width: 150px;"><strong>Tanggal Pengajuan</strong></td>
                        <td>: <?php echo date('d M Y, H:i', strtotime($cuti['tanggal_pengajuan'])); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Cuti</strong></td>
                        <td>: <?php echo date('d M Y', strtotime($cuti['tanggal_mulai'])); ?> s/d <?php echo date('d M Y', strtotime($cuti['tanggal_selesai'])); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Durasi</strong></td>
                        <td>: <?php echo $durasi; ?> hari</td>
                    </tr>
                     <tr>
                        <td><strong>Status Saat Ini</strong></td>
                        <td>: 
                            <?php
                                $status = $cuti['status'];
                                $badge_class = '';
                                if ($status == 'Disetujui') $badge_class = 'bg-success';
                                elseif ($status == 'Ditolak') $badge_class = 'bg-danger';
                                else $badge_class = 'bg-warning text-dark';
                            ?>
                            <span class="badge <?php echo $badge_class; ?> animate__animated animate__fadeInDown"><?php echo $status; ?></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>
        
        <h6>Alasan Pengajuan Cuti</h6>
        <div class="alert alert-light p-3 animate__animated animate__fadeInUp">
             <?php echo nl2br(htmlspecialchars($cuti['alasan'])); ?>
        </div>

        <hr>

        <!-- Form Verifikasi -->
        <?php if ($cuti['status'] == 'Diajukan'): ?>
            <h6>Formulir Verifikasi</h6>
            <form action="proses/cuti_verifikasi.php" method="POST" onsubmit="return handleVerifikasi(this)">
                <input type="hidden" name="id_pengajuan" value="<?php echo $cuti['id']; ?>">
                <div class="mb-3">
                    <label for="catatan_admin" class="form-label">Catatan (Opsional)</label>
                    <textarea class="form-control" name="catatan_admin" id="catatan_admin" rows="3" placeholder="Berikan catatan jika perlu, misal: 'Disetujui, harap selesaikan pekerjaan X sebelum cuti.'"></textarea>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" name="status" value="Ditolak" class="btn btn-danger">
                        <i class="bi bi-x-circle-fill"></i> Tolak
                    </button>
                    <button type="submit" name="status" value="Disetujui" class="btn btn-success">
                        <i class="bi bi-check-circle-fill"></i> Setujui
                    </button>
                </div>
            </form>
        <?php else: ?>
            <h6>Hasil Verifikasi</h6>
            <div class="alert alert-info">
                <p class="mb-1"><strong>Status Akhir:</strong> <?php echo $cuti['status']; ?></p>
                <p class="mb-0"><strong>Catatan dari Admin:</strong> <?php echo htmlspecialchars($cuti['catatan_admin'] ?? 'Tidak ada catatan.'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- SweetAlert2 & Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Popup animasi -->
<script>
function handleVerifikasi(form) {
    const status = form.querySelector('button[type="submit"]:focus').value;

    Swal.fire({
        title: (status === 'Disetujui') ? 'Setujui Pengajuan?' : 'Tolak Pengajuan?',
        text: "Pastikan Anda sudah mengecek semua data.",
        icon: (status === 'Disetujui') ? 'success' : 'warning',
        showCancelButton: true,
        confirmButtonColor: (status === 'Disetujui') ? '#28a745' : '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: (status === 'Disetujui') ? 'Ya, Setujui' : 'Ya, Tolak',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        showClass: {
            popup: 'animate__animated animate__zoomIn'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });

    return false;
}
</script>

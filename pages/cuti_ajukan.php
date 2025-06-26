<?php
// pages/cuti_ajukan.php

// Pastikan tidak ada yang bisa mengakses file ini secara langsung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}

// Pastikan yang mengakses adalah user
if ($_SESSION['role'] != 'user') {
    echo "<div class='alert alert-danger'>Halaman ini hanya untuk karyawan.</div>";
    return;
}

// Cek jika ada data error atau data lama dari proses sebelumnya
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$old_data = isset($_SESSION['old_data']) ? $_SESSION['old_data'] : [];
unset($_SESSION['errors']);
unset($_SESSION['old_data']);
?>

<?php 
// Tampilkan notifikasi jika ada
if (isset($_SESSION['pesan'])) {
    // Tentukan class alert berdasarkan jenis pesan
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
        <h5 class="card-title mb-0">Formulir Pengajuan Cuti</h5>
    </div>
    <div class="card-body">
        <form action="proses/cuti_ajukan.php" method="POST">
            <div class="row g-3">
                <!-- Kolom Tanggal Mulai -->
                <div class="col-md-6">
                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai Cuti</label>
                    <input type="date" class="form-control <?php echo isset($errors['tanggal_mulai']) ? 'is-invalid' : ''; ?>" id="tanggal_mulai" name="tanggal_mulai" value="<?php echo htmlspecialchars($old_data['tanggal_mulai'] ?? ''); ?>" required>
                    <?php if (isset($errors['tanggal_mulai'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['tanggal_mulai']; ?></div>
                    <?php endif; ?>
                </div>

                <!-- Kolom Tanggal Selesai -->
                <div class="col-md-6">
                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai Cuti</label>
                    <input type="date" class="form-control <?php echo isset($errors['tanggal_selesai']) ? 'is-invalid' : ''; ?>" id="tanggal_selesai" name="tanggal_selesai" value="<?php echo htmlspecialchars($old_data['tanggal_selesai'] ?? ''); ?>" required>
                    <?php if (isset($errors['tanggal_selesai'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['tanggal_selesai']; ?></div>
                    <?php endif; ?>
                </div>

                <!-- Kolom Alasan -->
                <div class="col-12">
                    <label for="alasan" class="form-label">Alasan Cuti</label>
                    <textarea class="form-control <?php echo isset($errors['alasan']) ? 'is-invalid' : ''; ?>" id="alasan" name="alasan" rows="4" required placeholder="Jelaskan alasan Anda mengajukan cuti..."><?php echo htmlspecialchars($old_data['alasan'] ?? ''); ?></textarea>
                     <?php if (isset($errors['alasan'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['alasan']; ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <hr class="my-4">

            <p class="text-muted">
                <strong>Perhatian:</strong> Pastikan tanggal yang Anda ajukan sudah benar. Pengajuan yang sudah dikirim tidak dapat diubah, hanya bisa dibatalkan jika belum diverifikasi oleh Admin.
            </p>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="reset" class="btn btn-outline-secondary">Reset Form</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send-fill"></i> Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

<?php
// pages/user_tambah.php

// Pastikan tidak ada yang bisa mengakses file ini secara langsung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}

// Cek jika ada data error dari proses sebelumnya
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$old_data = isset($_SESSION['old_data']) ? $_SESSION['old_data'] : [];
unset($_SESSION['errors']);
unset($_SESSION['old_data']);

// --- LOGIKA PENGISIAN OTOMATIS ---

// 1. Logika untuk NIK Otomatis
// Mengambil NIK terakhir dari database
$query_nik = "SELECT MAX(nik) as nik_terakhir FROM users";
$result_nik = mysqli_query($koneksi, $query_nik);
$data_nik = mysqli_fetch_assoc($result_nik);
$nik_terakhir = $data_nik['nik_terakhir'];

if ($nik_terakhir) {
    // Jika ada NIK, increment nomornya
    $nomor = (int) $nik_terakhir;
    $nomor++;
    // Format kembali menjadi 5 digit dengan angka 0 di depan
    $nik_otomatis = str_pad($nomor, 5, "0", STR_PAD_LEFT);
} else {
    // Jika belum ada user sama sekali, mulai dari 00001
    $nik_otomatis = "00001";
}

// 2. Logika untuk Tanggal Bergabung Otomatis
// Mengambil tanggal hari ini
$tanggal_sekarang = date('Y-m-d');

?>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Formulir Tambah Karyawan</h5>
        <a href="index.php?page=user_list" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="proses/user_tambah.php" method="POST">
            <div class="row g-3">
                <!-- Kolom NIK -->
                <div class="col-md-6">
                    <label for="nik" class="form-label">NIK (Nomor Induk Karyawan)</label>
                    <input type="text" class="form-control <?php echo isset($errors['nik']) ? 'is-invalid' : ''; ?>" id="nik" name="nik" value="<?php echo htmlspecialchars($old_data['nik'] ?? $nik_otomatis); ?>" required maxlength="5">
                    <div class="form-text">NIK terisi otomatis, namun bisa diubah jika perlu.</div>
                    <?php if (isset($errors['nik'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['nik']; ?></div>
                    <?php endif; ?>
                </div>

                <!-- Kolom Nama Lengkap -->
                <div class="col-md-6">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control <?php echo isset($errors['nama_lengkap']) ? 'is-invalid' : ''; ?>" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($old_data['nama_lengkap'] ?? ''); ?>" required>
                    <?php if (isset($errors['nama_lengkap'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['nama_lengkap']; ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Kolom Password -->
                <div class="col-md-6">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password" required>
                     <div class="form-text">Password default untuk karyawan baru.</div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Kolom Jenis Kelamin -->
                <div class="col-md-6">
                    <label class="form-label">Jenis Kelamin</label>
                    <div class="d-flex">
                        <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="laki_laki" value="L" <?php echo (isset($old_data['jenis_kelamin']) && $old_data['jenis_kelamin'] == 'L') ? 'checked' : 'checked'; ?>>
                            <label class="form-check-label" for="laki_laki">Laki-laki</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="perempuan" value="P" <?php echo (isset($old_data['jenis_kelamin']) && $old_data['jenis_kelamin'] == 'P') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="perempuan">Perempuan</label>
                        </div>
                    </div>
                </div>

                <!-- Kolom Jabatan -->
                 <div class="col-md-6">
                    <label for="jabatan" class="form-label">Jabatan</label>
                    <input type="text" class="form-control" id="jabatan" name="jabatan" value="<?php echo htmlspecialchars($old_data['jabatan'] ?? ''); ?>">
                </div>

                <!-- Kolom Tanggal Bergabung -->
                <div class="col-md-6">
                    <label for="tanggal_bergabung" class="form-label">Tanggal Bergabung</label>
                    <input type="date" class="form-control" id="tanggal_bergabung" name="tanggal_bergabung" value="<?php echo htmlspecialchars($old_data['tanggal_bergabung'] ?? $tanggal_sekarang); ?>">
                </div>

                <!-- Kolom Alamat -->
                <div class="col-12">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3"><?php echo htmlspecialchars($old_data['alamat'] ?? ''); ?></textarea>
                </div>

                 <!-- Kolom Role (Default User) -->
                <input type="hidden" name="role" value="user">

            </div>

            <hr class="my-4">

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="reset" class="btn btn-outline-secondary">Reset Form</button>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

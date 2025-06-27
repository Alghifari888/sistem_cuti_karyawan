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
// 1. NIK Otomatis
$query_nik = "SELECT MAX(nik) as nik_terakhir FROM users";
$result_nik = mysqli_query($koneksi, $query_nik);
$data_nik = mysqli_fetch_assoc($result_nik);
$nik_terakhir = $data_nik['nik_terakhir'];
$nik_otomatis = $nik_terakhir ? str_pad((int)$nik_terakhir + 1, 5, "0", STR_PAD_LEFT) : "00001";

// 2. Tanggal Bergabung Otomatis
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
                
                <!-- Data Login -->
                <h6 class="mt-4">Data Login & Pribadi</h6>
                <div class="col-md-6">
                    <label for="nik" class="form-label">NIK</label>
                    <input type="text" class="form-control <?php echo isset($errors['nik']) ? 'is-invalid' : ''; ?>" id="nik" name="nik" value="<?php echo htmlspecialchars($old_data['nik'] ?? $nik_otomatis); ?>" required maxlength="5">
                    <?php if (isset($errors['nik'])): ?><div class="invalid-feedback"><?php echo $errors['nik']; ?></div><?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password" required>
                    <div class="form-text">Password default untuk login pertama kali.</div>
                    <?php if (isset($errors['password'])): ?><div class="invalid-feedback"><?php echo $errors['password']; ?></div><?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control <?php echo isset($errors['nama_lengkap']) ? 'is-invalid' : ''; ?>" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($old_data['nama_lengkap'] ?? ''); ?>" required>
                    <?php if (isset($errors['nama_lengkap'])): ?><div class="invalid-feedback"><?php echo $errors['nama_lengkap']; ?></div><?php endif; ?>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select">
                        <option value="L" <?php echo (isset($old_data['jenis_kelamin']) && $old_data['jenis_kelamin'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="P" <?php echo (isset($old_data['jenis_kelamin']) && $old_data['jenis_kelamin'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="<?php echo htmlspecialchars($old_data['tanggal_lahir'] ?? ''); ?>">
                </div>

                <!-- Kontak & Alamat -->
                <h6 class="mt-4">Kontak & Alamat</h6>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($old_data['email'] ?? ''); ?>">
                    <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?php echo $errors['email']; ?></div><?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="no_telepon" class="form-label">No. Telepon</label>
                    <input type="text" class="form-control" id="no_telepon" name="no_telepon" value="<?php echo htmlspecialchars($old_data['no_telepon'] ?? ''); ?>">
                </div>

                <div class="col-12">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="2"><?php echo htmlspecialchars($old_data['alamat'] ?? ''); ?></textarea>
                </div>

                <!-- Data Kepegawaian -->
                <h6 class="mt-4">Data Kepegawaian</h6>
                <div class="col-md-6">
                    <label for="departemen" class="form-label">Departemen</label>
                    <input type="text" class="form-control" id="departemen" name="departemen" value="<?php echo htmlspecialchars($old_data['departemen'] ?? ''); ?>">
                </div>
                
                <div class="col-md-6">
                    <label for="jabatan" class="form-label">Jabatan</label>
                    <input type="text" class="form-control" id="jabatan" name="jabatan" value="<?php echo htmlspecialchars($old_data['jabatan'] ?? ''); ?>">
                </div>
                
                <div class="col-md-4">
                    <label for="status_karyawan" class="form-label">Status Karyawan</label>
                    <select name="status_karyawan" class="form-select">
                        <option value="Aktif" selected>Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                        <option value="Cuti">Cuti</option>
                        <option value="Resign">Resign</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="tanggal_bergabung" class="form-label">Tanggal Bergabung</label>
                    <input type="date" class="form-control" id="tanggal_bergabung" name="tanggal_bergabung" value="<?php echo htmlspecialchars($old_data['tanggal_bergabung'] ?? $tanggal_sekarang); ?>">
                </div>
                
                <div class="col-md-4">
                    <label for="gaji_pokok" class="form-label">Gaji Pokok</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="gaji_pokok" name="gaji_pokok" value="<?php echo htmlspecialchars($old_data['gaji_pokok'] ?? '0'); ?>" step="50000">
                    </div>
                </div>

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

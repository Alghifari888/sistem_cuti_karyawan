<?php
// pages/user_edit.php

// Pastikan tidak ada yang bisa mengakses file ini secara langsung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}

// Cek apakah ada ID yang dikirim
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID Karyawan tidak valid.</div>";
    return; // Hentikan eksekusi skrip
}

$id_user = $_GET['id'];

// Ambil data user dari database berdasarkan ID
$query = "SELECT nik, nama_lengkap, jenis_kelamin, alamat, jabatan, tanggal_bergabung FROM users WHERE id = ? AND role = 'user'";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Jika user tidak ditemukan
if (!$user) {
    echo "<div class='alert alert-danger'>Data Karyawan tidak ditemukan.</div>";
    return; // Hentikan eksekusi skrip
}

// Cek jika ada data error dari proses sebelumnya
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$old_data = isset($_SESSION['old_data']) ? $_SESSION['old_data'] : $user;
unset($_SESSION['errors']);
unset($_SESSION['old_data']);

?>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Formulir Edit Karyawan</h5>
        <a href="index.php?page=user_list" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="proses/user_edit.php" method="POST">
            <!-- Simpan ID user sebagai hidden input -->
            <input type="hidden" name="id" value="<?php echo $id_user; ?>">

            <div class="row g-3">
                <!-- Kolom NIK (tidak bisa diubah) -->
                <div class="col-md-6">
                    <label for="nik" class="form-label">NIK (Nomor Induk Karyawan)</label>
                    <input type="text" class="form-control" id="nik" name="nik" value="<?php echo htmlspecialchars($user['nik']); ?>" readonly disabled>
                    <div class="form-text">NIK tidak dapat diubah.</div>
                </div>

                <!-- Kolom Nama Lengkap -->
                <div class="col-md-6">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control <?php echo isset($errors['nama_lengkap']) ? 'is-invalid' : ''; ?>" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($old_data['nama_lengkap'] ?? ''); ?>" required>
                    <?php if (isset($errors['nama_lengkap'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['nama_lengkap']; ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Kolom Password (Opsional) -->
                <div class="col-md-6">
                    <label for="password" class="form-label">Password Baru</label>
                    <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password">
                    <div class="form-text text-danger">Kosongkan jika tidak ingin mengubah password.</div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Kolom Jenis Kelamin -->
                <div class="col-md-6">
                    <label class="form-label">Jenis Kelamin</label>
                    <div class="d-flex">
                        <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="laki_laki" value="L" <?php echo ($old_data['jenis_kelamin'] == 'L') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="laki_laki">Laki-laki</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="perempuan" value="P" <?php echo ($old_data['jenis_kelamin'] == 'P') ? 'checked' : ''; ?>>
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
                    <input type="date" class="form-control" id="tanggal_bergabung" name="tanggal_bergabung" value="<?php echo htmlspecialchars($old_data['tanggal_bergabung'] ?? ''); ?>">
                </div>

                <!-- Kolom Alamat -->
                <div class="col-12">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3"><?php echo htmlspecialchars($old_data['alamat'] ?? ''); ?></textarea>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="index.php?page=user_list" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Update Data</button>
            </div>
        </form>
    </div>
</div>

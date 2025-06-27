<?php
// pages/profil.php

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}

$user_id = $_SESSION['user_id'];

// Ambil data lengkap user yang sedang login dari database
$query_user = "SELECT * FROM users WHERE id = ?";
$stmt_user = mysqli_prepare($koneksi, $query_user);
mysqli_stmt_bind_param($stmt_user, "i", $user_id);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);
$user = mysqli_fetch_assoc($result_user);
mysqli_stmt_close($stmt_user);

if (!$user) {
    echo "<div class='alert alert-danger'>Gagal memuat data profil.</div>";
    return;
}

$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$pesan_sukses = isset($_SESSION['pesan_sukses']) ? $_SESSION['pesan_sukses'] : '';
unset($_SESSION['errors']);
unset($_SESSION['pesan_sukses']);
?>

<?php
if (!empty($pesan_sukses)) {
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>{$pesan_sukses}<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
}
if (isset($errors['database'])) {
     echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>{$errors['database']}<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
}
?>

<div class="row">
    <!-- Kolom Data Pribadi -->
    <div class="col-lg-7">
        <div class="card shadow-sm mb-4">
            <div class="card-header"><h5 class="card-title mb-0">Data Pribadi & Kontak</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">NIK</label><p class="form-control-plaintext ps-2"><?php echo htmlspecialchars($user['nik']); ?></p></div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">Nama Lengkap</label><p class="form-control-plaintext ps-2"><?php echo htmlspecialchars($user['nama_lengkap']); ?></p></div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">Jenis Kelamin</label><p class="form-control-plaintext ps-2"><?php echo ($user['jenis_kelamin'] == 'L') ? 'Laki-laki' : 'Perempuan'; ?></p></div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">Tanggal Lahir</label><p class="form-control-plaintext ps-2"><?php echo date('d F Y', strtotime($user['tanggal_lahir'])); ?></p></div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">Email</label><p class="form-control-plaintext ps-2"><?php echo htmlspecialchars($user['email']); ?></p></div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">No. Telepon</label><p class="form-control-plaintext ps-2"><?php echo htmlspecialchars($user['no_telepon']); ?></p></div>
                    <div class="col-12 mb-3"><label class="form-label fw-bold">Alamat</label><p class="form-control-plaintext ps-2"><?php echo nl2br(htmlspecialchars($user['alamat'])); ?></p></div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header"><h5 class="card-title mb-0">Data Kepegawaian</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">Departemen</label><p class="form-control-plaintext ps-2"><?php echo htmlspecialchars($user['departemen']); ?></p></div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">Jabatan</label><p class="form-control-plaintext ps-2"><?php echo htmlspecialchars($user['jabatan']); ?></p></div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">Tanggal Bergabung</label><p class="form-control-plaintext ps-2"><?php echo date('d F Y', strtotime($user['tanggal_bergabung'])); ?></p></div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">Status Karyawan</label><p class="form-control-plaintext ps-2"><?php echo htmlspecialchars($user['status_karyawan']); ?></p></div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">Gaji Pokok</label><p class="form-control-plaintext ps-2">Rp <?php echo number_format($user['gaji_pokok'], 2, ',', '.'); ?></p></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Ubah Password -->
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header"><h5 class="card-title mb-0">Ubah Password</h5></div>
            <div class="card-body">
                <form action="proses/profil_update.php" method="POST">
                    <div class="mb-3">
                        <label for="password_lama" class="form-label">Password Lama</label>
                        <input type="password" class="form-control <?php echo isset($errors['password_lama']) ? 'is-invalid' : ''; ?>" id="password_lama" name="password_lama" required>
                        <?php if (isset($errors['password_lama'])): ?><div class="invalid-feedback"><?php echo $errors['password_lama']; ?></div><?php endif; ?>
                    </div>
                     <div class="mb-3">
                        <label for="password_baru" class="form-label">Password Baru</label>
                        <input type="password" class="form-control <?php echo isset($errors['password_baru']) ? 'is-invalid' : ''; ?>" id="password_baru" name="password_baru" required>
                        <div class="form-text">Minimal 6 karakter.</div>
                         <?php if (isset($errors['password_baru'])): ?><div class="invalid-feedback"><?php echo $errors['password_baru']; ?></div><?php endif; ?>
                    </div>
                     <div class="mb-3">
                        <label for="konfirmasi_password" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control <?php echo isset($errors['konfirmasi_password']) ? 'is-invalid' : ''; ?>" id="konfirmasi_password" name="konfirmasi_password" required>
                         <?php if (isset($errors['konfirmasi_password'])): ?><div class="invalid-feedback"><?php echo $errors['konfirmasi_password']; ?></div><?php endif; ?>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-key-fill"></i> Ubah Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

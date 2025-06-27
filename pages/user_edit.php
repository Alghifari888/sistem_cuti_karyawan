<?php
// pages/user_edit.php

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}

// Cek ID user
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID Karyawan tidak valid.</div>";
    return;
}
$id_user = $_GET['id'];

// Ambil data user lengkap dari database
$query = "SELECT * FROM users WHERE id = ? AND role = 'user'";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    echo "<div class='alert alert-danger'>Data Karyawan tidak ditemukan.</div>";
    return;
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
        <a href="index.php?page=user_list" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
    <div class="card-body">
        <form action="proses/user_edit.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $id_user; ?>">
            <div class="row g-3">
                
                <!-- Data Login & Pribadi -->
                <h6 class="mt-4">Data Login & Pribadi</h6>
                <div class="col-md-6">
                    <label for="nik" class="form-label">NIK</label>
                    <input type="text" class="form-control" id="nik" value="<?php echo htmlspecialchars($old_data['nik']); ?>" readonly disabled>
                    <div class="form-text">NIK tidak dapat diubah.</div>
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label">Password Baru</label>
                    <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password">
                    <div class="form-text text-danger">Kosongkan jika tidak ingin mengubah password.</div>
                    <?php if (isset($errors['password'])): ?><div class="invalid-feedback"><?php echo $errors['password']; ?></div><?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control <?php echo isset($errors['nama_lengkap']) ? 'is-invalid' : ''; ?>" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($old_data['nama_lengkap']); ?>" required>
                    <?php if (isset($errors['nama_lengkap'])): ?><div class="invalid-feedback"><?php echo $errors['nama_lengkap']; ?></div><?php endif; ?>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select">
                        <option value="L" <?php echo ($old_data['jenis_kelamin'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="P" <?php echo ($old_data['jenis_kelamin'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="<?php echo htmlspecialchars($old_data['tanggal_lahir']); ?>">
                </div>

                <!-- Kontak & Alamat -->
                <h6 class="mt-4">Kontak & Alamat</h6>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($old_data['email']); ?>">
                    <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?php echo $errors['email']; ?></div><?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="no_telepon" class="form-label">No. Telepon</label>
                    <input type="text" class="form-control" id="no_telepon" name="no_telepon" value="<?php echo htmlspecialchars($old_data['no_telepon']); ?>">
                </div>
                <div class="col-12">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="2"><?php echo htmlspecialchars($old_data['alamat']); ?></textarea>
                </div>

                <!-- Data Kepegawaian -->
                <h6 class="mt-4">Data Kepegawaian</h6>
                <div class="col-md-6">
                    <label for="departemen" class="form-label">Departemen</label>
                    <input type="text" class="form-control" id="departemen" name="departemen" value="<?php echo htmlspecialchars($old_data['departemen']); ?>">
                </div>
                <div class="col-md-6">
                    <label for="jabatan" class="form-label">Jabatan</label>
                    <input type="text" class="form-control" id="jabatan" name="jabatan" value="<?php echo htmlspecialchars($old_data['jabatan']); ?>">
                </div>
                <div class="col-md-4">
                    <label for="status_karyawan" class="form-label">Status Karyawan</label>
                    <select name="status_karyawan" class="form-select">
                        <option value="Aktif" <?php echo ($old_data['status_karyawan'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                        <option value="Tidak Aktif" <?php echo ($old_data['status_karyawan'] == 'Tidak Aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                        <option value="Cuti" <?php echo ($old_data['status_karyawan'] == 'Cuti') ? 'selected' : ''; ?>>Cuti</option>
                        <option value="Resign" <?php echo ($old_data['status_karyawan'] == 'Resign') ? 'selected' : ''; ?>>Resign</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="tanggal_bergabung" class="form-label">Tanggal Bergabung</label>
                    <input type="date" class="form-control" id="tanggal_bergabung" name="tanggal_bergabung" value="<?php echo htmlspecialchars($old_data['tanggal_bergabung']); ?>">
                </div>
                <div class="col-md-4">
                    <label for="gaji_pokok" class="form-label">Gaji Pokok</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="gaji_pokok" name="gaji_pokok" value="<?php echo htmlspecialchars($old_data['gaji_pokok']); ?>" step="50000">
                    </div>
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

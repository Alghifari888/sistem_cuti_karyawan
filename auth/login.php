<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php?page=dashboard");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Cuti Karyawan</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-card">
        <div class="text-center login-header mb-4">
            <!-- Ganti icon orang dengan logo -->
            <img src="../asset/img/logo.png" alt="Logo" width="120">
            <h1 class="h4">Sistem Cuti Karyawan</h1>
            <p class="text-muted">Silakan login menggunakan NIK Anda</p>
        </div>

        <form action="proses_login.php" method="POST">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="nik" name="nik" placeholder="NIK" required>
                <label for="nik">NIK (Nomor Induk Karyawan)</label>
            </div>
            <div class="form-floating mb-4">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password">Password</label>
            </div>

            <button class="btn btn-primary w-100 py-2 mb-3" type="submit">Login</button>
            <p class="text-center text-muted">&copy; <?= date('Y') ?> Sistem Cuti</p>
        </form>
    </div>

    <!-- Modal Popup Error -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel"><i class="bi bi-exclamation-triangle-fill text-danger"></i> Login Gagal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <?php
                    if (isset($_GET['error'])) {
                        if ($_GET['error'] == '1') {
                            echo 'NIK atau Password salah!';
                        } elseif ($_GET['error'] == '2') {
                            echo 'Terjadi kesalahan pada sistem.';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script Show Modal -->
    <?php if (isset($_GET['error'])): ?>
    <script>
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
    </script>
    <?php endif; ?>
</body>
</html>

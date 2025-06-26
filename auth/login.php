<?php
session_start();

// Jika pengguna sudah login, alihkan ke dashboard yang sesuai
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: ../index.php?page=dashboard");
    } else {
        header("Location: ../index.php?page=dashboard");
    }
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
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 450px;
            width: 100%;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card login-card">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="bi bi-person-circle fs-1 text-primary"></i>
                    <h1 class="h3 mb-3 fw-normal">Sistem Cuti Karyawan</h1>
                    <p>Silakan login menggunakan NIK Anda.</p>
                </div>
                
                <?php
                // Tampilkan pesan error jika ada
                if (isset($_GET['error'])) {
                    $error_message = '';
                    if ($_GET['error'] == '1') {
                        $error_message = 'NIK atau Password salah!';
                    } elseif ($_GET['error'] == '2') {
                        $error_message = 'Terjadi kesalahan pada sistem.';
                    }
                    echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
                }
                ?>

                <form action="proses_login.php" method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="nik" name="nik" placeholder="NIK" required>
                        <label for="nik">NIK (Nomor Induk Karyawan)</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                    </div>

                    <button class="w-100 btn btn-lg btn-primary" type="submit">Login</button>
                    <p class="mt-5 mb-3 text-muted text-center">&copy; <?php echo date('Y'); ?></p>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

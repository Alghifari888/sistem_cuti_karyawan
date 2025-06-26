<?php
// proses/profil_update.php

session_start();
require_once '../config/db.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Pastikan request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    $errors = [];

    // 1. Validasi input dasar
    if (empty($password_lama)) $errors['password_lama'] = "Password lama harus diisi.";
    if (empty($password_baru)) $errors['password_baru'] = "Password baru harus diisi.";
    if (empty($konfirmasi_password)) $errors['konfirmasi_password'] = "Konfirmasi password harus diisi.";

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../index.php?page=profil");
        exit();
    }
    
    // 2. Ambil password saat ini dari database
    $sql_get_pass = "SELECT password FROM users WHERE id = ?";
    $stmt_get = mysqli_prepare($koneksi, $sql_get_pass);
    mysqli_stmt_bind_param($stmt_get, "i", $user_id);
    mysqli_stmt_execute($stmt_get);
    $result = mysqli_stmt_get_result($stmt_get);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt_get);

    if (!$user) {
        $errors['database'] = "Terjadi kesalahan, data user tidak ditemukan.";
        $_SESSION['errors'] = $errors;
        header("Location: ../index.php?page=profil");
        exit();
    }

    $hashed_password_db = $user['password'];

    // 3. Verifikasi password lama
    if (!password_verify($password_lama, $hashed_password_db)) {
        $errors['password_lama'] = "Password lama yang Anda masukkan salah.";
    }

    // 4. Validasi password baru
    if (strlen($password_baru) < 6) {
        $errors['password_baru'] = "Password baru minimal harus 6 karakter.";
    }
    
    if ($password_baru !== $konfirmasi_password) {
        $errors['konfirmasi_password'] = "Konfirmasi password tidak cocok dengan password baru.";
    }

    // Jika ada error setelah validasi lengkap, kembali ke form
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../index.php?page=profil");
        exit();
    }

    // 5. Jika semua validasi lolos, hash dan update password baru
    $hashed_password_baru = password_hash($password_baru, PASSWORD_DEFAULT);

    $sql_update_pass = "UPDATE users SET password = ? WHERE id = ?";
    $stmt_update = mysqli_prepare($koneksi, $sql_update_pass);
    mysqli_stmt_bind_param($stmt_update, "si", $hashed_password_baru, $user_id);
    
    if (mysqli_stmt_execute($stmt_update)) {
        $_SESSION['pesan_sukses'] = "Password Anda telah berhasil diperbarui.";
    } else {
        $_SESSION['errors']['database'] = "Gagal memperbarui password. Terjadi kesalahan pada database.";
    }
    
    mysqli_stmt_close($stmt_update);
    mysqli_close($koneksi);

    header("Location: ../index.php?page=profil");
    exit();

} else {
    // Jika bukan metode POST, alihkan
    header("Location: ../index.php?page=profil");
    exit();
}
?>

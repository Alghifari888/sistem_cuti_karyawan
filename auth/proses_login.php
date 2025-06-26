<?php
// auth/proses_login.php

// Mulai session
session_start();

// Hubungkan ke database
require_once '../config/db.php';

// Pastikan request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form dan bersihkan
    $nik = mysqli_real_escape_string($koneksi, $_POST['nik']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Cek apakah NIK atau password kosong
    if (empty($nik) || empty($password)) {
        header("Location: login.php?error=1"); // Error: Field kosong
        exit();
    }

    // Buat query untuk mencari user berdasarkan NIK
    $sql = "SELECT id, nama_lengkap, password, role FROM users WHERE nik = ?";
    
    // Gunakan prepared statement untuk keamanan
    $stmt = mysqli_prepare($koneksi, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $nik);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Cek apakah user ditemukan
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Jika password cocok, buat session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nik'] = $nik;
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['role'] = $user['role'];

                // Alihkan berdasarkan role
                if ($user['role'] == 'admin') {
                    header("Location: ../index.php?page=dashboard");
                } else {
                    header("Location: ../index.php?page=dashboard");
                }
                exit();
            } else {
                // Password salah
                header("Location: login.php?error=1");
                exit();
            }
        } else {
            // NIK tidak ditemukan
            header("Location: login.php?error=1");
            exit();
        }
        mysqli_stmt_close($stmt);
    } else {
        // Query gagal
        header("Location: login.php?error=2");
        exit();
    }
    mysqli_close($koneksi);
} else {
    // Jika bukan metode POST, alihkan ke halaman login
    header("Location: login.php");
    exit();
}
?>

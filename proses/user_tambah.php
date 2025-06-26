<?php
// proses/user_tambah.php

// Mulai session
session_start();

// Hubungkan ke database
require_once '../config/db.php';

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    // Jika bukan admin atau belum login, alihkan ke halaman login
    header("Location: ../auth/login.php");
    exit();
}

// Pastikan request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form
    $nik = mysqli_real_escape_string($koneksi, trim($_POST['nik']));
    $nama_lengkap = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
    $password = mysqli_real_escape_string($koneksi, trim($_POST['password']));
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $jabatan = mysqli_real_escape_string($koneksi, trim($_POST['jabatan']));
    $tanggal_bergabung = mysqli_real_escape_string($koneksi, $_POST['tanggal_bergabung']);
    $alamat = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    // Validasi data
    $errors = [];

    // Validasi NIK
    if (empty($nik)) {
        $errors['nik'] = "NIK tidak boleh kosong.";
    } elseif (!ctype_digit($nik) || strlen($nik) > 5) {
        $errors['nik'] = "NIK harus berupa angka dan maksimal 5 digit.";
    } else {
        // Cek apakah NIK sudah ada di database
        $check_nik_query = "SELECT id FROM users WHERE nik = ?";
        $stmt_check = mysqli_prepare($koneksi, $check_nik_query);
        mysqli_stmt_bind_param($stmt_check, "s", $nik);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $errors['nik'] = "NIK ini sudah terdaftar. Silakan gunakan NIK lain.";
        }
        mysqli_stmt_close($stmt_check);
    }

    // Validasi Nama Lengkap
    if (empty($nama_lengkap)) {
        $errors['nama_lengkap'] = "Nama lengkap tidak boleh kosong.";
    }
    
    // Validasi Password
    if (empty($password)) {
        $errors['password'] = "Password tidak boleh kosong.";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password minimal harus 6 karakter.";
    }

    // Jika ada error, kembali ke form dengan pesan error
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_data'] = $_POST; // Simpan data yang sudah diinput
        header("Location: ../index.php?page=user_tambah");
        exit();
    }

    // Jika tidak ada error, lanjutkan proses
    // Hash password untuk keamanan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Siapkan query SQL dengan prepared statement
    $sql = "INSERT INTO users (nik, nama_lengkap, jenis_kelamin, alamat, jabatan, tanggal_bergabung, password, role) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt, 
            "ssssssss", 
            $nik, 
            $nama_lengkap, 
            $jenis_kelamin, 
            $alamat, 
            $jabatan, 
            $tanggal_bergabung, 
            $hashed_password, 
            $role
        );

        // Eksekusi statement
        if (mysqli_stmt_execute($stmt)) {
            // Jika berhasil, kirim pesan sukses dan alihkan
            $_SESSION['pesan'] = "Karyawan baru berhasil ditambahkan.";
            header("Location: ../index.php?page=user_list");
            exit();
        } else {
            // Jika gagal, kirim pesan error
            $_SESSION['errors']['database'] = "Terjadi kesalahan saat menyimpan data: " . mysqli_stmt_error($stmt);
            $_SESSION['old_data'] = $_POST;
            header("Location: ../index.php?page=user_tambah");
            exit();
        }

        // Tutup statement
        mysqli_stmt_close($stmt);
    } else {
        // Jika statement gagal disiapkan
        $_SESSION['errors']['database'] = "Terjadi kesalahan pada sistem. Silakan coba lagi.";
        $_SESSION['old_data'] = $_POST;
        header("Location: ../index.php?page=user_tambah");
        exit();
    }

    // Tutup koneksi
    mysqli_close($koneksi);

} else {
    // Jika bukan metode POST, alihkan
    header("Location: ../index.php?page=user_list");
    exit();
}
?>

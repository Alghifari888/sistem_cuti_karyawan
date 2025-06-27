<?php
// proses/user_tambah.php

session_start();
require_once '../config/db.php';

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil semua data dari form
    $nik = mysqli_real_escape_string($koneksi, trim($_POST['nik']));
    $password = mysqli_real_escape_string($koneksi, trim($_POST['password']));
    $nama_lengkap = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $no_telepon = mysqli_real_escape_string($koneksi, trim($_POST['no_telepon']));
    $alamat = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));
    $departemen = mysqli_real_escape_string($koneksi, trim($_POST['departemen']));
    $jabatan = mysqli_real_escape_string($koneksi, trim($_POST['jabatan']));
    $status_karyawan = mysqli_real_escape_string($koneksi, $_POST['status_karyawan']);
    $tanggal_bergabung = mysqli_real_escape_string($koneksi, $_POST['tanggal_bergabung']);
    $gaji_pokok = mysqli_real_escape_string($koneksi, $_POST['gaji_pokok']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    // --- Validasi Data ---
    $errors = [];

    // Validasi Wajib Isi
    if (empty($nik)) $errors['nik'] = "NIK tidak boleh kosong.";
    if (empty($nama_lengkap)) $errors['nama_lengkap'] = "Nama lengkap tidak boleh kosong.";
    if (empty($password)) $errors['password'] = "Password tidak boleh kosong.";

    // Cek duplikasi NIK
    $check_nik_query = "SELECT id FROM users WHERE nik = ?";
    $stmt_check_nik = mysqli_prepare($koneksi, $check_nik_query);
    mysqli_stmt_bind_param($stmt_check_nik, "s", $nik);
    mysqli_stmt_execute($stmt_check_nik);
    mysqli_stmt_store_result($stmt_check_nik);
    if (mysqli_stmt_num_rows($stmt_check_nik) > 0) {
        $errors['nik'] = "NIK ini sudah terdaftar.";
    }
    mysqli_stmt_close($stmt_check_nik);

    // Cek duplikasi Email (jika diisi)
    if (!empty($email)) {
        $check_email_query = "SELECT id FROM users WHERE email = ?";
        $stmt_check_email = mysqli_prepare($koneksi, $check_email_query);
        mysqli_stmt_bind_param($stmt_check_email, "s", $email);
        mysqli_stmt_execute($stmt_check_email);
        mysqli_stmt_store_result($stmt_check_email);
        if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
            $errors['email'] = "Email ini sudah terdaftar.";
        }
        mysqli_stmt_close($stmt_check_email);
    }

    // Jika ada error, kembali ke form
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_data'] = $_POST;
        header("Location: ../index.php?page=user_tambah");
        exit();
    }

    // --- Lanjutkan Proses Jika Tidak Ada Error ---
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Siapkan query SQL dengan semua kolom baru
    $sql = "INSERT INTO users 
            (nik, nama_lengkap, jenis_kelamin, tanggal_lahir, alamat, no_telepon, email, jabatan, departemen, tanggal_bergabung, status_karyawan, gaji_pokok, password, role) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        // --- BAGIAN YANG DIPERBAIKI ---
        // Jumlah tipe data ('sssssssssssdss') harus 14, sesuai jumlah variabel.
        mysqli_stmt_bind_param(
            $stmt, 
            "sssssssssssdss", 
            $nik, 
            $nama_lengkap, 
            $jenis_kelamin, 
            $tanggal_lahir, 
            $alamat, 
            $no_telepon, 
            $email, 
            $jabatan, 
            $departemen, 
            $tanggal_bergabung, 
            $status_karyawan, 
            $gaji_pokok, 
            $hashed_password, 
            $role
        );
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['pesan'] = "Karyawan baru berhasil ditambahkan.";
            header("Location: ../index.php?page=user_list");
            exit();
        } else {
            $_SESSION['errors']['database'] = "Gagal menyimpan data: " . mysqli_stmt_error($stmt);
            $_SESSION['old_data'] = $_POST;
            header("Location: ../index.php?page=user_tambah");
            exit();
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['errors']['database'] = "Gagal menyiapkan query: " . mysqli_error($koneksi);
        $_SESSION['old_data'] = $_POST;
        header("Location: ../index.php?page=user_tambah");
        exit();
    }

    mysqli_close($koneksi);
} else {
    header("Location: ../index.php?page=user_list");
    exit();
}
?>

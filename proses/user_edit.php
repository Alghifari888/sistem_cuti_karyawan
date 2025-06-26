<?php
// proses/user_edit.php

// Mulai session
session_start();

// Hubungkan ke database
require_once '../config/db.php';

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Pastikan request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari form
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama_lengkap = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
    $password = mysqli_real_escape_string($koneksi, trim($_POST['password']));
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $jabatan = mysqli_real_escape_string($koneksi, trim($_POST['jabatan']));
    $tanggal_bergabung = mysqli_real_escape_string($koneksi, $_POST['tanggal_bergabung']);
    $alamat = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));

    // Validasi data
    $errors = [];

    // Validasi ID
    if (empty($id) || !is_numeric($id)) {
        $_SESSION['pesan'] = "Error: ID Karyawan tidak valid.";
        header("Location: ../index.php?page=user_list");
        exit();
    }
    
    // Validasi Nama Lengkap
    if (empty($nama_lengkap)) {
        $errors['nama_lengkap'] = "Nama lengkap tidak boleh kosong.";
    }
    
    // Validasi Password (hanya jika diisi)
    if (!empty($password) && strlen($password) < 6) {
        $errors['password'] = "Password baru minimal harus 6 karakter.";
    }

    // Jika ada error, kembali ke form edit
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_data'] = $_POST; // Simpan data yang sudah diinput
        header("Location: ../index.php?page=user_edit&id=" . $id);
        exit();
    }

    // Siapkan query update
    // Cek apakah password diisi atau tidak
    if (!empty($password)) {
        // Jika password diisi, update password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET nama_lengkap = ?, jenis_kelamin = ?, alamat = ?, jabatan = ?, tanggal_bergabung = ?, password = ? WHERE id = ?";
        $types = "ssssssi";
        $params = [
            $nama_lengkap, 
            $jenis_kelamin, 
            $alamat, 
            $jabatan, 
            $tanggal_bergabung, 
            $hashed_password, 
            $id
        ];
    } else {
        // Jika password kosong, jangan update password
        $sql = "UPDATE users SET nama_lengkap = ?, jenis_kelamin = ?, alamat = ?, jabatan = ?, tanggal_bergabung = ? WHERE id = ?";
        $types = "sssssi";
         $params = [
            $nama_lengkap, 
            $jenis_kelamin, 
            $alamat, 
            $jabatan, 
            $tanggal_bergabung,
            $id
        ];
    }
    
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        // Bind parameters dinamis
        mysqli_stmt_bind_param($stmt, $types, ...$params);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['pesan'] = "Data karyawan berhasil diperbarui.";
            header("Location: ../index.php?page=user_list");
            exit();
        } else {
            $_SESSION['errors']['database'] = "Terjadi kesalahan saat memperbarui data: " . mysqli_stmt_error($stmt);
            $_SESSION['old_data'] = $_POST;
            header("Location: ../index.php?page=user_edit&id=" . $id);
            exit();
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['errors']['database'] = "Terjadi kesalahan pada sistem. Gagal menyiapkan query.";
        $_SESSION['old_data'] = $_POST;
        header("Location: ../index.php?page=user_edit&id=" . $id);
        exit();
    }

    mysqli_close($koneksi);

} else {
    // Jika bukan metode POST, alihkan
    header("Location: ../index.php?page=user_list");
    exit();
}
?>

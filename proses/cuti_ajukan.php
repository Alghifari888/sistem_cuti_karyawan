<?php
// proses/cuti_ajukan.php

// Mulai session
session_start();

// Hubungkan ke database
require_once '../config/db.php';

// Pastikan hanya user yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit();
}

// Pastikan request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari form dan session
    $user_id = $_SESSION['user_id'];
    $tanggal_mulai = mysqli_real_escape_string($koneksi, $_POST['tanggal_mulai']);
    $tanggal_selesai = mysqli_real_escape_string($koneksi, $_POST['tanggal_selesai']);
    $alasan = mysqli_real_escape_string($koneksi, trim($_POST['alasan']));

    // --- Validasi Data ---
    $errors = [];
    $today = date('Y-m-d');

    // Validasi Tanggal Mulai
    if (empty($tanggal_mulai)) {
        $errors['tanggal_mulai'] = "Tanggal mulai tidak boleh kosong.";
    } elseif ($tanggal_mulai < $today) {
        $errors['tanggal_mulai'] = "Tanggal mulai tidak boleh tanggal yang sudah lewat.";
    }

    // Validasi Tanggal Selesai
    if (empty($tanggal_selesai)) {
        $errors['tanggal_selesai'] = "Tanggal selesai tidak boleh kosong.";
    }

    // Validasi Alasan
    if (empty($alasan)) {
        $errors['alasan'] = "Alasan cuti tidak boleh kosong.";
    }
    
    // Validasi rentang tanggal (jika kedua tanggal valid)
    if (empty($errors) && $tanggal_selesai < $tanggal_mulai) {
         $errors['tanggal_selesai'] = "Tanggal selesai tidak boleh sebelum tanggal mulai.";
    }

    // Jika ada error, kembali ke form dengan pesan error
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_data'] = $_POST;
        header("Location: ../index.php?page=cuti_ajukan");
        exit();
    }
    
    // --- Lanjutkan Proses Jika Tidak Ada Error ---

    // Siapkan query SQL dengan prepared statement
    $sql = "INSERT INTO pengajuan_cuti (user_id, tanggal_mulai, tanggal_selesai, alasan, status) VALUES (?, ?, ?, ?, 'Diajukan')";

    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt, 
            "isss", 
            $user_id,
            $tanggal_mulai,
            $tanggal_selesai,
            $alasan
        );

        // Eksekusi statement
        if (mysqli_stmt_execute($stmt)) {
            // Jika berhasil, kirim pesan sukses dan alihkan ke riwayat cuti
            $_SESSION['pesan'] = "Pengajuan cuti Anda telah berhasil dikirim dan sedang menunggu persetujuan.";
            header("Location: ../index.php?page=cuti_riwayat");
            exit();
        } else {
            // Jika gagal, kirim pesan error
            $_SESSION['pesan'] = "Error: Terjadi kesalahan saat menyimpan data ke database.";
            $_SESSION['old_data'] = $_POST;
            header("Location: ../index.php?page=cuti_ajukan");
            exit();
        }

        mysqli_stmt_close($stmt);
    } else {
        // Jika statement gagal disiapkan
        $_SESSION['pesan'] = "Error: Terjadi kesalahan pada sistem. Gagal menyiapkan query.";
        $_SESSION['old_data'] = $_POST;
        header("Location: ../index.php?page=cuti_ajukan");
        exit();
    }

    mysqli_close($koneksi);

} else {
    // Jika bukan metode POST, alihkan
    header("Location: ../index.php?page=cuti_ajukan");
    exit();
}
?>

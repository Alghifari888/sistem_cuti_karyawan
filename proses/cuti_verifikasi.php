<?php
// proses/cuti_verifikasi.php

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
    $id_pengajuan = mysqli_real_escape_string($koneksi, $_POST['id_pengajuan']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);
    $catatan_admin = mysqli_real_escape_string($koneksi, trim($_POST['catatan_admin']));

    // Validasi data
    if (empty($id_pengajuan) || !is_numeric($id_pengajuan)) {
        $_SESSION['pesan'] = "Error: ID Pengajuan tidak valid.";
        header("Location: ../index.php?page=cuti_semua");
        exit();
    }

    if ($status != 'Disetujui' && $status != 'Ditolak') {
        $_SESSION['pesan'] = "Error: Status verifikasi tidak valid.";
        header("Location: ../index.php?page=cuti_verifikasi&id=" . $id_pengajuan);
        exit();
    }

    // Siapkan query untuk update status dan catatan
    $sql = "UPDATE pengajuan_cuti SET status = ?, catatan_admin = ? WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $status, $catatan_admin, $id_pengajuan);

        // Eksekusi statement
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['pesan'] = "Pengajuan cuti telah berhasil di-update menjadi '{$status}'.";
        } else {
            $_SESSION['pesan'] = "Gagal memperbarui status pengajuan: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['pesan'] = "Gagal menyiapkan query: " . mysqli_error($koneksi);
    }
    
    mysqli_close($koneksi);
    
    // Alihkan kembali ke halaman daftar semua cuti
    header("Location: ../index.php?page=cuti_semua");
    exit();

} else {
    // Jika bukan metode POST, alihkan
    header("Location: ../index.php?page=cuti_semua");
    exit();
}
?>

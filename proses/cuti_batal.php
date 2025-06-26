<?php
// proses/cuti_batal.php

// Mulai session
session_start();

// Hubungkan ke database
require_once '../config/db.php';

// Pastikan hanya user yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit();
}

// Pastikan ada parameter ID pengajuan yang dikirim
if (isset($_GET['id'])) {
    $id_pengajuan = mysqli_real_escape_string($koneksi, $_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Validasi ID, pastikan itu angka
    if (!is_numeric($id_pengajuan)) {
        header("Location: ../index.php?page=cuti_riwayat");
        exit();
    }
    
    // Siapkan query untuk menghapus data.
    // Tambahkan kondisi untuk memastikan user hanya bisa membatalkan pengajuan miliknya sendiri
    // DAN statusnya harus 'Diajukan'
    $sql = "DELETE FROM pengajuan_cuti WHERE id = ? AND user_id = ? AND status = 'Diajukan'";

    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $id_pengajuan, $user_id);

        // Eksekusi statement
        if (mysqli_stmt_execute($stmt)) {
            // Cek apakah ada baris yang benar-benar dihapus
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $_SESSION['pesan'] = "Pengajuan cuti berhasil dibatalkan.";
            } else {
                // Ini terjadi jika user mencoba membatalkan cuti yang sudah diverifikasi atau bukan miliknya
                $_SESSION['pesan'] = "Gagal membatalkan: Pengajuan tidak ditemukan atau sudah diverifikasi oleh Admin.";
            }
        } else {
            $_SESSION['pesan'] = "Error: Terjadi kesalahan saat membatalkan pengajuan.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['pesan'] = "Error: Terjadi kesalahan pada sistem.";
    }

    mysqli_close($koneksi);
    
    // Alihkan kembali ke halaman riwayat cuti
    header("Location: ../index.php?page=cuti_riwayat");
    exit();

} else {
    // Jika tidak ada ID, alihkan kembali
    header("Location: ../index.php?page=cuti_riwayat");
    exit();
}
?>

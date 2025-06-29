<?php
// proses/cuti_verifikasi.php

// Mulai session
session_start();

// Hubungkan ke database
require_once '../config/db.php';

// Cek koneksi database
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

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

    // Validasi ID pengajuan
    if (empty($id_pengajuan) || !is_numeric($id_pengajuan)) {
        $_SESSION['pesan'] = "Error: ID Pengajuan tidak valid.";
        header("Location: ../index.php?page=cuti_semua");
        exit();
    }

    // Validasi status hanya boleh 'Disetujui' atau 'Ditolak'
    if ($status != 'Disetujui' && $status != 'Ditolak') {
        $_SESSION['pesan'] = "Error: Status verifikasi tidak valid.";
        header("Location: ../index.php?page=cuti_verifikasi&id=" . $id_pengajuan);
        exit();
    }

    // Query update status dan catatan admin
    $sql = "UPDATE pengajuan_cuti SET status = ?, catatan_admin = ? WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $status, $catatan_admin, $id_pengajuan);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['pesan'] = "Pengajuan cuti berhasil diperbarui menjadi '{$status}'.";
        } else {
            $_SESSION['pesan'] = "Gagal memperbarui data: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['pesan'] = "Gagal menyiapkan query: " . mysqli_error($koneksi);
    }

    // Tutup koneksi dan redirect
    mysqli_close($koneksi);
    header("Location: ../index.php?page=cuti_semua");
    exit();

} else {
    // Jika bukan metode POST, alihkan
    header("Location: ../index.php?page=cuti_semua");
    exit();
}
?>

<?php
// proses/user_hapus.php

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

// Pastikan ada parameter ID yang dikirim melalui GET
if (isset($_GET['id'])) {
    $id_user = mysqli_real_escape_string($koneksi, $_GET['id']);

    // Validasi ID, pastikan itu angka
    if (!is_numeric($id_user)) {
        // Jika ID bukan angka, alihkan kembali
        header("Location: ../index.php?page=user_list");
        exit();
    }
    
    // Jangan biarkan admin menghapus dirinya sendiri
    if ($id_user == $_SESSION['user_id']) {
        $_SESSION['pesan'] = "Error: Anda tidak dapat menghapus akun Anda sendiri.";
        // Ganti 'alert-success' menjadi 'alert-danger' atau 'alert-warning' di user_list.php jika ingin beda warna
        header("Location: ../index.php?page=user_list");
        exit();
    }


    // Siapkan query SQL untuk menghapus data
    $sql = "DELETE FROM users WHERE id = ?";

    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_user);

        // Eksekusi statement
        if (mysqli_stmt_execute($stmt)) {
            // Jika berhasil, kirim pesan sukses dan alihkan
            // Cek apakah ada baris yang terpengaruh (dihapus)
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                 $_SESSION['pesan'] = "Data karyawan berhasil dihapus.";
            } else {
                 $_SESSION['pesan'] = "Gagal menghapus data atau karyawan tidak ditemukan.";
            }
        } else {
            // Jika gagal, kirim pesan error
            $_SESSION['pesan'] = "Terjadi kesalahan saat menghapus data: " . mysqli_stmt_error($stmt);
        }

        // Tutup statement
        mysqli_stmt_close($stmt);

    } else {
        $_SESSION['pesan'] = "Terjadi kesalahan pada sistem. Gagal menyiapkan query.";
    }

    // Tutup koneksi
    mysqli_close($koneksi);
    
    // Alihkan kembali ke halaman daftar user
    header("Location: ../index.php?page=user_list");
    exit();

} else {
    // Jika tidak ada ID, alihkan kembali
    header("Location: ../index.php?page=user_list");
    exit();
}
?>

<?php
// config/db.php

// --- Konfigurasi Database ---
$db_host = 'localhost';     // Host database, biasanya 'localhost'
$db_user = 'root';          // Username database
$db_pass = '';              // Password database
$db_name = 'db_cuti_karyawan'; // Nama database

// --- Membuat Koneksi ---
$koneksi = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// --- Cek Koneksi ---
if (!$koneksi) {
    // Jika koneksi gagal, hentikan skrip dan tampilkan pesan error
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Mengatur zona waktu default jika diperlukan
date_default_timezone_set('Asia/Jakarta');
?>

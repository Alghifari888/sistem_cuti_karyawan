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
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

date_default_timezone_set('Asia/Jakarta');

// =======================================================
// KONFIGURASI PUSHER DENGAN KREDENSIAL YANG BENAR
// =======================================================

require_once __DIR__ . '/../vendor/autoload.php';

$pusher_options = [
    'cluster' => 'ap1',
    'useTLS' => true
];

// Instansiasi Pusher dengan urutan yang benar: key, secret, app_id
$pusher = new Pusher\Pusher(
    '287ce2af6d82f8141418', // key
    'cd0620671dafb0d67dc0', // secret
    '2016460',              // app_id
    $pusher_options
);
?>
<?php
// proses/cuti_ajukan.php

// Mulai session
session_start();

// Hubungkan ke database dan konfigurasi Pusher
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

    // Validasi data (disingkat, karena logikanya sama)
    $errors = [];
    if (empty($tanggal_mulai)) { $errors['tanggal_mulai'] = "Tanggal mulai tidak boleh kosong."; }
    if (empty($tanggal_selesai)) { $errors['tanggal_selesai'] = "Tanggal selesai tidak boleh kosong."; }
    if (empty($alasan)) { $errors['alasan'] = "Alasan cuti tidak boleh kosong."; }
    if (empty($errors) && $tanggal_selesai < $tanggal_mulai) { $errors['tanggal_selesai'] = "Tanggal selesai tidak boleh sebelum tanggal mulai."; }
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_data'] = $_POST;
        header("Location: ../index.php?page=cuti_ajukan");
        exit();
    }
    
    // Siapkan query SQL untuk menyimpan data
    $sql = "INSERT INTO pengajuan_cuti (user_id, tanggal_mulai, tanggal_selesai, alasan, status) VALUES (?, ?, ?, ?, 'Diajukan')";
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "isss", $user_id, $tanggal_mulai, $tanggal_selesai, $alasan);

        // Eksekusi statement
        if (mysqli_stmt_execute($stmt)) {
            $id_pengajuan_baru = mysqli_insert_id($koneksi); // Ambil ID dari cuti yang baru saja dibuat
            $_SESSION['pesan'] = "Pengajuan cuti Anda telah berhasil dikirim.";

            // =========================================================
            // BAGIAN BARU: KIRIM NOTIFIKASI REAL-TIME KE ADMIN
            // =========================================================
            
            // 1. Ambil data lengkap dari pengajuan baru untuk dikirim ke tabel admin
            $query_new_cuti = "SELECT pc.id, u.nama_lengkap, pc.tanggal_mulai, pc.tanggal_selesai, pc.status 
                               FROM pengajuan_cuti pc JOIN users u ON pc.user_id = u.id 
                               WHERE pc.id = ?";
            $stmt_new = mysqli_prepare($koneksi, $query_new_cuti);
            mysqli_stmt_bind_param($stmt_new, "i", $id_pengajuan_baru);
            mysqli_stmt_execute($stmt_new);
            $result_new = mysqli_stmt_get_result($stmt_new);
            $data_baru = mysqli_fetch_assoc($result_new);
            
            // 2. Hitung ulang statistik untuk dashboard admin
            $query_count = "SELECT 
                (SELECT COUNT(id) FROM users WHERE role = 'user') as total_karyawan,
                (SELECT COUNT(id) FROM pengajuan_cuti WHERE MONTH(tanggal_pengajuan) = MONTH(CURDATE()) AND YEAR(tanggal_pengajuan) = YEAR(CURDATE())) as cuti_bulan_ini,
                (SELECT COUNT(id) FROM pengajuan_cuti WHERE status = 'Disetujui') as cuti_disetujui,
                (SELECT COUNT(id) FROM pengajuan_cuti WHERE status = 'Diajukan') as cuti_diajukan";
            $result_count = mysqli_query($koneksi, $query_count);
            $counts = mysqli_fetch_assoc($result_count);

            // 3. Siapkan semua data untuk dikirim ke Pusher
            $data_pusher = [
                'pengajuan_baru' => $data_baru,
                'statistik_baru' => $counts
            ];

            // 4. Kirim trigger ke channel 'admin-channel'
            global $pusher;
            $pusher->trigger('admin-channel', 'pengajuan-baru', $data_pusher);
            // =========================================================

        } else {
            $_SESSION['pesan'] = "Error: Terjadi kesalahan saat menyimpan data ke database.";
            $_SESSION['old_data'] = $_POST;
            header("Location: ../index.php?page=cuti_ajukan");
            exit();
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['pesan'] = "Error: Terjadi kesalahan pada sistem. Gagal menyiapkan query.";
        $_SESSION['old_data'] = $_POST;
        header("Location: ../index.php?page=cuti_ajukan");
        exit();
    }

    mysqli_close($koneksi);
    header("Location: ../index.php?page=cuti_riwayat");
    exit();
} else {
    header("Location: ../index.php?page=cuti_ajukan");
    exit();
}
?>
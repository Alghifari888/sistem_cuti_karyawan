<?php
// proses/cuti_verifikasi.php

// Mulai session
session_start();

// Hubungkan ke database dan konfigurasi Pusher
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
    if (empty($id_pengajuan) || !is_numeric($id_pengajuan) || ($status != 'Disetujui' && $status != 'Ditolak')) {
        $_SESSION['pesan'] = "Error: Data verifikasi tidak valid.";
        header("Location: ../index.php?page=cuti_semua");
        exit();
    }

    // Query update status dan catatan admin
    $sql = "UPDATE pengajuan_cuti SET status = ?, catatan_admin = ? WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $status, $catatan_admin, $id_pengajuan);

        // Eksekusi update
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['pesan'] = "Pengajuan cuti berhasil diperbarui menjadi '{$status}'.";

            // Ambil user_id dari pengajuan cuti yang baru saja diverifikasi
            $query_get_user = "SELECT user_id FROM pengajuan_cuti WHERE id = ?";
            $stmt_get_user = mysqli_prepare($koneksi, $query_get_user);
            mysqli_stmt_bind_param($stmt_get_user, "i", $id_pengajuan);
            mysqli_stmt_execute($stmt_get_user);
            $result_user = mysqli_stmt_get_result($stmt_get_user);
            $data_cuti = mysqli_fetch_assoc($result_user);
            $target_user_id = $data_cuti['user_id'] ?? null;

            // Jika user_id ditemukan, lanjutkan mengirim notifikasi
            if ($target_user_id) {
                // =========================================================
                // BAGIAN BARU: MENGHITUNG ULANG STATISTIK DAN MENGIRIM DATA LENGKAP
                // =========================================================

                // 1. Hitung ulang jumlah cuti untuk setiap status milik user tersebut
                $query_count = "SELECT 
                    (SELECT COUNT(id) FROM pengajuan_cuti WHERE user_id = ? AND status = 'Disetujui') as disetujui,
                    (SELECT COUNT(id) FROM pengajuan_cuti WHERE user_id = ? AND status = 'Ditolak') as ditolak,
                    (SELECT COUNT(id) FROM pengajuan_cuti WHERE user_id = ? AND status = 'Diajukan') as menunggu";
                $stmt_count = mysqli_prepare($koneksi, $query_count);
                mysqli_stmt_bind_param($stmt_count, "iii", $target_user_id, $target_user_id, $target_user_id);
                mysqli_stmt_execute($stmt_count);
                $result_count = mysqli_stmt_get_result($stmt_count);
                $counts = mysqli_fetch_assoc($result_count);

                // 2. Siapkan data lengkap untuk dikirim ke Pusher
                $data = [
                    'pesan'           => "Pengajuan cuti Anda telah di-{$status} oleh Admin.",
                    'id_pengajuan'    => $id_pengajuan,
                    'status_baru'     => $status,
                    'count_disetujui' => $counts['disetujui'],
                    'count_ditolak'   => $counts['ditolak'],
                    'count_menunggu'  => $counts['menunggu']
                ];
                
                // 3. Tentukan nama channel dan kirim trigger
                $channel_name = 'user-channel-' . $target_user_id;
                global $pusher;
                $pusher->trigger($channel_name, 'status-update', $data);
                // =========================================================
            }
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
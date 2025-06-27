<?php
// proses/user_edit.php

session_start();
require_once '../config/db.php';

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil semua data dari form
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama_lengkap = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
    $password = mysqli_real_escape_string($koneksi, trim($_POST['password']));
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $no_telepon = mysqli_real_escape_string($koneksi, trim($_POST['no_telepon']));
    $alamat = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));
    $departemen = mysqli_real_escape_string($koneksi, trim($_POST['departemen']));
    $jabatan = mysqli_real_escape_string($koneksi, trim($_POST['jabatan']));
    $status_karyawan = mysqli_real_escape_string($koneksi, $_POST['status_karyawan']);
    $tanggal_bergabung = mysqli_real_escape_string($koneksi, $_POST['tanggal_bergabung']);
    $gaji_pokok = mysqli_real_escape_string($koneksi, $_POST['gaji_pokok']);

    // Validasi
    $errors = [];
    if (empty($id) || !is_numeric($id)) {
        $_SESSION['pesan'] = "Error: ID Karyawan tidak valid.";
        header("Location: ../index.php?page=user_list");
        exit();
    }
    if (empty($nama_lengkap)) $errors['nama_lengkap'] = "Nama lengkap tidak boleh kosong.";
    if (!empty($password) && strlen($password) < 6) $errors['password'] = "Password baru minimal harus 6 karakter.";
    
    // Cek duplikasi email (jika diubah dan tidak kosong)
    if (!empty($email)) {
        $check_email_query = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt_check_email = mysqli_prepare($koneksi, $check_email_query);
        mysqli_stmt_bind_param($stmt_check_email, "si", $email, $id);
        mysqli_stmt_execute($stmt_check_email);
        mysqli_stmt_store_result($stmt_check_email);
        if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
            $errors['email'] = "Email ini sudah digunakan oleh karyawan lain.";
        }
        mysqli_stmt_close($stmt_check_email);
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_data'] = $_POST;
        header("Location: ../index.php?page=user_edit&id=" . $id);
        exit();
    }

    // Siapkan query update
    $params = [];
    if (!empty($password)) {
        // Jika password diisi, update semua termasuk password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET nama_lengkap=?, jenis_kelamin=?, tanggal_lahir=?, alamat=?, no_telepon=?, email=?, jabatan=?, departemen=?, tanggal_bergabung=?, status_karyawan=?, gaji_pokok=?, password=? WHERE id=?";
        $types = "ssssssssssdsi";
        $params = [$nama_lengkap, $jenis_kelamin, $tanggal_lahir, $alamat, $no_telepon, $email, $jabatan, $departemen, $tanggal_bergabung, $status_karyawan, $gaji_pokok, $hashed_password, $id];
    } else {
        // Jika password kosong, update semua kecuali password
        $sql = "UPDATE users SET nama_lengkap=?, jenis_kelamin=?, tanggal_lahir=?, alamat=?, no_telepon=?, email=?, jabatan=?, departemen=?, tanggal_bergabung=?, status_karyawan=?, gaji_pokok=? WHERE id=?";
        $types = "ssssssssssdi";
        $params = [$nama_lengkap, $jenis_kelamin, $tanggal_lahir, $alamat, $no_telepon, $email, $jabatan, $departemen, $tanggal_bergabung, $status_karyawan, $gaji_pokok, $id];
    }
    
    $stmt = mysqli_prepare($koneksi, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['pesan'] = "Data karyawan berhasil diperbarui.";
            header("Location: ../index.php?page=user_list");
            exit();
        } else {
            $_SESSION['errors']['database'] = "Gagal memperbarui data: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['errors']['database'] = "Gagal menyiapkan query: " . mysqli_error($koneksi);
    }
    
    $_SESSION['old_data'] = $_POST;
    header("Location: ../index.php?page=user_edit&id=" . $id);
    exit();

} else {
    header("Location: ../index.php?page=user_list");
    exit();
}
?>

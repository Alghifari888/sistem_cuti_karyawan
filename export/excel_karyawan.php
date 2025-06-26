<?php
// export/excel_karyawan.php

// Mulai session untuk memastikan hanya admin yang bisa akses
session_start();

// Hubungkan ke database
require_once '../config/db.php';

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak. Anda harus login sebagai admin.");
}

// Nama file excel yang akan di-download
$filename = "Data_Karyawan_" . date('Ymd') . ".xls";

// Set header untuk memberitahu browser bahwa ini adalah file excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Query untuk mengambil semua data karyawan
$query = "SELECT nik, nama_lengkap, jenis_kelamin, alamat, jabatan, tanggal_bergabung FROM users WHERE role = 'user' ORDER BY nik ASC";
$result = mysqli_query($koneksi, $query);

// Membuat tabel HTML yang akan menjadi isi file Excel
echo '<table border="1">';
// Header tabel
echo '<thead>
        <tr>
            <th>No</th>
            <th>NIK</th>
            <th>Nama Lengkap</th>
            <th>Jenis Kelamin</th>
            <th>Alamat</th>
            <th>Jabatan</th>
            <th>Tanggal Bergabung</th>
        </tr>
      </thead>';

// Isi tabel
echo '<tbody>';
if (mysqli_num_rows($result) > 0) {
    $no = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>
                <td>' . $no++ . '</td>
                <td>\'' . htmlspecialchars($row['nik']) . '</td>
                <td>' . htmlspecialchars($row['nama_lengkap']) . '</td>
                <td>' . ($row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan') . '</td>
                <td>' . htmlspecialchars($row['alamat']) . '</td>
                <td>' . htmlspecialchars($row['jabatan']) . '</td>
                <td>' . date('d/m/Y', strtotime($row['tanggal_bergabung'])) . '</td>
              </tr>';
    }
} else {
    echo '<tr><td colspan="7">Tidak ada data karyawan.</td></tr>';
}
echo '</tbody>';

echo '</table>';

// Tutup koneksi
mysqli_close($koneksi);
exit();
?>

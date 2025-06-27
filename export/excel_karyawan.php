<?php
// export/excel_karyawan.php

session_start();
require_once '../config/db.php';

// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak. Anda harus login sebagai admin.");
}

$filename = "Data_Karyawan_" . date('Ymd') . ".xls";

// Set header untuk memberitahu browser bahwa ini adalah file excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Query untuk mengambil semua data user dengan kolom-kolom baru
$query = "SELECT nik, nama_lengkap, jenis_kelamin, tanggal_lahir, alamat, no_telepon, email, jabatan, departemen, tanggal_bergabung, status_karyawan, gaji_pokok 
          FROM users 
          WHERE role = 'user' 
          ORDER BY nik ASC";
$result = mysqli_query($koneksi, $query);

// Membuat tabel HTML untuk isi file Excel
echo '<table border="1">';
// Header tabel
echo '<thead>
        <tr>
            <th>No</th>
            <th>NIK</th>
            <th>Nama Lengkap</th>
            <th>Jenis Kelamin</th>
            <th>Tanggal Lahir</th>
            <th>Departemen</th>
            <th>Jabatan</th>
            <th>Email</th>
            <th>No. Telepon</th>
            <th>Alamat</th>
            <th>Tanggal Bergabung</th>
            <th>Gaji Pokok</th>
            <th>Status Karyawan</th>
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
                <td>' . date('d/m/Y', strtotime($row['tanggal_lahir'])) . '</td>
                <td>' . htmlspecialchars($row['departemen']) . '</td>
                <td>' . htmlspecialchars($row['jabatan']) . '</td>
                <td>' . htmlspecialchars($row['email']) . '</td>
                <td>\'' . htmlspecialchars($row['no_telepon']) . '</td>
                <td>' . htmlspecialchars($row['alamat']) . '</td>
                <td>' . date('d/m/Y', strtotime($row['tanggal_bergabung'])) . '</td>
                <td>' . $row['gaji_pokok'] . '</td>
                <td>' . htmlspecialchars($row['status_karyawan']) . '</td>
              </tr>';
    }
} else {
    echo '<tr><td colspan="13">Tidak ada data karyawan.</td></tr>';
}
echo '</tbody>';
echo '</table>';

mysqli_close($koneksi);
exit();
?>

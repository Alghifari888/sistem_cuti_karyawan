<?php
// pages/user_list.php

// Pastikan tidak ada yang bisa mengakses file ini secara langsung
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Akses ditolak.');
}

// Query untuk mengambil semua data user dengan kolom-kolom baru
$query = "SELECT id, nik, nama_lengkap, jenis_kelamin, tanggal_lahir, alamat, no_telepon, email, jabatan, departemen, tanggal_bergabung, status_karyawan, gaji_pokok 
          FROM users 
          WHERE role = 'user' 
          ORDER BY nama_lengkap ASC";
$result = mysqli_query($koneksi, $query);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Daftar Karyawan</h4>
    <div class="btn-group">
        <a href="export/excel_karyawan.php" class="btn btn-success">
            <i class="bi bi-file-earmark-excel-fill"></i> Export ke Excel
        </a>
        <a href="index.php?page=user_tambah" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Karyawan
        </a>
    </div>
</div>

<?php 
// Tampilkan notifikasi jika ada
if (isset($_SESSION['pesan'])) {
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            {$_SESSION['pesan']}
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
    unset($_SESSION['pesan']); // Hapus session setelah ditampilkan
}
?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">No</th>
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
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="text-center"><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['nik']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                <td><?php echo ($row['jenis_kelamin'] == 'L') ? 'L' : 'P'; ?></td>
                                <td><?php echo date('d M Y', strtotime($row['tanggal_lahir'])); ?></td>
                                <td><?php echo htmlspecialchars($row['departemen']); ?></td>
                                <td><?php echo htmlspecialchars($row['jabatan']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['no_telepon']); ?></td>
                                <td style="min-width: 200px;"><?php echo htmlspecialchars($row['alamat']); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['tanggal_bergabung'])); ?></td>
                                <td>Rp <?php echo number_format($row['gaji_pokok'], 0, ',', '.'); ?></td>
                                <td>
                                    <?php 
                                    $status_class = 'bg-secondary'; // Default
                                    if ($row['status_karyawan'] == 'Aktif') $status_class = 'bg-success';
                                    elseif ($row['status_karyawan'] == 'Tidak Aktif' || $row['status_karyawan'] == 'Resign') $status_class = 'bg-danger';
                                    elseif ($row['status_karyawan'] == 'Cuti') $status_class = 'bg-info';
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($row['status_karyawan']); ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?page=user_edit&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning me-2" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="proses/user_hapus.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?');" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="14" class="text-center">Belum ada data karyawan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

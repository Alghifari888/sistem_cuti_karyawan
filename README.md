Sistem Informasi Manajemen Cuti Karyawan (PHP Native)
Aplikasi web sederhana berbasis PHP Native untuk mengelola proses pengajuan dan persetujuan cuti karyawan. Aplikasi ini dibangun tanpa menggunakan framework PHP, hanya mengandalkan PHP Native, MySQL sebagai database, dan Bootstrap 5 untuk tampilan antarmuka yang responsif. Proyek ini cocok untuk dipelajari oleh pemula yang ingin memahami konsep dasar pengembangan web dengan PHP.

✨ Fitur Utama
Aplikasi ini memiliki dua hak akses (role) dengan fitur yang berbeda: Admin dan Karyawan (User).

👨‍💼 Fitur untuk Admin
Dashboard Admin: Menampilkan ringkasan statistik seperti total karyawan, jumlah pengajuan cuti bulan ini, total cuti yang disetujui, dan yang masih menunggu konfirmasi.

Manajemen Karyawan (CRUD):

Create: Menambah data karyawan baru. NIK dan tanggal bergabung dapat terisi otomatis.

Read: Melihat daftar semua karyawan dalam bentuk tabel.

Update: Mengubah data karyawan.

Delete: Menghapus data karyawan.

Manajemen Cuti:

Melihat semua riwayat pengajuan cuti dari seluruh karyawan, diurutkan berdasarkan status (pengajuan baru paling atas).

Melihat detail setiap pengajuan cuti.

Verifikasi Cuti: Menyetujui atau menolak pengajuan cuti dan memberikan catatan.

Export Data: Mengekspor daftar data karyawan ke dalam format file Microsoft Excel (.xls).

👩‍🔧 Fitur untuk Karyawan (User)
Dashboard User: Menampilkan ringkasan status pengajuan cuti pribadi (total diajukan, disetujui, ditolak, menunggu).

Pengajuan Cuti: Mengisi dan mengirimkan formulir pengajuan cuti baru.

Riwayat Cuti:

Melihat seluruh riwayat pengajuan cuti pribadi beserta statusnya.

Melihat catatan dari admin terkait pengajuan yang disetujui atau ditolak.

Membatalkan Cuti: Dapat membatalkan pengajuan cuti selama statusnya masih "Diajukan".

Manajemen Profil:

Melihat data pribadi.

Mengubah password untuk keamanan akun.

🛠️ Teknologi yang Digunakan
Backend: PHP 8.x (Native, tanpa framework)

Database: MySQL / MariaDB

Frontend: HTML, CSS

UI Framework: Bootstrap 5.3

Icons: Bootstrap Icons

📋 Spesifikasi & Kebutuhan Sistem
Web Server: Apache (disarankan menggunakan XAMPP atau WAMP).

PHP: Versi 8.0 atau lebih tinggi.

Database Server: MySQL atau MariaDB.

Web Browser: Google Chrome, Firefox, atau browser modern lainnya.

🚀 Cara Instalasi & Konfigurasi
Ikuti langkah-langkah berikut untuk menjalankan aplikasi ini di komputer lokal Anda.

1. Unduh atau Clone Proyek
Unduh dan ekstrak file ZIP proyek ini, atau clone repositori jika menggunakan Git.

Letakkan seluruh folder proyek (misalnya cuti-karyawan) ke dalam direktori htdocs di dalam folder instalasi XAMPP Anda. (Contoh: C:\xampp\htdocs\cuti-karyawan)

2. Buat Database
Buka phpMyAdmin melalui browser (http://localhost/phpmyadmin).

Buat database baru dengan nama db_cuti_karyawan.

Pilih database yang baru dibuat, lalu buka tab SQL.

Salin dan tempel seluruh kode SQL di bawah ini, lalu klik Go atau Kirim.

-- Membuat Database (jika belum ada)
CREATE DATABASE IF NOT EXISTS db_cuti_karyawan;

-- Menggunakan Database
USE db_cuti_karyawan;

-- Tabel 1: users
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nik` CHAR(5) NOT NULL UNIQUE,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `jenis_kelamin` ENUM('L', 'P') NOT NULL,
  `alamat` TEXT,
  `jabatan` VARCHAR(50) DEFAULT NULL,
  `tanggal_bergabung` DATE DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'user') NOT NULL
) ENGINE=InnoDB;

-- Tabel 2: pengajuan_cuti
CREATE TABLE `pengajuan_cuti` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `tanggal_mulai` DATE NOT NULL,
  `tanggal_selesai` DATE NOT NULL,
  `alasan` TEXT NOT NULL,
  `status` ENUM('Diajukan', 'Disetujui', 'Ditolak') NOT NULL DEFAULT 'Diajukan',
  `tanggal_pengajuan` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `catatan_admin` TEXT DEFAULT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Membuat Akun Admin Default
-- NIK: admin | Password: password
INSERT INTO `users` (`nik`, `nama_lengkap`, `jenis_kelamin`, `password`, `role`) VALUES
('admin', 'Administrator', 'L', '$2y$10$iCi.C.C9A141c2E5P7d3guU0YmJ9V/r0M8aO4doqKAjFz.a/zKP.S', 'admin');

Catatan: Kode SQL di atas juga akan otomatis membuat satu akun Admin default untuk login pertama kali.

3. Konfigurasi Koneksi Database
Buka file config/db.php.

Sesuaikan konfigurasi berikut dengan pengaturan database Anda (jika berbeda dari default XAMPP).

$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; // Biasanya kosong secara default
$db_name = 'db_cuti_karyawan';

4. Jalankan Aplikasi
Pastikan Web Server (Apache) dan Database (MySQL) Anda berjalan dari control panel XAMPP.

Buka web browser Anda.

Akses aplikasi melalui URL: http://localhost/cuti-karyawan/

Anda akan diarahkan ke halaman login.

5. Login ke Aplikasi
Login sebagai Admin:

NIK: admin

Password: password

Login sebagai User:

Tambahkan user baru melalui dashboard Admin, lalu gunakan NIK dan password yang telah dibuat untuk login.

📁 Struktur Folder dan File (Detail)
Berikut adalah penjelasan rinci mengenai setiap file dalam proyek.

/cuti-karyawan/
│
├── config/
│   └── db.php              # File konfigurasi koneksi ke database.
│
├── auth/
│   ├── login.php           # Halaman form login.
│   ├── proses_login.php    # Logika untuk verifikasi login dan pembuatan session.
│   └── logout.php          # Logika untuk menghapus session dan logout.
│
├── layout/
│   ├── header.php          # Bagian atas HTML (termasuk <head> dan navbar).
│   ├── sidebar.php         # Menu navigasi samping yang dinamis sesuai role.
│   └── footer.php          # Bagian bawah HTML (penutup tag dan script JS).
│
├── pages/
│   ├── dashboard_admin.php # Tampilan dashboard untuk Admin.
│   ├── dashboard_user.php  # Tampilan dashboard untuk User.
│   ├── user_list.php       # Halaman daftar semua karyawan (Admin).
│   ├── user_tambah.php     # Halaman form tambah karyawan (Admin).
│   ├── user_edit.php       # Halaman form edit karyawan (Admin).
│   ├── cuti_ajukan.php     # Halaman form pengajuan cuti (User).
│   ├── cuti_riwayat.php    # Halaman riwayat cuti (User).
│   ├── cuti_semua.php      # Halaman daftar semua cuti (Admin).
│   ├── cuti_verifikasi.php # Halaman detail dan verifikasi cuti (Admin).
│   └── profil.php          # Halaman profil dan ubah password (User).
│
├── proses/
│   ├── user_tambah.php     # Proses menyimpan karyawan baru.
│   ├── user_edit.php       # Proses update data karyawan.
│   ├── user_hapus.php      # Proses menghapus karyawan.
│   ├── cuti_ajukan.php     # Proses menyimpan pengajuan cuti baru.
│   ├── cuti_batal.php      # Proses membatalkan pengajuan cuti oleh user.
│   ├── cuti_verifikasi.php # Proses verifikasi (setuju/tolak) cuti oleh admin.
│   └── profil_update.php   # Proses update password oleh user.
│
├── export/
│   └── excel_karyawan.php  # Proses untuk generate file Excel data karyawan.
│
└── index.php               # File utama sebagai router untuk memuat semua halaman.

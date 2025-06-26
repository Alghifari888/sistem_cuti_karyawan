# 💼 Sistem Informasi Manajemen Cuti Karyawan (PHP Native)

Sistem Informasi Manajemen Cuti Karyawan adalah aplikasi web sederhana berbasis **PHP Native** (tanpa framework) yang dirancang untuk mengelola proses **pengajuan dan persetujuan cuti karyawan**. Aplikasi ini menggunakan **MySQL** sebagai basis data dan **Bootstrap 5** untuk antarmuka pengguna yang responsif. Cocok digunakan sebagai pembelajaran bagi pemula yang ingin memahami konsep dasar pengembangan web menggunakan PHP.

---

![admin Dashboard](gambar1.png)
![user Dashboard](gambar2.png)

## ✨ Fitur Utama

Aplikasi memiliki dua jenis role dengan akses berbeda:

### 👨‍💼 Admin

* **Dashboard**: Menampilkan statistik seperti total karyawan, pengajuan cuti bulan ini, jumlah cuti disetujui, dan menunggu persetujuan.
* **Manajemen Karyawan (CRUD)**:

  * Tambah, lihat, edit, dan hapus data karyawan.
  * NIK dan tanggal bergabung terisi otomatis saat input.
* **Manajemen Pengajuan Cuti**:

  * Lihat semua pengajuan cuti.
  * Detail pengajuan + catatan.
  * Verifikasi cuti: Setujui atau tolak.
* **Export Data**:

  * Ekspor data karyawan ke file Excel (`.xls`).

### 👩‍🔧 Karyawan (User)

* **Dashboard Pribadi**: Ringkasan pengajuan cuti (diajukan, disetujui, ditolak, menunggu).
* **Pengajuan Cuti**: Isi formulir pengajuan cuti baru.
* **Riwayat Cuti**:

  * Lihat histori dan status pengajuan.
  * Lihat catatan dari admin.
  * Batalkan pengajuan selama status masih “Diajukan”.
* **Manajemen Profil**:

  * Lihat data pribadi.
  * Ubah password.

---

## 🛠️ Teknologi yang Digunakan

| Komponen     | Teknologi        |
| ------------ | ---------------- |
| Backend      | PHP 8.x (Native) |
| Database     | MySQL / MariaDB  |
| Frontend     | HTML, CSS        |
| UI Framework | Bootstrap 5.3    |
| Icon Set     | Bootstrap Icons  |

---

## 📋 Spesifikasi Sistem

* Web Server: Apache (disarankan: XAMPP / WAMP)
* PHP: Versi 8.0 atau lebih tinggi
* Database Server: MySQL / MariaDB
* Browser: Google Chrome, Firefox, atau browser modern lainnya

---

## 🚀 Panduan Instalasi

### 1. Unduh / Clone Proyek

* Letakkan folder proyek (misal: `cuti-karyawan`) di dalam direktori `htdocs` XAMPP.
  **Contoh**: `C:\xampp\htdocs\cuti-karyawan`

### 2. Buat Database

1. Buka `http://localhost/phpmyadmin`
2. Buat database baru: `db_cuti_karyawan`
3. Jalankan skrip SQL berikut:

```sql
CREATE DATABASE IF NOT EXISTS db_cuti_karyawan;
USE db_cuti_karyawan;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nik` CHAR(5) NOT NULL UNIQUE,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `jenis_kelamin` ENUM('L', 'P') NOT NULL,
  `alamat` TEXT,
  `jabatan` VARCHAR(50),
  `tanggal_bergabung` DATE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'user') NOT NULL
) ENGINE=InnoDB;

CREATE TABLE `pengajuan_cuti` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `tanggal_mulai` DATE NOT NULL,
  `tanggal_selesai` DATE NOT NULL,
  `alasan` TEXT NOT NULL,
  `status` ENUM('Diajukan', 'Disetujui', 'Ditolak') NOT NULL DEFAULT 'Diajukan',
  `tanggal_pengajuan` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `catatan_admin` TEXT,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Akun admin default
INSERT INTO `users` (`nik`, `nama_lengkap`, `jenis_kelamin`, `password`, `role`) VALUES
('admin', 'Administrator', 'L', '$2y$10$iCi.C.C9A141c2E5P7d3guU0YmJ9V/r0M8aO4doqKAjFz.a/zKP.S', 'admin');
```

### 3. Konfigurasi Database

Edit file `config/db.php`:

```php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; // kosong default XAMPP
$db_name = 'db_cuti_karyawan';
```

### 4. Jalankan Aplikasi

1. Aktifkan Apache & MySQL via XAMPP
2. Buka browser: `http://localhost/cuti-karyawan/`

### 5. Login

* **Admin**

  * NIK: `10002`
  * Password: `admin1234`
* **User**
* NIK: `10005`
* Password: `user123`

  * Tambahkan user baru via dashboard Admin.

---

## 📁 Struktur Folder & File

```
cuti-karyawan/
│
├── config/
│   └── db.php               # Konfigurasi koneksi database
│
├── auth/
│   ├── login.php            # Form login
│   ├── proses_login.php     # Proses login
│   └── logout.php           # Logout
│
├── layout/
│   ├── header.php           # Header + Navbar
│   ├── sidebar.php          # Sidebar navigasi
│   └── footer.php           # Footer + JS
│
├── pages/
│   ├── dashboard_admin.php  # Dashboard Admin
│   ├── dashboard_user.php   # Dashboard User
│   ├── user_list.php        # Daftar karyawan
│   ├── user_tambah.php      # Tambah karyawan
│   ├── user_edit.php        # Edit karyawan
│   ├── cuti_ajukan.php      # Ajukan cuti
│   ├── cuti_riwayat.php     # Riwayat cuti (User)
│   ├── cuti_semua.php       # Semua cuti (Admin)
│   ├── cuti_verifikasi.php  # Verifikasi cuti
│   └── profil.php           # Profil dan ganti password
│
├── proses/
│   ├── user_tambah.php      # Proses tambah karyawan
│   ├── user_edit.php        # Proses edit karyawan
│   ├── user_hapus.php       # Hapus karyawan
│   ├── cuti_ajukan.php      # Proses ajukan cuti
│   ├── cuti_batal.php       # Batalkan cuti
│   ├── cuti_verifikasi.php  # Verifikasi admin
│   └── profil_update.php    # Update password
│
├── export/
│   └── excel_karyawan.php   # Ekspor Excel
│
└── index.php                # Router utama
```

---

## 📣 Kontribusi

Kontribusi sangat terbuka untuk pengembangan lebih lanjut!


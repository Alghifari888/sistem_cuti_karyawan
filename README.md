# 💼 Sistem Informasi Manajemen Cuti Karyawan

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




## 📣 Panduan Kontribusi

Kontribusi terhadap proyek ini sangat terbuka! Ada dua cara utama untuk berkontribusi:
Berikut adalah versi lengkap dan terperinci dari bagian **📣 Kontribusi** yang menjelaskan secara **detail dan aman** cara berkontribusi melalui **fork** maupun sebagai **kolaborator langsung** (misalnya di branch `Views`).
---



### 🚀 A. Sebagai Kolaborator Langsung (Sudah Diundang)

Jika kamu **sudah diundang sebagai kolaborator GitHub**, ikuti panduan ini untuk berkontribusi secara langsung tanpa perlu fork.

#### 1. Clone repository ke komputer kamu

```bash
git clone https://github.com/Alghifari888/sistem_cuti_karyawan.git
cd sistem_cuti_karyawan
```

#### 2. Checkout ke branch `Views` (branch tempat pengembangan UI dilakukan)

```bash
git checkout -b Views origin/Views
```

#### 3. Lakukan perubahan (tambah fitur, edit tampilan, dsb)

Setelah selesai edit:

```bash
git add .
git commit -m "Deskripsi perubahan yang dilakukan"
```

#### 4. Push ke branch `Views` di GitHub

```bash
git push origin Views
```

#### 5. (Opsional) Buka Pull Request ke `main` (jika perubahan besar) (Konfirmasi Jika Melakukan nya Ke Alghifari888)

> Meskipun kamu bisa push langsung ke `Views`, lebih baik ajukan **Pull Request** (PR) ke `main` agar perubahan bisa direview dulu sebelum digabung.

#### 6. Selalu sinkronkan branch sebelum mulai kerja

```bash
git pull origin Views
```

---

#### 🖥️ **Langkah Menjalankan Proyek Secara Lokal Setelah Clone**

Setelah clone & checkout branch `Views`, kamu bisa langsung **menjalankan aplikasi di localhost** untuk melihat hasilnya secara langsung. Berikut langkahnya:

##### a. Letakkan folder di direktori `htdocs` (jika pakai XAMPP)

Contoh di Windows:

```bash
mv sistem_cuti_karyawan C:/xampp/htdocs/
```

Atau langsung clone di sana:

```bash
cd C:/xampp/htdocs
git clone https://github.com/Alghifari888/sistem_cuti_karyawan.git
cd sistem_cuti_karyawan
git checkout -b Views origin/Views
```

##### b. Buat database di phpMyAdmin

1. Buka `http://localhost/phpmyadmin`
2. Buat database baru dengan nama:

   ```
   db_cuti_karyawan
   ```
3. Jalankan SQL setup yang ada di README (atau file `database.sql` jika tersedia)

##### c. Konfigurasi koneksi database

Buka file `config/db.php` dan pastikan isi seperti ini:

```php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; // default XAMPP
$db_name = 'db_cuti_karyawan';
```

##### d. Jalankan aplikasi di browser

1. Aktifkan **Apache & MySQL** di XAMPP
2. Akses aplikasi di browser:

   ```
   http://localhost/sistem_cuti_karyawan/
   ```

##### e. Login ke aplikasi

Gunakan akun bawaan:

* **Admin**

  * NIK: `admin`
  * Password: `admin1234`

---

---

### 🪄 B. Melalui Fork (Jika Belum Jadi Kolaborator)

Kamu bisa tetap ikut berkontribusi meskipun belum diundang sebagai kolaborator dengan cara melakukan *fork*:

#### 1. Fork repositori ini

* Buka [repo ini](https://github.com/Alghifari888/sistem_cuti_karyawan)
* Klik tombol **"Fork"** (kanan atas)
* Repo akan tersalin ke akun GitHub kamu

#### 2. Clone repo hasil fork ke lokal

```bash
git clone https://github.com/USERNAME-KAMU/sistem_cuti_karyawan.git
cd sistem_cuti_karyawan
```

#### 3. Buat branch baru untuk fitur/perubahanmu

```bash
git checkout -b fitur-nama-fitur
```

Contoh:

```bash
git checkout -b fitur-export-pdf
```

#### 4. Lakukan perubahan → commit → push ke branch di repo kamu

```bash
git add .
git commit -m "Tambah fitur export PDF laporan cuti"
git push origin fitur-nama-fitur
```

#### 5. Buka Pull Request ke repo utama

* Buka repo fork kamu di GitHub
* Klik tombol **"Contribute" → "Open Pull Request"**
* Pilih base: `main` atau `Views` dari repo `Alghifari888`
* Pilih compare: branch milikmu (misalnya `fitur-export-pdf`)
* Berikan deskripsi yang jelas

---

## ✅ Pedoman Kontribusi

* Gunakan **branch per fitur** (bukan langsung edit `main`)
* Deskripsikan commit dengan **jelas dan ringkas**
* Gunakan format commit yang konsisten, contoh:

  * `fitur: Tambah form cetak slip cuti`
  * `fix: Perbaikan validasi form login`
  * `refactor: Pecah layout jadi file terpisah`
* Selalu lakukan `git pull` sebelum `push` untuk menghindari konflik
* Uji aplikasi secara lokal sebelum push atau PR

---

## 📄 Contoh Alur Fork dan Pull Request

```bash
# Clone repo hasil fork
git clone https://github.com/USERNAME-KAMU/sistem_cuti_karyawan.git
cd sistem_cuti_karyawan

# Buat branch baru
git checkout -b fitur-tampilan-baru

# Edit → commit → push
git add .
git commit -m "Tambah UI tabel cuti dengan style Bootstrap"
git push origin fitur-tampilan-baru
```

Lalu buka GitHub → buat pull request ke:

* Base repo: `Alghifari888/sistem_cuti_karyawan`
* Branch: `Views` (kalau kamu kerja di tampilan) atau `main` (fitur umum)

---




-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 27 Jun 2025 pada 06.49
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_cuti_karyawan`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan_cuti`
--

CREATE TABLE `pengajuan_cuti` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `alasan` text NOT NULL,
  `status` enum('Diajukan','Disetujui','Ditolak') NOT NULL DEFAULT 'Diajukan',
  `tanggal_pengajuan` timestamp NOT NULL DEFAULT current_timestamp(),
  `catatan_admin` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengajuan_cuti`
--

INSERT INTO `pengajuan_cuti` (`id`, `user_id`, `tanggal_mulai`, `tanggal_selesai`, `alasan`, `status`, `tanggal_pengajuan`, `catatan_admin`) VALUES
(1, 1, '2025-07-01', '2025-07-05', 'Liburan keluarga', 'Diajukan', '2025-06-25 20:35:40', NULL),
(5, 5, '2025-06-27', '2025-06-30', 'anak saya lagi di rawat', 'Disetujui', '2025-06-26 00:01:45', 'saya setujui');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nik` char(5) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `jabatan` varchar(50) DEFAULT NULL,
  `departemen` varchar(50) DEFAULT NULL,
  `tanggal_bergabung` date DEFAULT NULL,
  `status_karyawan` enum('Aktif','Tidak Aktif','Cuti','Resign') NOT NULL DEFAULT 'Aktif',
  `gaji_pokok` decimal(10,2) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nik`, `nama_lengkap`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `no_telepon`, `email`, `jabatan`, `departemen`, `tanggal_bergabung`, `status_karyawan`, `gaji_pokok`, `password`, `role`) VALUES
(1, '10001', 'Andi Saputra', 'L', NULL, 'Jl. Merdeka No. 1', NULL, NULL, 'Staff IT', NULL, '2022-01-10', 'Aktif', NULL, '$2y$10$8jljQvMcE.Rq3oVWW9k5E.kHdS6NsezHRjqGiZ/d9KHqSYIqqJwBi', 'user'),
(2, '10002', 'Siti Nurhaliza', 'P', NULL, 'Jl. Melati No. 3', NULL, NULL, 'HRD', NULL, '2021-08-15', 'Aktif', NULL, '$2a$12$fbtFo62AhaGxRTkTVmfKXuHCoVlZcz01FGFTQ5IDFWlX9l/fE69yO', 'admin'),
(5, '10005', 'ahmad basuri', 'L', NULL, 'Jakarta Barat, Tomang', NULL, NULL, 'operator produksi', NULL, '2025-06-26', 'Aktif', NULL, '$2y$10$itODpkpqPKc1IGMgd3pYYuONAOlF8SWLjAHJfnu6fdhzDi5fr4Szu', 'user');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `pengajuan_cuti`
--
ALTER TABLE `pengajuan_cuti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `pengajuan_cuti`
--
ALTER TABLE `pengajuan_cuti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pengajuan_cuti`
--
ALTER TABLE `pengajuan_cuti`
  ADD CONSTRAINT `pengajuan_cuti_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

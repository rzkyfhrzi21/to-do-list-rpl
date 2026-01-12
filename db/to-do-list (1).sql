-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 09 Jan 2026 pada 17.23
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
-- Database: `to-do-list`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `hari`
--

CREATE TABLE `hari` (
  `id_hari` bigint(20) NOT NULL,
  `nama_hari` varchar(20) NOT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `hari`
--

INSERT INTO `hari` (`id_hari`, `nama_hari`, `tanggal`) VALUES
(3, 'Jumat', '2026-01-09'),
(2, 'Kamis', '2026-01-08'),
(1, 'Rabu', '2026-01-07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `keterangan`
--

CREATE TABLE `keterangan` (
  `id_keterangan` bigint(20) NOT NULL,
  `jenis_pekerjaan` enum('Keseluruhan','Sebagian','Perbaikan') NOT NULL,
  `deskripsi_keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `keterangan`
--

INSERT INTO `keterangan` (`id_keterangan`, `jenis_pekerjaan`, `deskripsi_keterangan`) VALUES
(1, 'Keseluruhan', 'Mengerjakan fitur CRUD tugas dan validasi form.'),
(2, 'Sebagian', 'Membuat tampilan list tugas dan filter status.'),
(3, 'Perbaikan', 'Perbaikan bug penjadwalan tugas berdasarkan masukan dosen.');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `task_id` bigint(20) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `task_id`, `title`, `message`, `is_read`, `created_at`, `updated_at`) VALUES
(5, 2, 6, 'Kerjakan Task 1', 'Ayo Selesaikan sebelum waktu deadline', 0, '2026-01-07 01:26:58', '2026-01-07 01:26:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` bigint(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `nama`, `username`, `password`) VALUES
(1, 'Adinata', 'adinata1', 'adinata1'),
(4, '', 'ADINATA11', 'ADINATA11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reminder_log`
--

CREATE TABLE `reminder_log` (
  `id_log` bigint(20) NOT NULL,
  `id_tugas` bigint(20) NOT NULL,
  `reminder_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tasks`
--

CREATE TABLE `tasks` (
  `id_task` bigint(20) NOT NULL,
  `id_user` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `priority` enum('rendah','sedang','tinggi') DEFAULT 'sedang',
  `status` enum('belum','selesai') DEFAULT 'belum',
  `deadline` datetime DEFAULT NULL,
  `reminder` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tasks`
--

INSERT INTO `tasks` (`id_task`, `id_user`, `name`, `description`, `category`, `priority`, `status`, `deadline`, `reminder`, `created_at`, `updated_at`) VALUES
(6, 2, 'Tugas 1', 'Mengerjakan tugas 1', 'Kuliah', 'rendah', 'belum', '2025-01-11 00:01:00', '2025-12-31 00:01:00', '2025-12-30 02:58:08', '2025-12-30 03:55:20'),
(8, 2, 'Tugas 2', 'Mengerjakan tugas 2', 'Kuliah', 'sedang', 'belum', '2025-12-31 23:59:00', '2025-12-31 20:00:00', '2025-12-30 03:26:25', '2025-12-30 03:55:46'),
(10, 2, 'Tugas 3', 'Mengerjakan tugas 3', 'Belajar', 'tinggi', 'belum', '2026-01-02 10:00:00', '2026-01-02 08:30:00', '2025-12-30 03:26:25', '2026-01-06 23:44:33'),
(13, 2, 'Task 4', 'Task 4', 'Kuliah', 'tinggi', 'belum', '2026-01-07 22:44:00', '2026-01-07 06:43:00', '2026-01-06 23:43:56', '2026-01-06 23:58:25');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tugas`
--

CREATE TABLE `tugas` (
  `id_tugas` bigint(20) NOT NULL,
  `nama_tugas` varchar(255) NOT NULL,
  `deskripsi_tugas` text DEFAULT NULL,
  `id_pengguna` bigint(20) NOT NULL,
  `id_waktu` bigint(20) NOT NULL,
  `id_hari` bigint(20) NOT NULL,
  `id_keterangan` bigint(20) DEFAULT NULL,
  `status_tugas` enum('belum','selesai') DEFAULT 'belum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tugas`
--

INSERT INTO `tugas` (`id_tugas`, `nama_tugas`, `deskripsi_tugas`, `id_pengguna`, `id_waktu`, `id_hari`, `id_keterangan`, `status_tugas`) VALUES
(1, 'Kerjakan ERD & Relasi', 'Menyusun relasi tabel pengguna-tugas-waktu-hari-keterangan.', 1, 1, 1, 1, 'belum'),
(7, 'Kerjakan ERD & Relasi1', 'aaaaaaaaaaaaaaaaaaaa', 1, 3, 3, 3, 'belum'),
(9, 'Kerjakan ERD & Relasi1', '1111111111', 1, 2, 1, 3, 'belum'),
(19, 'kok masih', 'asdd', 1, 2, 3, 2, 'belum'),
(20, 'nyoba lagi', 'eaaa', 1, 1, 2, 1, 'belum'),
(21, 'hljkhlkjh', 'khfhkfkhf', 1, 1, 2, 1, 'belum');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `name`, `email`, `password`, `created_at`, `updated_at`) VALUES
(2, 'Adinata', 'adinata@gmail.com', 'adinata@gmail.com', '2025-12-30 02:12:06', '2025-12-30 03:28:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `waktu`
--

CREATE TABLE `waktu` (
  `id_waktu` bigint(20) NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `waktu`
--

INSERT INTO `waktu` (`id_waktu`, `jam_mulai`, `jam_selesai`) VALUES
(1, '08:00:00', '10:00:00'),
(2, '13:00:00', '15:00:00'),
(3, '19:00:00', '21:00:00');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `hari`
--
ALTER TABLE `hari`
  ADD PRIMARY KEY (`id_hari`),
  ADD UNIQUE KEY `uniq_hari` (`nama_hari`,`tanggal`),
  ADD KEY `idx_hari_tanggal` (`tanggal`);

--
-- Indeks untuk tabel `keterangan`
--
ALTER TABLE `keterangan`
  ADD PRIMARY KEY (`id_keterangan`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notifications_task` (`task_id`),
  ADD KEY `idx_notifications_user_read` (`user_id`,`is_read`,`created_at`);

--
-- Indeks untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `reminder_log`
--
ALTER TABLE `reminder_log`
  ADD PRIMARY KEY (`id_log`),
  ADD UNIQUE KEY `uniq_tugas_date` (`id_tugas`,`reminder_date`);

--
-- Indeks untuk tabel `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id_task`),
  ADD KEY `idx_tasks_user_status_deadline` (`id_user`,`status`,`deadline`);

--
-- Indeks untuk tabel `tugas`
--
ALTER TABLE `tugas`
  ADD PRIMARY KEY (`id_tugas`),
  ADD KEY `fk_tugas_waktu` (`id_waktu`),
  ADD KEY `fk_tugas_hari` (`id_hari`),
  ADD KEY `fk_tugas_keterangan` (`id_keterangan`),
  ADD KEY `idx_tugas_pengguna_status` (`id_pengguna`,`status_tugas`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `waktu`
--
ALTER TABLE `waktu`
  ADD PRIMARY KEY (`id_waktu`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `hari`
--
ALTER TABLE `hari`
  MODIFY `id_hari` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `keterangan`
--
ALTER TABLE `keterangan`
  MODIFY `id_keterangan` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `reminder_log`
--
ALTER TABLE `reminder_log`
  MODIFY `id_log` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id_task` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `tugas`
--
ALTER TABLE `tugas`
  MODIFY `id_tugas` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `waktu`
--
ALTER TABLE `waktu`
  MODIFY `id_waktu` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_task` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id_task`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_tasks_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tugas`
--
ALTER TABLE `tugas`
  ADD CONSTRAINT `fk_tugas_hari` FOREIGN KEY (`id_hari`) REFERENCES `hari` (`id_hari`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tugas_keterangan` FOREIGN KEY (`id_keterangan`) REFERENCES `keterangan` (`id_keterangan`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tugas_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tugas_waktu` FOREIGN KEY (`id_waktu`) REFERENCES `waktu` (`id_waktu`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

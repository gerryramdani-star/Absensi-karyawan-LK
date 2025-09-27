-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 26 Sep 2025 pada 06.00
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_absensi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absences`
--

CREATE TABLE `absences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('kantor','wfa','izin','sakit','cuti') NOT NULL,
  `check_in` datetime DEFAULT NULL,
  `check_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `absences`
--

INSERT INTO `absences` (`id`, `user_id`, `type`, `check_in`, `check_out`) VALUES
(2, 1, 'kantor', '2025-09-23 10:41:23', '2025-09-23 10:44:45'),
(5, 13, 'kantor', '2025-09-23 16:51:11', NULL),
(6, 1, 'kantor', '2025-09-24 09:24:13', NULL),
(8, 13, '', '2025-09-24 15:12:06', '2025-09-24 15:12:09'),
(9, 1, 'kantor', '2025-09-25 10:03:55', '2025-09-25 16:34:57'),
(10, 14, 'kantor', '2025-09-25 10:05:34', '2025-09-25 11:58:16'),
(12, 1, '', '2025-09-26 10:28:53', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `divisions`
--

CREATE TABLE `divisions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `divisions`
--

INSERT INTO `divisions` (`id`, `name`) VALUES
(3, 'Client Relations'),
(1, 'Creative'),
(2, 'Digital Marketing'),
(4, 'Human Resources'),
(5, 'IT');

-- --------------------------------------------------------

--
-- Struktur dari tabel `holidays`
--

CREATE TABLE `holidays` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tipe_pengajuan` enum('cuti','izin','sakit') NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `keterangan` text NOT NULL,
  `file_pendukung` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approver_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `user_id`, `tipe_pengajuan`, `tanggal_mulai`, `tanggal_selesai`, `keterangan`, `file_pendukung`, `status`, `approver_id`, `created_at`) VALUES
(1, 1, 'cuti', '2025-09-26', '2025-09-25', 'ok', '', 'pending', NULL, '2025-09-25 09:35:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `overtime_logs`
--

CREATE TABLE `overtime_logs` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `durasi_menit` int(11) NOT NULL,
  `tipe_hari` enum('weekday','weekend','holiday') NOT NULL,
  `pengali` decimal(3,1) NOT NULL,
  `total_jam_dihitung` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `overtime_requests`
--

CREATE TABLE `overtime_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `absence_id` int(11) NOT NULL,
  `tanggal_lembur` date NOT NULL,
  `jam_mulai_pengajuan` time NOT NULL,
  `jam_selesai_pengajuan` time NOT NULL,
  `catatan_pengajuan` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approver_id` int(11) DEFAULT NULL,
  `approval_at` datetime DEFAULT NULL,
  `catatan_approval` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `positions`
--

CREATE TABLE `positions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `positions`
--

INSERT INTO `positions` (`id`, `name`) VALUES
(1, 'Account Executive'),
(7, 'Advertiser'),
(5, 'Content Writer'),
(3, 'Fotografer'),
(4, 'Graphic Designer'),
(8, 'Head Creative'),
(9, 'HRD Staff'),
(10, 'IT Staff'),
(2, 'Social Media Specialist'),
(6, 'Video Editor');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `division_id` int(11) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `is_admin`, `division_id`, `position_id`, `created_at`) VALUES
(1, 'Wahid', 'wahid', '112', 1, 5, 10, '2025-09-22 08:00:37'),
(13, 'alif', 'alif hacker tzy', '123', 0, 4, 7, '2025-09-23 09:49:57'),
(14, 'Daffa', 'daffa', '123', 0, 5, 10, '2025-09-25 03:05:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `work_logs`
--

CREATE TABLE `work_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `total_hours` decimal(5,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absences`
--
ALTER TABLE `absences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `divisions`
--
ALTER TABLE `divisions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeks untuk tabel `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tanggal` (`tanggal`);

--
-- Indeks untuk tabel `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `overtime_logs`
--
ALTER TABLE `overtime_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`);

--
-- Indeks untuk tabel `overtime_requests`
--
ALTER TABLE `overtime_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `absence_id` (`absence_id`);

--
-- Indeks untuk tabel `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `division_id` (`division_id`),
  ADD KEY `position_id` (`position_id`);

--
-- Indeks untuk tabel `work_logs`
--
ALTER TABLE `work_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absences`
--
ALTER TABLE `absences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `divisions`
--
ALTER TABLE `divisions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `overtime_logs`
--
ALTER TABLE `overtime_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `overtime_requests`
--
ALTER TABLE `overtime_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `work_logs`
--
ALTER TABLE `work_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `absences`
--
ALTER TABLE `absences`
  ADD CONSTRAINT `absences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `overtime_logs`
--
ALTER TABLE `overtime_logs`
  ADD CONSTRAINT `overtime_logs_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `overtime_requests` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `overtime_requests`
--
ALTER TABLE `overtime_requests`
  ADD CONSTRAINT `overtime_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `overtime_requests_ibfk_2` FOREIGN KEY (`absence_id`) REFERENCES `absences` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `work_logs`
--
ALTER TABLE `work_logs`
  ADD CONSTRAINT `work_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Waktu pembuatan: 20 Agu 2025 pada 09.37
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
-- Database: `kompetensi_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `diklat`
--

CREATE TABLE `diklat` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `nip` varchar(20) DEFAULT NULL,
  `jenis_diklat` varchar(100) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `nama_diklat` varchar(100) DEFAULT NULL,
  `instansi` varchar(100) DEFAULT NULL,
  `no_sertifikat` varchar(50) DEFAULT NULL,
  `tgl_mulai` date DEFAULT NULL,
  `tgl_selesai` date DEFAULT NULL,
  `durasi_jam` int(11) DEFAULT NULL,
  `file_sertifikat` varchar(100) DEFAULT NULL,
  `jenis_diklat_struktural` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` varchar(50) DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `diklat`
--

INSERT INTO `diklat` (`id`, `user_id`, `nama`, `nip`, `jenis_diklat`, `jabatan`, `nama_diklat`, `instansi`, `no_sertifikat`, `tgl_mulai`, `tgl_selesai`, `durasi_jam`, `file_sertifikat`, `jenis_diklat_struktural`, `created_at`, `created_by`) VALUES
(36, 4, 'NUR AFIFA, SE', '198207252006042032', 'Seminar', 'KASUBAG UMUM DAN KEPEGAWAIAN', 'Webinar ASN Belajar Seri 7', 'BPSDM Prov. Jatim', '229472/ BPSDM - ASNB / 205.3 / II / 2025', '2025-02-20', '2025-02-20', 3, '6890102a0d4ea_229472_BPSDM-ASNB_205.3_II_2025_20250220120204 sertifikat bangkom prop.pdf', '', '0000-00-00 00:00:00', ''),
(38, 4, 'NUR AFIFA, SE', '198207252006042032', 'Diklat Struktural', 'KASUBAG UMUM DAN KEPEGAWAIAN', 'Diklat Pim IV', 'BKD Kabupaten Kediri', '229472/ BPSDM - ASNB / 205.3 / II / 2025', '2018-07-01', '2018-07-31', 40, '689012631c46c_400617_WEBINAR_ASNB-12_205.3_2025_20250410074742 WEBINAR PROP SERI 12.pdf', 'SEPALA/ADUM/DIKLAT PIM TK. IV', '0000-00-00 00:00:00', ''),
(64, 11, 'Karim', '200006172025073001', 'Seminar', 'SA', 'Webinar ASN Belajar Seri 7', 'BKD Kabupaten Kediri', '229472/ BPSDM - ASNB / 205.3 / II / 2025', '2025-08-01', '2025-08-01', 3, '675639_WEBINAR_ASNB-23_205.4_2025_20250619121045.pdf', '', '2025-08-15 08:57:14', 'admin'),
(68, 11, 'Karim', '12345678', 'Pelatihan Jarak Jauh', '1asdasd', 'asdasdasd', 'asdasd', 'asdasd', '2025-08-01', '2025-08-14', 123, '1.jpg', 'SEPALA/ADUM/DIKLAT PIM TK. IV', '2025-08-19 08:28:40', 'admin');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `nip` varchar(20) DEFAULT NULL,
  `role` enum('asn','nonasn','admin') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `nip`, `role`) VALUES
(1, 'Admin Sistem', '0000', 'admin'),
(3, 'karimnASN', '12345', 'asn'),
(4, 'NUR AFIFA SE', '198207252006042032', 'asn'),
(11, 'Karim', '200006172025073001', 'nonasn');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `diklat`
--
ALTER TABLE `diklat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `diklat`
--
ALTER TABLE `diklat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `diklat`
--
ALTER TABLE `diklat`
  ADD CONSTRAINT `diklat_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

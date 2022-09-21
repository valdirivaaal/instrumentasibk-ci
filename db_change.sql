ALTER TABLE user_info ADD COLUMN `level` ENUM('member','admin') DEFAULT 'member' AFTER `foto`;


CREATE TABLE `logo_daerah` (
  `id` int(11) NOT NULL,
  `nama` varchar(999) NOT NULL,
  `path` varchar(999) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `logo_daerah`
--

INSERT INTO `logo_daerah` (`id`, `nama`, `path`) VALUES
(2, 'Provinsi DKI Jakarta 1', 'f8b208d854320f6e876ebb37ca5039f4.png');

ALTER TABLE `logo_daerah`
  ADD PRIMARY KEY (`id`);
  
  --
-- AUTO_INCREMENT untuk tabel `logo_daerah`
--
ALTER TABLE `logo_daerah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE instrumen_jawaban ADD COLUMN `email` VARCHAR(255) AFTER `tanggal_lahir`;

ALTER TABLE instrumen_jawaban ADD COLUMN `whatsapp` VARCHAR(255) AFTER `email`;

CREATE TABLE `pengumuman` (
  `id` int(11) NOT NULL,
  `pesan` varchar(999) NOT NULL,
  `tanggal` varchar(999) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id`);
  
--
-- AUTO_INCREMENT untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

ALTER TABLE `pengumuman` ADD `status` ENUM('show','hide') NOT NULL DEFAULT 'show' AFTER `tanggal`;

ALTER TABLE `user_surat` ADD `logo` VARCHAR(255) NOT NULL DEFAULT 'other' AFTER `baris_kelima`;

ALTER TABLE `kelas` ADD `tahun_ajaran` VARCHAR(255) NOT NULL AFTER `jumlah_siswa`;

ALTER TABLE `event_key` ADD `key_type` ENUM('single','multi') NOT NULL DEFAULT 'single' AFTER `tipe`;

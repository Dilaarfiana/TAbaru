/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.5.5-10.4.32-MariaDB : Database - sihati
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`sihati` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `sihati`;

/*Table structure for table `cache` */

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cache` */

/*Table structure for table `cache_locks` */

DROP TABLE IF EXISTS `cache_locks`;

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cache_locks` */

/*Table structure for table `detail_pemeriksaans` */

DROP TABLE IF EXISTS `detail_pemeriksaans`;

CREATE TABLE `detail_pemeriksaans` (
  `id_detprx` varchar(5) NOT NULL,
  `tanggal_jam` datetime NOT NULL,
  `id_siswa` varchar(10) NOT NULL,
  `status_pemeriksaan` enum('belum lengkap','lengkap') NOT NULL,
  `id_dokter` varchar(5) NOT NULL,
  `nip` varchar(18) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detprx`),
  KEY `detail_pemeriksaans_id_siswa_foreign` (`id_siswa`),
  KEY `detail_pemeriksaans_id_dokter_foreign` (`id_dokter`),
  KEY `detail_pemeriksaans_nip_foreign` (`nip`),
  CONSTRAINT `detail_pemeriksaans_id_dokter_foreign` FOREIGN KEY (`id_dokter`) REFERENCES `dokters` (`Id_Dokter`) ON DELETE CASCADE,
  CONSTRAINT `detail_pemeriksaans_id_siswa_foreign` FOREIGN KEY (`id_siswa`) REFERENCES `siswas` (`id_siswa`) ON DELETE CASCADE,
  CONSTRAINT `detail_pemeriksaans_nip_foreign` FOREIGN KEY (`nip`) REFERENCES `petugas_uks` (`NIP`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `detail_pemeriksaans` */

/*Table structure for table `detail_siswas` */

DROP TABLE IF EXISTS `detail_siswas`;

CREATE TABLE `detail_siswas` (
  `id_detsiswa` varchar(5) NOT NULL,
  `id_siswa` varchar(10) NOT NULL,
  `kode_jurusan` char(1) DEFAULT NULL,
  `kode_kelas` varchar(5) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detsiswa`),
  KEY `detail_siswas_id_siswa_foreign` (`id_siswa`),
  KEY `detail_siswas_kode_jurusan_foreign` (`kode_jurusan`),
  KEY `detail_siswas_kode_kelas_foreign` (`kode_kelas`),
  CONSTRAINT `detail_siswas_id_siswa_foreign` FOREIGN KEY (`id_siswa`) REFERENCES `siswas` (`id_siswa`) ON DELETE CASCADE,
  CONSTRAINT `detail_siswas_kode_jurusan_foreign` FOREIGN KEY (`kode_jurusan`) REFERENCES `jurusan` (`Kode_Jurusan`) ON DELETE CASCADE,
  CONSTRAINT `detail_siswas_kode_kelas_foreign` FOREIGN KEY (`kode_kelas`) REFERENCES `kelas` (`Kode_Kelas`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `detail_siswas` */

/*Table structure for table `dokters` */

DROP TABLE IF EXISTS `dokters`;

CREATE TABLE `dokters` (
  `Id_Dokter` varchar(5) NOT NULL,
  `Nama_Dokter` varchar(50) NOT NULL,
  `Spesialisasi` varchar(25) DEFAULT NULL,
  `No_Telp` varchar(15) DEFAULT NULL,
  `Alamat` text DEFAULT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id_Dokter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `dokters` */

/*Table structure for table `jurusan` */

DROP TABLE IF EXISTS `jurusan`;

CREATE TABLE `jurusan` (
  `Kode_Jurusan` char(1) NOT NULL,
  `Nama_Jurusan` varchar(30) NOT NULL,
  PRIMARY KEY (`Kode_Jurusan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `jurusan` */

/*Table structure for table `kelas` */

DROP TABLE IF EXISTS `kelas`;

CREATE TABLE `kelas` (
  `Kode_Kelas` varchar(5) NOT NULL,
  `Nama_Kelas` varchar(20) NOT NULL,
  `Tahun_Ajaran` varchar(10) DEFAULT NULL,
  `Kode_Jurusan` char(1) DEFAULT NULL,
  `Jumlah_Siswa` int(11) DEFAULT NULL,
  PRIMARY KEY (`Kode_Kelas`),
  KEY `kelas_kode_jurusan_foreign` (`Kode_Jurusan`),
  CONSTRAINT `kelas_kode_jurusan_foreign` FOREIGN KEY (`Kode_Jurusan`) REFERENCES `jurusan` (`Kode_Jurusan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `kelas` */

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values (1,'2025_04_24_061238_create_siswas_table',1),(2,'2025_04_24_065731_create_jurusans_table',1),(3,'2025_04_24_072005_create_orang_tuas_table',1),(4,'2025_04_24_074532_create_dokters_table',1),(5,'2025_04_27_091001_create_petugas_u_k_s_table',1),(6,'2025_04_28_034107_create_kelas_table',1),(7,'2025_04_28_054406_create_detail_siswas_table',1),(8,'2025_04_29_155456_create_rekam_medis_table',1),(9,'2025_05_04_002011_create_detail_pemeriksaans_table',1),(10,'2025_05_04_004250_create_pemeriksaan_awals_table',1),(11,'2025_05_04_211942_create_pemeriksaan_fisiks_table',1),(12,'2025_05_05_015134_create_pemeriksaan_harians_table',1),(13,'2025_05_21_163148_create_reseps_table',1),(14,'2025_05_28_210828_create_sessions_table',1),(15,'2025_05_28_215013_create_cache_table',1),(16,'2025_06_06_014316_create_notifications_table',1);

/*Table structure for table `notifications` */

DROP TABLE IF EXISTS `notifications`;

CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_orang_tua` varchar(5) NOT NULL,
  `id_siswa` varchar(10) NOT NULL,
  `type` enum('rekam_medis','pemeriksaan_awal','pemeriksaan_fisik','pemeriksaan_harian','resep') NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` varchar(50) DEFAULT NULL,
  `created_by_role` varchar(20) DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_id_orang_tua_is_read_index` (`id_orang_tua`,`is_read`),
  KEY `notifications_id_siswa_index` (`id_siswa`),
  KEY `notifications_created_at_index` (`created_at`),
  CONSTRAINT `notifications_id_orang_tua_foreign` FOREIGN KEY (`id_orang_tua`) REFERENCES `orang_tuas` (`id_orang_tua`) ON DELETE CASCADE,
  CONSTRAINT `notifications_id_siswa_foreign` FOREIGN KEY (`id_siswa`) REFERENCES `siswas` (`id_siswa`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `notifications` */

/*Table structure for table `orang_tuas` */

DROP TABLE IF EXISTS `orang_tuas`;

CREATE TABLE `orang_tuas` (
  `id_orang_tua` varchar(5) NOT NULL,
  `id_siswa` varchar(255) NOT NULL,
  `nama_ayah` varchar(100) DEFAULT NULL,
  `tanggal_lahir_ayah` date DEFAULT NULL,
  `pekerjaan_ayah` varchar(50) DEFAULT NULL,
  `pendidikan_ayah` varchar(50) DEFAULT NULL,
  `nama_ibu` varchar(100) DEFAULT NULL,
  `tanggal_lahir_ibu` date DEFAULT NULL,
  `pekerjaan_ibu` varchar(50) DEFAULT NULL,
  `pendidikan_ibu` varchar(50) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_orang_tua`),
  KEY `orang_tuas_id_siswa_index` (`id_siswa`),
  CONSTRAINT `orang_tuas_id_siswa_foreign` FOREIGN KEY (`id_siswa`) REFERENCES `siswas` (`id_siswa`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `orang_tuas` */

/*Table structure for table `pemeriksaan_awals` */

DROP TABLE IF EXISTS `pemeriksaan_awals`;

CREATE TABLE `pemeriksaan_awals` (
  `id_preawal` varchar(5) NOT NULL,
  `id_detprx` varchar(5) NOT NULL,
  `pemeriksaan` text DEFAULT NULL,
  `keluhan_dahulu` varchar(255) DEFAULT NULL,
  `suhu` decimal(3,1) DEFAULT NULL,
  `nadi` decimal(3,0) DEFAULT NULL,
  `tegangan` varchar(7) DEFAULT NULL,
  `pernapasan` int(11) DEFAULT NULL,
  `tipe` int(11) DEFAULT NULL,
  `status_nyeri` int(11) DEFAULT NULL,
  `karakteristik` varchar(50) DEFAULT NULL,
  `lokasi` varchar(50) DEFAULT NULL,
  `durasi` varchar(30) DEFAULT NULL,
  `frekuensi` varchar(30) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_preawal`),
  KEY `pemeriksaan_awals_id_detprx_foreign` (`id_detprx`),
  CONSTRAINT `pemeriksaan_awals_id_detprx_foreign` FOREIGN KEY (`id_detprx`) REFERENCES `detail_pemeriksaans` (`id_detprx`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pemeriksaan_awals` */

/*Table structure for table `pemeriksaan_fisiks` */

DROP TABLE IF EXISTS `pemeriksaan_fisiks`;

CREATE TABLE `pemeriksaan_fisiks` (
  `id_prefisik` varchar(5) NOT NULL,
  `id_detprx` varchar(5) NOT NULL,
  `tinggi_badan` decimal(4,1) DEFAULT NULL,
  `berat_badan` decimal(4,1) DEFAULT NULL,
  `lingkar_kepala` decimal(4,1) DEFAULT NULL,
  `lingkar_lengan_atas` decimal(3,1) DEFAULT NULL,
  `dada` varchar(50) DEFAULT NULL,
  `jantung` varchar(50) DEFAULT NULL,
  `paru` varchar(50) DEFAULT NULL,
  `perut` varchar(50) DEFAULT NULL,
  `hepar` varchar(50) DEFAULT NULL,
  `anogenital` varchar(50) DEFAULT NULL,
  `ekstremitas` varchar(50) DEFAULT NULL,
  `kepala` varchar(50) DEFAULT NULL,
  `pemeriksaan_penunjang` text DEFAULT NULL,
  `masalah_aktif` varchar(50) DEFAULT NULL,
  `rencana_medis_dan_terapi` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_prefisik`),
  KEY `pemeriksaan_fisiks_id_detprx_foreign` (`id_detprx`),
  CONSTRAINT `pemeriksaan_fisiks_id_detprx_foreign` FOREIGN KEY (`id_detprx`) REFERENCES `detail_pemeriksaans` (`id_detprx`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pemeriksaan_fisiks` */

/*Table structure for table `pemeriksaan_harians` */

DROP TABLE IF EXISTS `pemeriksaan_harians`;

CREATE TABLE `pemeriksaan_harians` (
  `Id_Harian` varchar(5) NOT NULL,
  `Tanggal_Jam` datetime NOT NULL,
  `Hasil_Pemeriksaan` text NOT NULL,
  `Id_Siswa` varchar(10) NOT NULL,
  `NIP` varchar(18) NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  `diperbarui_pada` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`Id_Harian`),
  KEY `pemeriksaan_harians_id_siswa_foreign` (`Id_Siswa`),
  KEY `pemeriksaan_harians_nip_foreign` (`NIP`),
  CONSTRAINT `pemeriksaan_harians_id_siswa_foreign` FOREIGN KEY (`Id_Siswa`) REFERENCES `siswas` (`id_siswa`) ON DELETE CASCADE,
  CONSTRAINT `pemeriksaan_harians_nip_foreign` FOREIGN KEY (`NIP`) REFERENCES `petugas_uks` (`NIP`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pemeriksaan_harians` */

/*Table structure for table `petugas_uks` */

DROP TABLE IF EXISTS `petugas_uks`;

CREATE TABLE `petugas_uks` (
  `NIP` varchar(18) NOT NULL,
  `nama_petugas_uks` varchar(50) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `level` enum('admin','petugas') NOT NULL DEFAULT 'petugas',
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`NIP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `petugas_uks` */

/*Table structure for table `rekam_medis` */

DROP TABLE IF EXISTS `rekam_medis`;

CREATE TABLE `rekam_medis` (
  `No_Rekam_Medis` varchar(5) NOT NULL,
  `Id_Siswa` varchar(10) DEFAULT NULL,
  `Id_Dokter` varchar(5) DEFAULT NULL,
  `Tanggal_Jam` datetime NOT NULL,
  `Keluhan_Utama` text NOT NULL,
  `Riwayat_Penyakit_Sekarang` text DEFAULT NULL,
  `Riwayat_Penyakit_Dahulu` text DEFAULT NULL,
  `Riwayat_Imunisasi` text DEFAULT NULL,
  `Riwayat_Penyakit_Keluarga` text DEFAULT NULL,
  `Silsilah_Keluarga` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`No_Rekam_Medis`),
  KEY `rekam_medis_id_siswa_foreign` (`Id_Siswa`),
  KEY `rekam_medis_id_dokter_foreign` (`Id_Dokter`),
  CONSTRAINT `rekam_medis_id_dokter_foreign` FOREIGN KEY (`Id_Dokter`) REFERENCES `dokters` (`Id_Dokter`) ON DELETE CASCADE,
  CONSTRAINT `rekam_medis_id_siswa_foreign` FOREIGN KEY (`Id_Siswa`) REFERENCES `siswas` (`id_siswa`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `rekam_medis` */

/*Table structure for table `resep` */

DROP TABLE IF EXISTS `resep`;

CREATE TABLE `resep` (
  `Id_Resep` varchar(5) NOT NULL,
  `Id_Siswa` varchar(10) NOT NULL,
  `Id_Dokter` varchar(5) NOT NULL,
  `Tanggal_Resep` date NOT NULL,
  `Nama_Obat` varchar(30) NOT NULL,
  `Dosis` varchar(30) NOT NULL,
  `Durasi` varchar(30) NOT NULL,
  `Dokumen` mediumblob DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  `diperbarui_pada` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`Id_Resep`),
  KEY `resep_id_siswa_foreign` (`Id_Siswa`),
  KEY `resep_id_dokter_foreign` (`Id_Dokter`),
  CONSTRAINT `resep_id_dokter_foreign` FOREIGN KEY (`Id_Dokter`) REFERENCES `dokters` (`Id_Dokter`) ON DELETE CASCADE,
  CONSTRAINT `resep_id_siswa_foreign` FOREIGN KEY (`Id_Siswa`) REFERENCES `siswas` (`id_siswa`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `resep` */

/*Table structure for table `sessions` */

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sessions` */

/*Table structure for table `siswas` */

DROP TABLE IF EXISTS `siswas`;

CREATE TABLE `siswas` (
  `id_siswa` varchar(10) NOT NULL,
  `nama_siswa` varchar(50) NOT NULL,
  `tempat_lahir` varchar(30) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `tanggal_lulus` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_siswa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `siswas` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

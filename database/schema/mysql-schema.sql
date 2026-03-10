/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `academic_history_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_history_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_akademik_id` bigint unsigned NOT NULL,
  `previous_mode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_mode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trigger_event` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `batch_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_log_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  KEY `activity_log_causer_type_causer_id_index` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `akademik_ekuivalensi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `akademik_ekuivalensi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `prodi_id` bigint unsigned NOT NULL,
  `mk_asal_id` bigint unsigned NOT NULL,
  `mk_tujuan_id` bigint unsigned NOT NULL,
  `nomor_sk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ekuivalensi_pair` (`mk_asal_id`,`mk_tujuan_id`),
  KEY `akademik_ekuivalensi_prodi_id_foreign` (`prodi_id`),
  KEY `akademik_ekuivalensi_mk_tujuan_id_foreign` (`mk_tujuan_id`),
  KEY `akademik_ekuivalensi_created_by_foreign` (`created_by`),
  CONSTRAINT `akademik_ekuivalensi_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `akademik_ekuivalensi_mk_asal_id_foreign` FOREIGN KEY (`mk_asal_id`) REFERENCES `master_mata_kuliahs` (`id`),
  CONSTRAINT `akademik_ekuivalensi_mk_tujuan_id_foreign` FOREIGN KEY (`mk_tujuan_id`) REFERENCES `master_mata_kuliahs` (`id`),
  CONSTRAINT `akademik_ekuivalensi_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `ref_prodi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `akademik_grade_revision_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `akademik_grade_revision_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `krs_detail_id` bigint unsigned NOT NULL,
  `old_nilai_angka` decimal(5,2) NOT NULL,
  `old_nilai_huruf` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_nilai_angka` decimal(5,2) NOT NULL,
  `new_nilai_huruf` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alasan_perbaikan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_sk_perbaikan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `executed_by` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `akademik_grade_revision_logs_krs_detail_id_foreign` (`krs_detail_id`),
  KEY `akademik_grade_revision_logs_executed_by_foreign` (`executed_by`),
  CONSTRAINT `akademik_grade_revision_logs_executed_by_foreign` FOREIGN KEY (`executed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `akademik_grade_revision_logs_krs_detail_id_foreign` FOREIGN KEY (`krs_detail_id`) REFERENCES `krs_detail` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jadwal_kuliah`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jadwal_kuliah` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_akademik_id` bigint unsigned NOT NULL,
  `kurikulum_id` bigint unsigned DEFAULT NULL,
  `mata_kuliah_id` bigint unsigned NOT NULL,
  `dosen_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kelas` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hari` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `ruang` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kuota_kelas` int NOT NULL DEFAULT '40',
  `isi_kelas` int NOT NULL DEFAULT '0',
  `id_program_kelas_allow` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jadwal_kuliah_tahun_akademik_id_foreign` (`tahun_akademik_id`),
  KEY `jadwal_kuliah_mata_kuliah_id_foreign` (`mata_kuliah_id`),
  KEY `jadwal_kuliah_id_program_kelas_allow_foreign` (`id_program_kelas_allow`),
  KEY `jadwal_kuliah_dosen_id_foreign` (`dosen_id`),
  KEY `jadwal_kuliah_kurikulum_id_foreign` (`kurikulum_id`),
  CONSTRAINT `jadwal_kuliah_dosen_id_foreign` FOREIGN KEY (`dosen_id`) REFERENCES `trx_dosen` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `jadwal_kuliah_id_program_kelas_allow_foreign` FOREIGN KEY (`id_program_kelas_allow`) REFERENCES `ref_program_kelas` (`id`),
  CONSTRAINT `jadwal_kuliah_kurikulum_id_foreign` FOREIGN KEY (`kurikulum_id`) REFERENCES `master_kurikulums` (`id`) ON DELETE SET NULL,
  CONSTRAINT `jadwal_kuliah_mata_kuliah_id_foreign` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `master_mata_kuliahs` (`id`),
  CONSTRAINT `jadwal_kuliah_tahun_akademik_id_foreign` FOREIGN KEY (`tahun_akademik_id`) REFERENCES `ref_tahun_akademik` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `keuangan_adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `keuangan_adjustments` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tagihan_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_adjustment` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `keuangan_adjustments_tagihan_id_foreign` (`tagihan_id`),
  KEY `keuangan_adjustments_created_by_foreign` (`created_by`),
  CONSTRAINT `keuangan_adjustments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `keuangan_adjustments_tagihan_id_foreign` FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan_mahasiswas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `keuangan_detail_tarif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `keuangan_detail_tarif` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `skema_tarif_id` bigint unsigned NOT NULL,
  `komponen_biaya_id` bigint unsigned NOT NULL,
  `nominal` decimal(19,2) NOT NULL DEFAULT '0.00',
  `berlaku_semester` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `keuangan_detail_tarif_skema_tarif_id_foreign` (`skema_tarif_id`),
  KEY `keuangan_detail_tarif_komponen_biaya_id_foreign` (`komponen_biaya_id`),
  CONSTRAINT `keuangan_detail_tarif_komponen_biaya_id_foreign` FOREIGN KEY (`komponen_biaya_id`) REFERENCES `keuangan_komponen_biaya` (`id`),
  CONSTRAINT `keuangan_detail_tarif_skema_tarif_id_foreign` FOREIGN KEY (`skema_tarif_id`) REFERENCES `keuangan_skema_tarif` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `keuangan_komponen_biaya`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `keuangan_komponen_biaya` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_komponen` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe_biaya` enum('TETAP','SKS','SEKALI','INSIDENTAL') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `keuangan_saldo_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `keuangan_saldo_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `saldo_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('IN','OUT') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `referensi_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `keuangan_saldo_transactions_saldo_id_foreign` (`saldo_id`),
  CONSTRAINT `keuangan_saldo_transactions_saldo_id_foreign` FOREIGN KEY (`saldo_id`) REFERENCES `keuangan_saldos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `keuangan_saldos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `keuangan_saldos` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mahasiswa_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `saldo` decimal(15,2) NOT NULL DEFAULT '0.00',
  `last_updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `keuangan_saldos_mahasiswa_id_foreign` (`mahasiswa_id`),
  CONSTRAINT `keuangan_saldos_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `keuangan_skema_tarif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `keuangan_skema_tarif` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_skema` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `angkatan_id` int NOT NULL,
  `prodi_id` bigint unsigned NOT NULL,
  `program_kelas_id` bigint unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_skema_tarif` (`angkatan_id`,`prodi_id`,`program_kelas_id`),
  KEY `keuangan_skema_tarif_prodi_id_foreign` (`prodi_id`),
  KEY `keuangan_skema_tarif_program_kelas_id_foreign` (`program_kelas_id`),
  CONSTRAINT `keuangan_skema_tarif_angkatan_id_foreign` FOREIGN KEY (`angkatan_id`) REFERENCES `ref_angkatan` (`id_tahun`),
  CONSTRAINT `keuangan_skema_tarif_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `ref_prodi` (`id`),
  CONSTRAINT `keuangan_skema_tarif_program_kelas_id_foreign` FOREIGN KEY (`program_kelas_id`) REFERENCES `ref_program_kelas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `krs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `krs` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mahasiswa_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_akademik_id` bigint unsigned NOT NULL,
  `tgl_krs` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status_krs` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `is_paket_snapshot` tinyint(1) DEFAULT NULL,
  `dosen_wali_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `krs_mahasiswa_id_tahun_akademik_id_unique` (`mahasiswa_id`,`tahun_akademik_id`),
  KEY `krs_tahun_akademik_id_foreign` (`tahun_akademik_id`),
  KEY `krs_dosen_wali_id_foreign` (`dosen_wali_id`),
  CONSTRAINT `krs_dosen_wali_id_foreign` FOREIGN KEY (`dosen_wali_id`) REFERENCES `trx_dosen` (`id`) ON DELETE SET NULL,
  CONSTRAINT `krs_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswas` (`id`),
  CONSTRAINT `krs_tahun_akademik_id_foreign` FOREIGN KEY (`tahun_akademik_id`) REFERENCES `ref_tahun_akademik` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `krs_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `krs_detail` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `krs_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jadwal_kuliah_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_mk_snapshot` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_mk_snapshot` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sks_snapshot` int DEFAULT NULL,
  `activity_type_snapshot` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'REGULAR',
  `ekuivalensi_id` bigint unsigned DEFAULT NULL,
  `status_ambil` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'B',
  `nilai_angka` decimal(5,2) NOT NULL DEFAULT '0.00',
  `nilai_huruf` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai_indeks` decimal(3,2) NOT NULL DEFAULT '0.00',
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `is_edom_filled` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `krs_detail_krs_id_jadwal_kuliah_id_unique` (`krs_id`,`jadwal_kuliah_id`),
  KEY `krs_detail_jadwal_kuliah_id_foreign` (`jadwal_kuliah_id`),
  KEY `krs_detail_ekuivalensi_id_foreign` (`ekuivalensi_id`),
  CONSTRAINT `krs_detail_ekuivalensi_id_foreign` FOREIGN KEY (`ekuivalensi_id`) REFERENCES `akademik_ekuivalensi` (`id`) ON DELETE SET NULL,
  CONSTRAINT `krs_detail_jadwal_kuliah_id_foreign` FOREIGN KEY (`jadwal_kuliah_id`) REFERENCES `jadwal_kuliah` (`id`),
  CONSTRAINT `krs_detail_krs_id_foreign` FOREIGN KEY (`krs_id`) REFERENCES `krs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `krs_detail_nilai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `krs_detail_nilai` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `krs_detail_id` bigint unsigned NOT NULL,
  `komponen_id` bigint unsigned NOT NULL,
  `nilai_angka` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `krs_detail_nilai_krs_detail_id_foreign` (`krs_detail_id`),
  KEY `krs_detail_nilai_komponen_id_foreign` (`komponen_id`),
  CONSTRAINT `krs_detail_nilai_komponen_id_foreign` FOREIGN KEY (`komponen_id`) REFERENCES `ref_komponen_nilai` (`id`),
  CONSTRAINT `krs_detail_nilai_krs_detail_id_foreign` FOREIGN KEY (`krs_detail_id`) REFERENCES `krs_detail` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kurikulum_komponen_nilai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kurikulum_komponen_nilai` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kurikulum_id` bigint unsigned NOT NULL,
  `komponen_id` bigint unsigned NOT NULL,
  `bobot_persen` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kurikulum_komponen_nilai_kurikulum_id_foreign` (`kurikulum_id`),
  KEY `kurikulum_komponen_nilai_komponen_id_foreign` (`komponen_id`),
  CONSTRAINT `kurikulum_komponen_nilai_komponen_id_foreign` FOREIGN KEY (`komponen_id`) REFERENCES `ref_komponen_nilai` (`id`),
  CONSTRAINT `kurikulum_komponen_nilai_kurikulum_id_foreign` FOREIGN KEY (`kurikulum_id`) REFERENCES `master_kurikulums` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kurikulum_mata_kuliah`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kurikulum_mata_kuliah` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kurikulum_id` bigint unsigned NOT NULL,
  `mata_kuliah_id` bigint unsigned NOT NULL,
  `semester_paket` int NOT NULL,
  `sks_tatap_muka` int NOT NULL,
  `sks_praktek` int NOT NULL DEFAULT '0',
  `sks_lapangan` int NOT NULL DEFAULT '0',
  `sifat_mk` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'W',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `prasyarat_mk_id` bigint unsigned DEFAULT NULL,
  `min_nilai_prasyarat` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'D',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kurikulum_mata_kuliah_kurikulum_id_mata_kuliah_id_unique` (`kurikulum_id`,`mata_kuliah_id`),
  KEY `kurikulum_mata_kuliah_mata_kuliah_id_foreign` (`mata_kuliah_id`),
  KEY `kurikulum_mata_kuliah_prasyarat_mk_id_foreign` (`prasyarat_mk_id`),
  CONSTRAINT `kurikulum_mata_kuliah_kurikulum_id_foreign` FOREIGN KEY (`kurikulum_id`) REFERENCES `master_kurikulums` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kurikulum_mata_kuliah_mata_kuliah_id_foreign` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `master_mata_kuliahs` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `kurikulum_mata_kuliah_prasyarat_mk_id_foreign` FOREIGN KEY (`prasyarat_mk_id`) REFERENCES `master_mata_kuliahs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lpm_ami_findings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lpm_ami_findings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `periode_id` bigint unsigned NOT NULL,
  `prodi_id` bigint unsigned NOT NULL,
  `standar_id` bigint unsigned NOT NULL,
  `auditor_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `klasifikasi` enum('OB','KTS_MINOR','KTS_MAYOR') COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi_temuan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `akar_masalah` text COLLATE utf8mb4_unicode_ci,
  `rencana_tindak_lanjut` text COLLATE utf8mb4_unicode_ci,
  `deadline_perbaikan` date DEFAULT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lpm_ami_findings_periode_id_foreign` (`periode_id`),
  KEY `lpm_ami_findings_prodi_id_foreign` (`prodi_id`),
  KEY `lpm_ami_findings_standar_id_foreign` (`standar_id`),
  CONSTRAINT `lpm_ami_findings_periode_id_foreign` FOREIGN KEY (`periode_id`) REFERENCES `lpm_ami_periodes` (`id`),
  CONSTRAINT `lpm_ami_findings_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `ref_prodi` (`id`),
  CONSTRAINT `lpm_ami_findings_standar_id_foreign` FOREIGN KEY (`standar_id`) REFERENCES `lpm_standars` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lpm_ami_periodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lpm_ami_periodes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_periode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `status` enum('DRAFT','ON-GOING','FINISHED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lpm_dokumens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lpm_dokumens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_dokumen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` enum('KEBIJAKAN','MANUAL','STANDAR','FORMULIR') COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `versi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
  `tgl_berlaku` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lpm_edom_jawaban`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lpm_edom_jawaban` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `krs_detail_id` bigint unsigned NOT NULL,
  `pertanyaan_id` bigint unsigned NOT NULL,
  `skor` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lpm_edom_jawaban_krs_detail_id_foreign` (`krs_detail_id`),
  KEY `lpm_edom_jawaban_pertanyaan_id_foreign` (`pertanyaan_id`),
  CONSTRAINT `lpm_edom_jawaban_krs_detail_id_foreign` FOREIGN KEY (`krs_detail_id`) REFERENCES `krs_detail` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lpm_edom_jawaban_pertanyaan_id_foreign` FOREIGN KEY (`pertanyaan_id`) REFERENCES `lpm_kuisioner_pertanyaan` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lpm_iku_targets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lpm_iku_targets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `indikator_id` bigint unsigned NOT NULL,
  `tahun` int NOT NULL,
  `target_nilai` decimal(10,2) NOT NULL,
  `capaian_nilai` decimal(10,2) NOT NULL DEFAULT '0.00',
  `analisis_kendala` text COLLATE utf8mb4_unicode_ci,
  `tindakan_koreksi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lpm_iku_targets_indikator_id_foreign` (`indikator_id`),
  CONSTRAINT `lpm_iku_targets_indikator_id_foreign` FOREIGN KEY (`indikator_id`) REFERENCES `lpm_indikators` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lpm_indikators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lpm_indikators` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `standar_id` bigint unsigned NOT NULL,
  `nama_indikator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bobot` decimal(5,2) NOT NULL DEFAULT '0.00',
  `sumber_data_siakad` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lpm_indikators_slug_unique` (`slug`),
  KEY `lpm_indikators_standar_id_foreign` (`standar_id`),
  CONSTRAINT `lpm_indikators_standar_id_foreign` FOREIGN KEY (`standar_id`) REFERENCES `lpm_standars` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lpm_kuisioner_kelompok`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lpm_kuisioner_kelompok` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_kelompok` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` int NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lpm_kuisioner_pertanyaan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lpm_kuisioner_pertanyaan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kelompok_id` bigint unsigned NOT NULL,
  `bunyi_pertanyaan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe_skala` enum('1-4','1-5') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1-4',
  `is_required` tinyint(1) NOT NULL DEFAULT '1',
  `urutan` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lpm_kuisioner_pertanyaan_kelompok_id_foreign` (`kelompok_id`),
  CONSTRAINT `lpm_kuisioner_pertanyaan_kelompok_id_foreign` FOREIGN KEY (`kelompok_id`) REFERENCES `lpm_kuisioner_kelompok` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lpm_standars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lpm_standars` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_standar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_standar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` enum('AKADEMIK','NON-AKADEMIK') COLLATE utf8mb4_unicode_ci NOT NULL,
  `pernyataan_standar` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_pencapaian` int NOT NULL DEFAULT '100',
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '%',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lpm_standars_kode_standar_unique` (`kode_standar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mahasiswas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mahasiswas` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `person_id` bigint unsigned DEFAULT NULL,
  `nim` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `angkatan_id` int NOT NULL,
  `prodi_id` bigint unsigned NOT NULL,
  `dosen_wali_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `program_kelas_id` bigint unsigned NOT NULL,
  `data_tambahan` json DEFAULT NULL,
  `id_pd_feeder` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mahasiswas_nim_unique` (`nim`),
  KEY `mahasiswas_prodi_id_foreign` (`prodi_id`),
  KEY `mahasiswas_program_kelas_id_foreign` (`program_kelas_id`),
  KEY `mahasiswas_angkatan_id_foreign` (`angkatan_id`),
  KEY `mahasiswas_id_pd_feeder_index` (`id_pd_feeder`),
  KEY `mahasiswas_dosen_wali_id_foreign` (`dosen_wali_id`),
  KEY `mahasiswas_person_id_foreign` (`person_id`),
  CONSTRAINT `mahasiswas_angkatan_id_foreign` FOREIGN KEY (`angkatan_id`) REFERENCES `ref_angkatan` (`id_tahun`),
  CONSTRAINT `mahasiswas_dosen_wali_id_foreign` FOREIGN KEY (`dosen_wali_id`) REFERENCES `trx_dosen` (`id`) ON DELETE SET NULL,
  CONSTRAINT `mahasiswas_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `ref_person` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mahasiswas_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `ref_prodi` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `mahasiswas_program_kelas_id_foreign` FOREIGN KEY (`program_kelas_id`) REFERENCES `ref_program_kelas` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `master_kurikulums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `master_kurikulums` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `prodi_id` bigint unsigned NOT NULL,
  `nama_kurikulum` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_mulai` int NOT NULL,
  `id_semester_mulai` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `jumlah_sks_lulus` int NOT NULL DEFAULT '144' COMMENT 'Total SKS minimal untuk lulus',
  `jumlah_sks_wajib` int NOT NULL DEFAULT '0',
  `jumlah_sks_pilihan` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `master_kurikulums_prodi_id_foreign` (`prodi_id`),
  CONSTRAINT `master_kurikulums_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `ref_prodi` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `master_mata_kuliahs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `master_mata_kuliahs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `prodi_id` bigint unsigned NOT NULL,
  `kode_mk` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_mk` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sks_default` int NOT NULL DEFAULT '3',
  `sks_tatap_muka` int NOT NULL DEFAULT '0',
  `sks_praktek` int NOT NULL DEFAULT '0',
  `sks_lapangan` int NOT NULL DEFAULT '0',
  `jenis_mk` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `activity_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'REGULAR',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `master_mata_kuliahs_prodi_id_kode_mk_unique` (`prodi_id`,`kode_mk`),
  CONSTRAINT `master_mata_kuliahs_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `ref_prodi` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pembayaran_mahasiswas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembayaran_mahasiswas` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tagihan_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nominal_bayar` decimal(19,2) NOT NULL,
  `tanggal_bayar` datetime NOT NULL,
  `metode_pembayaran` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MANUAL',
  `bukti_bayar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan_pengirim` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_verifikasi` enum('PENDING','VALID','INVALID') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `verified_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `catatan_verifikasi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pembayaran_mahasiswas_tagihan_id_foreign` (`tagihan_id`),
  KEY `pembayaran_mahasiswas_verified_by_foreign` (`verified_by`),
  KEY `pembayaran_mahasiswas_status_verifikasi_index` (`status_verifikasi`),
  CONSTRAINT `pembayaran_mahasiswas_tagihan_id_foreign` FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan_mahasiswas` (`id`),
  CONSTRAINT `pembayaran_mahasiswas_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `perkuliahan_absensi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `perkuliahan_absensi` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `perkuliahan_sesi_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `krs_detail_id` bigint unsigned NOT NULL,
  `status_kehadiran` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `waktu_check_in` datetime DEFAULT NULL,
  `bukti_validasi` json DEFAULT NULL,
  `is_manual_update` tinyint(1) NOT NULL DEFAULT '0',
  `modified_by_user_id` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alasan_perubahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `perkuliahan_absensi_perkuliahan_sesi_id_foreign` (`perkuliahan_sesi_id`),
  KEY `perkuliahan_absensi_krs_detail_id_status_kehadiran_index` (`krs_detail_id`,`status_kehadiran`),
  KEY `perkuliahan_absensi_status_kehadiran_index` (`status_kehadiran`),
  CONSTRAINT `perkuliahan_absensi_krs_detail_id_foreign` FOREIGN KEY (`krs_detail_id`) REFERENCES `krs_detail` (`id`) ON DELETE CASCADE,
  CONSTRAINT `perkuliahan_absensi_perkuliahan_sesi_id_foreign` FOREIGN KEY (`perkuliahan_sesi_id`) REFERENCES `perkuliahan_sesi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `perkuliahan_sesi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `perkuliahan_sesi` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jadwal_kuliah_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pertemuan_ke` int NOT NULL,
  `waktu_mulai_rencana` datetime NOT NULL,
  `waktu_mulai_realisasi` datetime DEFAULT NULL,
  `waktu_selesai_realisasi` datetime DEFAULT NULL,
  `materi_kuliah` text COLLATE utf8mb4_unicode_ci,
  `catatan_dosen` text COLLATE utf8mb4_unicode_ci,
  `token_sesi` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metode_validasi` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'QR',
  `status_sesi` enum('terjadwal','dibuka','selesai','dibatalkan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'terjadwal',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `perkuliahan_sesi_jadwal_kuliah_id_pertemuan_ke_index` (`jadwal_kuliah_id`,`pertemuan_ke`),
  KEY `perkuliahan_sesi_token_sesi_index` (`token_sesi`),
  KEY `perkuliahan_sesi_status_sesi_index` (`status_sesi`),
  CONSTRAINT `perkuliahan_sesi_jadwal_kuliah_id_foreign` FOREIGN KEY (`jadwal_kuliah_id`) REFERENCES `jadwal_kuliah` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ref_angkatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_angkatan` (
  `id_tahun` int NOT NULL,
  `batas_tahun_studi` int DEFAULT NULL,
  `is_active_pmb` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ref_aturan_sks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_aturan_sks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `min_ips` decimal(4,2) NOT NULL,
  `max_ips` decimal(4,2) NOT NULL,
  `max_sks` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ref_fakultas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_fakultas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_fakultas` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_fakultas` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_feeder` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ref_fakultas_kode_fakultas_unique` (`kode_fakultas`),
  KEY `ref_fakultas_id_feeder_index` (`id_feeder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ref_gelar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_gelar` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `posisi` enum('DEPAN','BELAKANG') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BELAKANG',
  `jenjang` enum('D3','D4','S1','S2','S3','PROFESI') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ref_gelar_kode_unique` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ref_jabatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_jabatan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_jabatan` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_jabatan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` enum('STRUKTURAL','FUNGSIONAL') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ref_jabatan_kode_jabatan_unique` (`kode_jabatan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ref_komponen_nilai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_komponen_nilai` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_komponen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ref_komponen_nilai_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ref_person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_person` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempat_lahir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ref_person_nik_unique` (`nik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ref_person_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_person_role` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ref_person_role_kode_role_unique` (`kode_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ref_prodi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_prodi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fakultas_id` bigint unsigned NOT NULL,
  `kode_prodi_dikti` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_prodi_internal` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_prodi` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_paket` tinyint(1) NOT NULL DEFAULT '1',
  `jenjang` enum('D3','D4','S1','S2','S3','PROFESI') COLLATE utf8mb4_unicode_ci NOT NULL,
  `gelar_lulusan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `format_nim` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Pattern: {THN}=24, {TAHUN}=2024, {KODE}=KodeProdi, {NO:4}=0001',
  `last_nim_seq` int NOT NULL DEFAULT '0',
  `id_feeder` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ref_prodi_kode_prodi_internal_unique` (`kode_prodi_internal`),
  KEY `ref_prodi_fakultas_id_foreign` (`fakultas_id`),
  KEY `ref_prodi_kode_prodi_dikti_index` (`kode_prodi_dikti`),
  KEY `ref_prodi_id_feeder_index` (`id_feeder`),
  CONSTRAINT `ref_prodi_fakultas_id_foreign` FOREIGN KEY (`fakultas_id`) REFERENCES `ref_fakultas` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ref_program_kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_program_kelas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_program` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_internal` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_pembayaran_persen` int NOT NULL DEFAULT '50' COMMENT 'Syarat minimal bayar untuk bisa KRS',
  `id_jenis_kelas_feeder` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ref_program_kelas_kode_internal_unique` (`kode_internal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ref_skala_nilai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_skala_nilai` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `huruf` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bobot_indeks` decimal(3,2) NOT NULL,
  `nilai_min` decimal(5,2) NOT NULL,
  `nilai_max` decimal(5,2) NOT NULL,
  `is_lulus` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ref_tahun_akademik`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ref_tahun_akademik` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_tahun` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_tahun` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` int NOT NULL COMMENT '1=Ganjil, 2=Genap, 3=Pendek',
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `buka_krs` tinyint(1) NOT NULL DEFAULT '0',
  `buka_input_nilai` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tgl_mulai_krs` date DEFAULT NULL,
  `tgl_selesai_krs` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ref_tahun_akademik_kode_tahun_unique` (`kode_tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `riwayat_status_mahasiswas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `riwayat_status_mahasiswas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_akademik_id` bigint unsigned NOT NULL,
  `status_kuliah` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `ips` decimal(4,2) NOT NULL DEFAULT '0.00',
  `ipk` decimal(4,2) NOT NULL DEFAULT '0.00',
  `sks_semester` int NOT NULL DEFAULT '0',
  `sks_total` int NOT NULL DEFAULT '0',
  `nomor_sk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_status_per_semester` (`mahasiswa_id`,`tahun_akademik_id`),
  KEY `riwayat_status_mahasiswas_tahun_akademik_id_foreign` (`tahun_akademik_id`),
  KEY `riwayat_status_mahasiswas_status_kuliah_index` (`status_kuliah`),
  CONSTRAINT `riwayat_status_mahasiswas_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `riwayat_status_mahasiswas_tahun_akademik_id_foreign` FOREIGN KEY (`tahun_akademik_id`) REFERENCES `ref_tahun_akademik` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tagihan_mahasiswas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tagihan_mahasiswas` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mahasiswa_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_akademik_id` bigint unsigned DEFAULT NULL,
  `kode_transaksi` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_tagihan` decimal(19,2) NOT NULL,
  `total_bayar` decimal(19,2) NOT NULL DEFAULT '0.00',
  `sisa_tagihan` decimal(19,2) GENERATED ALWAYS AS ((`total_tagihan` - `total_bayar`)) VIRTUAL,
  `status_bayar` enum('BELUM','CICIL','LUNAS') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BELUM',
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tenggat_waktu` date DEFAULT NULL,
  `rincian_item` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tagihan_mahasiswas_kode_transaksi_unique` (`kode_transaksi`),
  KEY `tagihan_mahasiswas_mahasiswa_id_foreign` (`mahasiswa_id`),
  KEY `tagihan_mahasiswas_tahun_akademik_id_foreign` (`tahun_akademik_id`),
  KEY `tagihan_mahasiswas_status_bayar_index` (`status_bayar`),
  KEY `tagihan_mahasiswas_created_by_foreign` (`created_by`),
  CONSTRAINT `tagihan_mahasiswas_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tagihan_mahasiswas_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswas` (`id`),
  CONSTRAINT `tagihan_mahasiswas_tahun_akademik_id_foreign` FOREIGN KEY (`tahun_akademik_id`) REFERENCES `ref_tahun_akademik` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trx_dosen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trx_dosen` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `person_id` bigint unsigned NOT NULL,
  `prodi_id` bigint unsigned NOT NULL,
  `jenis_dosen` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TETAP',
  `asal_institusi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nidn` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nuptk` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `data_tambahan` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trx_dosen_nidn_unique` (`nidn`),
  UNIQUE KEY `trx_dosen_nuptk_unique` (`nuptk`),
  KEY `trx_dosen_person_id_foreign` (`person_id`),
  KEY `trx_dosen_prodi_id_foreign` (`prodi_id`),
  CONSTRAINT `trx_dosen_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `ref_person` (`id`) ON DELETE CASCADE,
  CONSTRAINT `trx_dosen_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `ref_prodi` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trx_pegawai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trx_pegawai` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `person_id` bigint unsigned NOT NULL,
  `nip` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_pegawai` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trx_pegawai_nip_unique` (`nip`),
  KEY `trx_pegawai_person_id_foreign` (`person_id`),
  CONSTRAINT `trx_pegawai_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `ref_person` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trx_person_gelar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trx_person_gelar` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `person_id` bigint unsigned NOT NULL,
  `gelar_id` bigint unsigned NOT NULL,
  `urutan` tinyint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trx_person_gelar_person_id_gelar_id_unique` (`person_id`,`gelar_id`),
  KEY `trx_person_gelar_gelar_id_foreign` (`gelar_id`),
  CONSTRAINT `trx_person_gelar_gelar_id_foreign` FOREIGN KEY (`gelar_id`) REFERENCES `ref_gelar` (`id`) ON DELETE CASCADE,
  CONSTRAINT `trx_person_gelar_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `ref_person` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trx_person_jabatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trx_person_jabatan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `person_id` bigint unsigned NOT NULL,
  `jabatan_id` bigint unsigned NOT NULL,
  `fakultas_id` bigint unsigned DEFAULT NULL,
  `prodi_id` bigint unsigned DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `trx_person_jabatan_person_id_foreign` (`person_id`),
  KEY `trx_person_jabatan_jabatan_id_foreign` (`jabatan_id`),
  KEY `trx_person_jabatan_fakultas_id_foreign` (`fakultas_id`),
  KEY `trx_person_jabatan_prodi_id_foreign` (`prodi_id`),
  CONSTRAINT `trx_person_jabatan_fakultas_id_foreign` FOREIGN KEY (`fakultas_id`) REFERENCES `ref_fakultas` (`id`),
  CONSTRAINT `trx_person_jabatan_jabatan_id_foreign` FOREIGN KEY (`jabatan_id`) REFERENCES `ref_jabatan` (`id`),
  CONSTRAINT `trx_person_jabatan_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `ref_person` (`id`) ON DELETE CASCADE,
  CONSTRAINT `trx_person_jabatan_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `ref_prodi` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trx_person_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trx_person_role` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `person_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `trx_person_role_person_id_foreign` (`person_id`),
  KEY `trx_person_role_role_id_foreign` (`role_id`),
  CONSTRAINT `trx_person_role_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `ref_person` (`id`),
  CONSTRAINT `trx_person_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `ref_person_role` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `person_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mahasiswa',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profileable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profileable_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_profileable_type_profileable_id_index` (`profileable_type`,`profileable_id`),
  KEY `users_person_id_foreign` (`person_id`),
  CONSTRAINT `users_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `ref_person` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2026_01_28_012404_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2026_01_28_013820_create_ref_fakultas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2026_01_28_013821_create_ref_prodi_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2026_01_28_013821_create_ref_program_kelas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2026_01_28_013823_create_ref_tahun_akademik_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2026_01_28_013828_create_mahasiswas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2026_01_28_013829_create_riwayat_status_mahasiswas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2026_01_28_013830_create_dosens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2026_01_28_013834_create_master_kurikulums_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2026_01_28_013834_create_master_mata_kuliahs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2026_01_28_013835_create_jadwal_kuliah_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2026_01_28_013835_create_krs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2026_01_28_013835_create_kurikulum_mata_kuliah_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2026_01_28_013837_create_krs_detail_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2026_01_28_013842_create_keuangan_detail_tarif_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2026_01_28_013842_create_keuangan_komponen_biaya_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2026_01_28_013842_create_keuangan_skema_tarif_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2026_01_28_013843_create_tagihan_mahasiswas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2026_01_28_013844_create_pembayaran_mahasiswas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2026_01_28_035350_add_role_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2026_01_28_041308_add_is_active_to_tahun_akademiks',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2026_01_28_045628_add_rincian_sks_to_master_mata_kuliahs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2026_01_28_054424_create_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2026_01_28_054425_add_event_column_to_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2026_01_28_054426_add_batch_uuid_column_to_activity_log_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2026_01_28_102117_add_dosen_wali_id_to_mahasiswas',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2026_01_28_140325_create_ref_aturan_sks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2026_01_28_153033_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2026_01_28_160902_add_format_nim_to_ref_prodi',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2026_01_28_163335_update_kode_transaksi_length_in_tagihan_mahasiswas',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2026_01_28_172132_add_min_bayar_to_program_kelas',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2026_01_28_181644_add_feeder_columns_to_master_kurikulums',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2026_01_28_182103_modify_id_semester_length_in_master_kurikulums',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2026_01_28_182824_add_prasyarat_to_kurikulum_mata_kuliah',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2026_01_28_185449_create_ref_skala_nilai_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2026_01_29_025555_create_ref_person_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2026_01_29_025602_create_ref_person_role_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2026_01_29_025607_create_trx_person_role_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2026_01_29_025612_create_trx_dosen_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2026_01_29_025618_create_trx_pegawai_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2026_01_29_025622_create_ref_gelar_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2026_01_29_025627_create_trx_person_gelar_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2026_01_29_031749_create_ref_jabatan_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2026_01_29_031839_create_trx_person_jabatan_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2026_01_30_013833_upgrade_trx_dosen_structure',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2026_01_30_014702_drop_old_dosens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2026_01_30_034434_finalize_user_person_ssot_architecture',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2026_01_30_041810_cleanup_mahasiswa_redundant_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2026_01_30_042723_remove_nama_dekan',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2026_01_30_152532_add_columnto_dosen',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2026_01_30_232040_add_is_paket_to_prodi_and_krs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2026_01_31_020110_create_financial_adjustment_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2026_01_31_110933_audit_log_keuangan',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2026_02_01_001920_add_column_person',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2026_02_01_002108_add_columns_to_ref_person_and_dosen',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2026_02_04_124848_ref_komponen_nilai',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2026_02_04_134114_delete_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2026_02_04_135606_create_lpm_standars_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2026_02_04_143113_create_table_lpm_iku_targets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2026_02_04_144741_create_lpm_edom_jawaban',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2026_02_05_134446_jadwal_kuliah',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2026_02_05_140007_akademik_ekuivalensi',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2026_02_05_140920_krs_detail',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2026_02_06_145118_create_master_mata_kuliahs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2026_02_06_183059_change_krs_detail',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2026_02_06_202925_create_akademik_grade_revision_logs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2026_02_06_204350_create_perkuliahan_sesi',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2026_03_08_062753_fix_sanctum_for_uuid',1);

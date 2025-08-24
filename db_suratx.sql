-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 24, 2025 at 09:14 PM
-- Server version: 8.0.42
-- PHP Version: 8.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_suratx`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'bima', 'bima@gmail.com', '$2y$12$7FgOwOifLwdTydpkclpcx.YJdK5PF1jvjn699GSlWN8He73n29c5u', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `history_surat`
--

CREATE TABLE `history_surat` (
  `id` bigint UNSIGNED NOT NULL,
  `surat_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `aksi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu_aksi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `history_surat`
--

INSERT INTO `history_surat` (`id`, `surat_id`, `user_id`, `aksi`, `waktu_aksi`) VALUES
(1, 1, 2, 'baru1', '2025-08-17 18:23:02');

-- --------------------------------------------------------

--
-- Table structure for table `jenis_surat`
--

CREATE TABLE `jenis_surat` (
  `id` bigint UNSIGNED NOT NULL,
  `kode_jenis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_jenis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `template_fields` json DEFAULT NULL,
  `template_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jenis_surat`
--

INSERT INTO `jenis_surat` (`id`, `kode_jenis`, `nama_jenis`, `deskripsi`, `template_fields`, `template_file`, `created_at`, `updated_at`) VALUES
(1, 'JNS-001', 'Template Berita acara serah terima pekerjaan(Go-Live)', 'Formulir Permohonan Pelaksanaan Pekerjaan (GO-Live)', '[{\"fields\": [{\"columns\": {\"8de0395b-d3e4-441a-987b-1d3d78c61931\": {\"column_name\": null}}, \"field_name\": \"judul-surat\", \"field_type\": \"text\", \"grouped_columns\": {\"3996cdac-ab34-410b-accf-054cb8abc83a\": {\"rows\": {\"66d092ff-b407-41c6-9ba6-ec71880c52bf\": {\"value1\": null}}, \"group_title\": null}}}, {\"columns\": {\"688b2c15-d52e-4ce5-b12b-f2d0e0aa0ae5\": {\"column_name\": null}}, \"field_name\": \"hari-surat\", \"field_type\": \"text\", \"grouped_columns\": {\"1989fbe6-e43f-404f-a7e0-3ecf7952ea34\": {\"rows\": {\"934e5db5-5ee2-437f-8590-796da2b7c2fb\": {\"value1\": null}}, \"group_title\": null}}}, {\"columns\": {\"29e048bb-1e21-4c75-99b8-d1ae8ae209db\": {\"column_name\": null}}, \"field_name\": \"tanggal\", \"field_type\": \"text\", \"grouped_columns\": {\"383e0895-291e-4142-a98e-fbe3138aa0b5\": {\"rows\": {\"23f06d26-8dfe-4d4b-acc8-623bd7128043\": {\"value1\": null}}, \"group_title\": null}}}, {\"columns\": {\"d2447973-5dda-4279-bd7d-971f70b43462\": {\"column_name\": null}}, \"field_name\": \"bulan\", \"field_type\": \"text\", \"grouped_columns\": {\"905512fe-82e8-4a2e-9656-7e3e934de1f6\": {\"rows\": {\"5d9e9cdf-2566-4616-8bc3-f62d6ed230cc\": {\"value1\": null}}, \"group_title\": null}}}, {\"columns\": {\"c2e8a32d-98b6-4320-bea1-dd4b87e683bf\": {\"column_name\": null}}, \"field_name\": \"tahun\", \"field_type\": \"text\", \"grouped_columns\": {\"fb7fb989-6a57-44de-992c-672024b22f19\": {\"rows\": {\"e4fe81a9-8b3f-43e9-8dc1-d0c589afedb3\": {\"value1\": null}}, \"group_title\": null}}}], \"section_title\": \"Judul\"}, {\"fields\": [{\"columns\": {\"a3191a0c-60c1-431e-b0c9-0ae5d6f4e971\": {\"column_name\": null}}, \"field_name\": \"nama-diwakili-pihak-pertama\", \"field_type\": \"text\", \"grouped_columns\": {\"6e53ab0b-9cf4-4d3c-ba79-b34ee6396842\": {\"rows\": {\"c5764acf-5379-4cb8-a1e7-f066d18be9ce\": {\"value1\": null}}, \"group_title\": null}}}, {\"columns\": {\"ccf24787-3e16-45ec-9ab3-89ca0c413b4c\": {\"column_name\": null}}, \"field_name\": \"jabatan-pihak-pertama\", \"field_type\": \"text\", \"grouped_columns\": {\"cabea697-5349-4e73-aee7-a457ef5a3122\": {\"rows\": {\"eb544a62-2102-4246-956b-88d2cd8a3ac7\": {\"value1\": null}}, \"group_title\": null}}}, {\"columns\": {\"98a7d02e-d9cc-47e7-84a4-d5856c2383f4\": {\"column_name\": null}}, \"field_name\": \"divisi-pihak-pertama\", \"field_type\": \"text\", \"grouped_columns\": {\"2753b742-c6f0-4ca9-931c-1812c7cfe063\": {\"rows\": {\"18f6cb9d-f736-49aa-8173-6d39742a413d\": {\"value1\": null}}, \"group_title\": null}}}, {\"columns\": {\"acee9efc-b1bc-4cd2-88c8-da2d3996b164\": {\"column_name\": null}}, \"field_name\": \"nama-diwakili-pihak-kedua\", \"field_type\": \"text\", \"grouped_columns\": {\"cc3679ab-5bb7-4115-8231-893b4ca2d933\": {\"rows\": {\"451a8222-568f-4470-8503-da39d29cca1f\": {\"value1\": null}}, \"group_title\": null}}}, {\"columns\": {\"162b9296-1433-4e5e-a1d2-f4e186577b85\": {\"column_name\": null}}, \"field_name\": \"jabatan-pihak-kedua\", \"field_type\": \"text\", \"grouped_columns\": {\"f736954e-aca8-49d1-a69e-65c7f6dcd9c6\": {\"rows\": {\"8b1997d3-c3bb-41ac-9c52-583118844080\": {\"value1\": null}}, \"group_title\": null}}}, {\"columns\": {\"b04765c4-e989-41aa-a90f-b0e9b300b1c5\": {\"column_name\": null}}, \"field_name\": \"perusahaan-PT-ABC\", \"field_type\": \"text\", \"grouped_columns\": {\"a51766f8-559a-4f6d-9079-8407321b1da2\": {\"rows\": {\"9a000357-6f8f-438b-a2e8-6ee6463ae735\": {\"value1\": null}}, \"group_title\": null}}}, {\"columns\": {\"826b8aa1-6132-4670-af35-8ee1aa6b0e16\": {\"column_name\": null}}, \"field_name\": \"nomor-surat-kerjasama\", \"field_type\": \"text\", \"grouped_columns\": {\"84c6e7f5-3c2c-42a7-8457-3e8bfb3e3f67\": {\"rows\": {\"357fc05e-3bb5-4dd4-89d4-4f4db56ff297\": {\"value1\": null}}, \"group_title\": null}}}, {\"columns\": {\"0510b641-a5d1-4d7f-9aa2-048a4dc9b91f\": {\"column_name\": null}}, \"field_name\": \"kebutuhan-untuk\", \"field_type\": \"text\", \"grouped_columns\": {\"49ca09fd-661d-4fc3-b7f3-3e1eab9f2e46\": {\"rows\": {\"662efb2b-ca4f-4f42-8472-b99b4a8e5e66\": {\"value1\": null}}, \"group_title\": null}}}, {\"columns\": {\"d3e252ee-138a-4c43-bf21-f9cdf54a0b5e\": {\"column_name\": null}}, \"field_name\": \"catatan\", \"field_type\": \"text\", \"grouped_columns\": {\"5a4ed324-c77d-429f-af83-d7d6f4097b53\": {\"rows\": {\"3417ad8c-b91a-4973-91de-b29f5c769c94\": {\"value1\": null}}, \"group_title\": null}}}], \"section_title\": \"Isi-Surat\"}, {\"fields\": [{\"columns\": {\"00ddc5da-69b8-4258-a266-9a832feaac5e\": {\"column_name\": null}}, \"field_name\": \"nama-lengkap-diwakili-pihak-pertama\", \"field_type\": \"text\", \"grouped_columns\": {\"c54c1a40-44b4-4036-85fa-afd04e2c1d8e\": {\"rows\": {\"a2659900-6b51-4a46-9d5c-1c7e94399dae\": {\"value1\": null}}, \"group_title\": null}}}, {\"columns\": {\"ee88af17-dab7-4cbe-a100-5b0101739d67\": {\"column_name\": null}}, \"field_name\": \"jabatan-pihak-pertama\", \"field_type\": \"text\", \"grouped_columns\": {\"6be7ad8a-b78f-4422-9895-8a49036c0143\": {\"rows\": {\"21ac4e0f-824a-4985-997f-3de781708f87\": {\"value1\": null}}, \"group_title\": null}}}, {\"columns\": {\"c8d432cb-0e49-4804-b9e3-23088f9e7f00\": {\"column_name\": null}}, \"field_name\": \"ttd-pihak-pertama\", \"field_type\": \"signature\", \"grouped_columns\": {\"353e6bfb-102c-4f37-bd58-3098b6d27f0f\": {\"rows\": {\"fa25bcda-bf2c-4b84-9deb-af40b3d785fc\": {\"value1\": null}}, \"group_title\": null}}}, {\"columns\": {\"47b4edfd-b790-4415-b497-dd1f424bb029\": {\"column_name\": null}}, \"field_name\": \"nama-diwakili-pihak-kedua\", \"field_type\": \"text\", \"grouped_columns\": {\"389d8535-0a82-4f2a-b5d8-c020f0cf1cf9\": {\"rows\": {\"f995495c-1c8f-4143-91d9-406f62db4630\": {\"value1\": null}}, \"group_title\": null}}}, {\"columns\": {\"3126bd28-8531-4175-84b1-42585ef3ed39\": {\"column_name\": null}}, \"field_name\": \"jabatan-pihak-kedua\", \"field_type\": \"text\", \"grouped_columns\": {\"0fd0cfd4-414d-4960-8917-5946394d8b28\": {\"rows\": {\"413507ab-fa57-4e62-ba30-575cbbbf3064\": {\"value1\": null}}, \"group_title\": null}}}, {\"columns\": {\"624a46eb-d2a0-42b4-883d-d3eaa95f1d13\": {\"column_name\": null}}, \"field_name\": \"ttd-pihak-kedua\", \"field_type\": \"signature\", \"grouped_columns\": {\"886b87a8-b55e-444f-bf2e-f91b000809d4\": {\"rows\": {\"911ce027-2967-4777-8ed4-c0c2253b495e\": {\"value1\": null}}, \"group_title\": null}}}], \"section_title\": \"Persetujuan\"}]', 'template-files/Template-Berita-acara-serah-terima-pekerjaan(Go-Live).docx', '2025-08-17 18:20:18', '2025-08-17 18:20:18');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

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
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_05_12_085508_create_jenis_surat_table', 1),
(5, '2025_05_12_085532_create_surats_table', 1),
(6, '2025_05_12_085537_create_history_surats_table', 1),
(7, '2025_05_21_115759_create_admins_table', 1),
(8, '2025_05_22_135621_add_file_columns_to_surat_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('f4yuZWBY3YBa96kVLMJ3txeIINXLwl0gdiIO6izl', 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiTWdJaksxWHk1ZldKWHZGNkVHQTJlVGw5MGtIeGhtcElNYk8xeWJKVCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4OCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4OC9hZG1pbiI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1756037347),
('fjyKyhKKHAjCCpV7zsuwxWTgzmWnvZSuE5pdR5tM', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieWdvYlZiUG1UR1U0Q2x2SHpWV1h2QkY4ODhsdVdKUnJZQWdwS3VkSiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4OCI7fX0=', 1756042685),
('n6XSn2wYLrN1jDBhag72jvH0d7Q8aSHnQtHnjqpR', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo4OntzOjY6Il90b2tlbiI7czo0MDoiYUNaTnkzaDRyeXlDWnRKY0NXRGN5WkxZb2xNRzh5VjlzcDhlQ1lZMyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTM6Imh0dHA6Ly9zdXJhdHgtZXhpbS50ZXN0OjgwODAvc3VyYXQvMS9wcmludD9mb3JtYXQ9cGRmIjt9czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0OToiaHR0cDovL3N1cmF0eC1leGltLnRlc3Q6ODA4MC9zdXJhdC9jcmVhdGUvSk5TLTAwMSI7fXM6NTI6ImxvZ2luX2FkbWluXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE5OiJwYXNzd29yZF9oYXNoX2FkbWluIjtzOjYwOiIkMnkkMTIkN0ZnT3dPaWZMd2RUeWRwa2NscGN4LllKZEs1UEYxanZqbjY5OUdTbFdOOEhlNzNuMjljNXUiO3M6ODoiZmlsYW1lbnQiO2E6MDp7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1755454999);

-- --------------------------------------------------------

--
-- Table structure for table `surat`
--

CREATE TABLE `surat` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_surat_id` bigint UNSIGNED NOT NULL,
  `nomor_surat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isi_data` json NOT NULL,
  `file_surat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `file_docx` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_pdf` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `surat`
--

INSERT INTO `surat` (`id`, `user_id`, `username`, `jenis_surat_id`, `nomor_surat`, `isi_data`, `file_surat`, `created_at`, `updated_at`, `file_docx`, `file_pdf`) VALUES
(1, 2, NULL, 1, 'SURAT-EXIM-01', '{\"bulan\": \"t32t32\", \"tahun\": \"3t32t2t\", \"catatan\": \"g4343g43g34\", \"tanggal\": \"43432\", \"hari-surat\": \"3r24g24gg4\", \"nama_surat\": \"baru1\", \"judul-surat\": \"fewfewfew\", \"kebutuhan-untuk\": \"t34t43\", \"ttd-pihak-kedua\": \"signatures/i4zsXxpnBNvJkjUZirjHsOSr1pl59P2nf5GESJjR.png\", \"perusahaan-PT-ABC\": \"34t43\", \"jabatan-pihak-kedua\": \"5h35h35h\", \"divisi-pihak-pertama\": \"t23t32\", \"jabatan-pihak-pertama\": \"5353h53h\", \"nomor-surat-kerjasama\": \"34t34t43\", \"nama-diwakili-pihak-kedua\": \"h55h\", \"nama-diwakili-pihak-pertama\": \"3232\", \"nama-lengkap-diwakili-pihak-pertama\": \"h3h4334\"}', NULL, '2025-08-17 18:23:02', '2025-08-17 18:23:02', 'generated/template-berita-acara-serah-terima-pekerjaango-live-1755454980.docx', 'generated/template-berita-acara-serah-terima-pekerjaango-live-1755454980.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Test User', 'test@example.com', '2025-08-17 17:58:03', '$2y$12$ZfmGdVXrzmaKkUaaAVdv4u.abawpBC0IHyGzqw7psG.o3h1ZigFtW', 'lxARwZu5qa', '2025-08-17 17:58:03', '2025-08-17 17:58:03'),
(2, 'Admin', 'admin@gmail.com', NULL, '$2y$12$7FgOwOifLwdTydpkclpcx.YJdK5PF1jvjn699GSlWN8He73n29c5u', NULL, '2025-08-17 17:59:31', '2025-08-17 17:59:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `history_surat`
--
ALTER TABLE `history_surat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `history_surat_surat_id_foreign` (`surat_id`),
  ADD KEY `history_surat_user_id_foreign` (`user_id`);

--
-- Indexes for table `jenis_surat`
--
ALTER TABLE `jenis_surat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jenis_surat_kode_jenis_unique` (`kode_jenis`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `surat`
--
ALTER TABLE `surat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `surat_user_id_foreign` (`user_id`),
  ADD KEY `surat_jenis_surat_id_foreign` (`jenis_surat_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `history_surat`
--
ALTER TABLE `history_surat`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jenis_surat`
--
ALTER TABLE `jenis_surat`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `surat`
--
ALTER TABLE `surat`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `history_surat`
--
ALTER TABLE `history_surat`
  ADD CONSTRAINT `history_surat_surat_id_foreign` FOREIGN KEY (`surat_id`) REFERENCES `surat` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `history_surat_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `surat`
--
ALTER TABLE `surat`
  ADD CONSTRAINT `surat_jenis_surat_id_foreign` FOREIGN KEY (`jenis_surat_id`) REFERENCES `jenis_surat` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `surat_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

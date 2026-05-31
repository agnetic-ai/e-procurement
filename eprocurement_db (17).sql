-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2026 at 07:07 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eprocurement_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `approval_levels`
--

CREATE TABLE `approval_levels` (
  `id` int(11) NOT NULL,
  `module_code` varchar(10) NOT NULL,
  `level` int(11) NOT NULL,
  `level_name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approval_levels`
--

INSERT INTO `approval_levels` (`id`, `module_code`, `level`, `level_name`, `created_at`) VALUES
(1, 'PR', 1, 'Manager Approval', '2025-12-14 08:47:27'),
(2, 'PR', 2, 'Head Approval', '2025-12-14 08:47:27'),
(3, 'PR', 3, 'Director Approval', '2025-12-14 08:47:27'),
(4, 'PO', 1, 'Manager Approval', '2025-12-14 01:47:27'),
(5, 'PO', 2, 'Head Approval', '2025-12-14 01:47:27'),
(6, 'PO', 3, 'Director Approval', '2025-12-14 01:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `approval_level_roles`
--

CREATE TABLE `approval_level_roles` (
  `id` int(11) NOT NULL,
  `module_code` varchar(10) NOT NULL,
  `approval_level_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approval_level_roles`
--

INSERT INTO `approval_level_roles` (`id`, `module_code`, `approval_level_id`, `role_id`, `created_at`) VALUES
(1, 'PR', 1, 4, '2025-12-14 08:48:05'),
(2, 'PR', 2, 7, '2025-12-14 08:48:05'),
(3, 'PR', 3, 6, '2025-12-14 08:48:05'),
(4, 'PO', 4, 4, '2025-12-14 01:48:05'),
(5, 'PO', 5, 7, '2025-12-14 01:48:05'),
(6, 'PO', 6, 6, '2025-12-14 01:48:05');

-- --------------------------------------------------------

--
-- Table structure for table `approval_rules`
--

CREATE TABLE `approval_rules` (
  `id` int(11) NOT NULL,
  `module_code` varchar(10) NOT NULL,
  `min_amount` decimal(15,2) NOT NULL,
  `max_amount` decimal(15,2) DEFAULT NULL,
  `total_level` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approval_rules`
--

INSERT INTO `approval_rules` (`id`, `module_code`, `min_amount`, `max_amount`, `total_level`, `is_active`, `created_at`) VALUES
(1, 'PR', '0.00', '10000000.00', 1, 1, '2025-12-14 08:46:57'),
(2, 'PR', '10000001.00', '50000000.00', 2, 1, '2025-12-14 08:46:57'),
(3, 'PR', '50000001.00', NULL, 3, 1, '2025-12-14 08:46:57'),
(4, 'PO', '0.00', '20000000.00', 1, 1, '2025-12-14 01:46:57'),
(5, 'PO', '20000001.00', '60000000.00', 2, 1, '2025-12-14 01:46:57'),
(6, 'PO', '60000001.00', NULL, 3, 1, '2025-12-14 01:46:57');

-- --------------------------------------------------------

--
-- Table structure for table `asset_assignments`
--

CREATE TABLE `asset_assignments` (
  `id` int(11) NOT NULL,
  `asset_unit_id` int(11) NOT NULL COMMENT 'FK ke asset_units.id',
  `employee_id` int(11) NOT NULL COMMENT 'FK ke employees.id (penerima aset)',
  `assigned_at` datetime NOT NULL DEFAULT current_timestamp(),
  `returned_at` datetime DEFAULT NULL,
  `assigned_by` int(11) NOT NULL COMMENT 'FK ke users.id (petugas/admin)',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Histori distribusi dan pengembalian aset';

--
-- Dumping data for table `asset_assignments`
--

INSERT INTO `asset_assignments` (`id`, `asset_unit_id`, `employee_id`, `assigned_at`, `returned_at`, `assigned_by`, `notes`, `created_at`) VALUES
(2, 8, 25, '2026-01-24 10:52:00', '2026-01-24 00:00:00', 1, 'ASIGN LAPTOP UNUTK ARIF KURNIAWAN', '2026-01-24 03:52:00'),
(3, 9, 2, '2026-01-24 10:54:10', '2026-01-24 00:00:00', 1, 'ASSET UNUTK BUDI SANTOSO', '2026-01-24 03:54:10'),
(4, 10, 50, '2026-01-24 11:05:16', '2026-01-24 00:00:00', 1, 'bayuu', '2026-01-24 04:05:16'),
(5, 1, 25, '2026-01-24 13:29:36', '2026-01-24 00:00:00', 1, 'Arif', '2026-01-24 06:29:36'),
(6, 11, 24, '2026-01-24 13:36:46', '2026-01-24 00:00:00', 1, 'oioia', '2026-01-24 06:36:46'),
(7, 7, 47, '2026-01-24 13:36:59', '2026-01-24 00:00:00', 1, 'gbfvdc', '2026-01-24 06:36:59'),
(8, 15, 15, '2026-01-24 15:21:07', '2026-01-24 00:00:00', 1, 'BRANG KE OKI', '2026-01-24 08:21:07'),
(9, 17, 47, '2026-01-27 00:08:29', '2026-01-27 00:00:00', 1, '', '2026-01-26 17:08:29');

-- --------------------------------------------------------

--
-- Table structure for table `asset_units`
--

CREATE TABLE `asset_units` (
  `id` int(11) NOT NULL,
  `goods_receipt_detail_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `status_code` varchar(30) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL COMMENT 'FK ke employees.id (pemilik aset)',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `asset_units`
--

INSERT INTO `asset_units` (`id`, `goods_receipt_detail_id`, `product_id`, `serial_number`, `status_code`, `employee_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'SN-01', 'ASSET_ASSIGNED', 25, '2026-01-22 15:20:49', '2026-01-24 06:29:36'),
(2, 1, 1, 'SN-02', 'ASSET_IN_STOCK', NULL, '2026-01-22 15:20:49', '2026-01-22 15:20:49'),
(3, 1, 1, 'SN-03', 'ASSET_IN_STOCK', NULL, '2026-01-22 15:20:49', '2026-01-22 15:20:49'),
(4, 1, 1, 'SN-04', 'ASSET_IN_STOCK', NULL, '2026-01-22 15:20:49', '2026-01-22 15:20:49'),
(5, 1, 1, 'SN-05', 'ASSET_IN_STOCK', NULL, '2026-01-22 15:20:49', '2026-01-22 15:20:49'),
(6, 2, 2, 'SN-06', 'ASSET_IN_STOCK', NULL, '2026-01-22 15:20:49', '2026-01-22 15:20:49'),
(7, 2, 2, 'SN-07', 'ASSET_ASSIGNED', 47, '2026-01-22 15:20:49', '2026-01-24 06:36:59'),
(8, 4, 13, 'SN-2025-001', 'ASSET_ASSIGNED', 25, '2026-01-22 15:21:49', '2026-01-24 03:52:00'),
(9, 4, 13, 'SN-2025-002', 'ASSET_ASSIGNED', 2, '2026-01-22 15:21:49', '2026-01-24 03:54:10'),
(10, 4, 13, 'SN-2025-003', 'ASSET_ASSIGNED', 50, '2026-01-22 15:21:49', '2026-01-24 04:05:16'),
(11, 5, 2, 'SN-RM-1', 'ASSET_ASSIGNED', 24, '2026-01-22 16:52:53', '2026-01-24 06:36:46'),
(12, 7, 14, 'SN-ASUS-1', 'ASSET_IN_STOCK', NULL, '2026-01-24 08:04:18', '2026-01-24 08:04:18'),
(13, 7, 14, 'SN-ASUS-2', 'ASSET_IN_STOCK', NULL, '2026-01-24 08:04:18', '2026-01-24 08:04:18'),
(14, 7, 14, 'SN-ASUS-3', 'ASSET_IN_STOCK', NULL, '2026-01-24 08:04:18', '2026-01-24 08:04:18'),
(15, 10, 14, 'SN -ASUS-3', 'ASSET_ASSIGNED', 15, '2026-01-24 08:06:19', '2026-01-24 08:21:07'),
(16, 12, 1, 'SEIALNUMBER 1', 'ASSET_IN_STOCK', NULL, '2026-01-26 17:04:14', '2026-01-26 17:04:14'),
(17, 13, 14, 'SEIALNUMBER 2', 'ASSET_ASSIGNED', 47, '2026-01-26 17:04:14', '2026-01-26 17:08:29'),
(18, 13, 14, 'SEIALNUMBER 3', 'ASSET_IN_STOCK', NULL, '2026-01-26 17:04:14', '2026-01-26 17:04:14'),
(19, 14, 14, 'SER-002', 'ASSET_IN_STOCK', NULL, '2026-05-09 09:11:58', '2026-05-09 09:11:58');

-- --------------------------------------------------------

--
-- Table structure for table `business_types`
--

CREATE TABLE `business_types` (
  `id` int(11) NOT NULL,
  `type_code` varchar(10) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business_types`
--

INSERT INTO `business_types` (`id`, `type_code`, `type_name`, `description`, `created_at`) VALUES
(1, 'SUP', 'Supplier Material', 'Penyedia bahan baku dan material konstruksi', '2025-12-09 09:44:03'),
(2, 'CONT', 'Contractor', 'Kontraktor pekerjaan konstruksi dan bangunan', '2025-12-09 09:44:03'),
(3, 'SERV', 'Service Provider', 'Penyedia jasa maintenance, cleaning, security', '2025-12-09 09:44:03'),
(4, 'LOG', 'Logistics', 'Penyedia jasa transportasi dan logistik', '2025-12-09 09:44:03'),
(5, 'CONS', 'Consultant', 'Konsultan teknis, hukum, dan manajemen', '2025-12-09 09:44:03'),
(6, 'IT', 'IT Vendor', 'Penyedia perangkat keras dan lunak IT', '2025-12-09 09:44:03'),
(7, 'OFF', 'Office Supplier', 'Penyedia perlengkapan kantor', '2025-12-09 09:44:03');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_code` varchar(100) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_code`, `name`, `description`) VALUES
(1, 'products', 'Elektronik & IT', 'Perangkat elektronik dan teknologi informasi'),
(2, 'products', 'Alat Tulis Kantor', 'Kebutuhan alat tulis dan perlengkapan kantor'),
(3, 'products', 'Furniture', 'Perabotan dan mebel kantor'),
(4, 'products', 'Jasa', 'Layanan dan jasa profesional');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `city_name` varchar(100) NOT NULL,
  `province_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `city_name`, `province_name`, `created_at`) VALUES
(1, 'Jakarta Selatan', 'DKI Jakarta', '2025-12-09 09:44:03'),
(2, 'Jakarta Pusat', 'DKI Jakarta', '2025-12-09 09:44:03'),
(3, 'Jakarta Utara', 'DKI Jakarta', '2025-12-09 09:44:03'),
(4, 'Jakarta Barat', 'DKI Jakarta', '2025-12-09 09:44:03'),
(5, 'Jakarta Timur', 'DKI Jakarta', '2025-12-09 09:44:03'),
(6, 'Bandung', 'Jawa Barat', '2025-12-09 09:44:03'),
(7, 'Surabaya', 'Jawa Timur', '2025-12-09 09:44:03'),
(8, 'Medan', 'Sumatera Utara', '2025-12-09 09:44:03'),
(9, 'Semarang', 'Jawa Tengah', '2025-12-09 09:44:03'),
(10, 'Makassar', 'Sulawesi Selatan', '2025-12-09 09:44:03'),
(11, 'Denpasar', 'Bali', '2025-12-09 09:44:03'),
(12, 'Yogyakarta', 'DI Yogyakarta', '2025-12-09 09:44:03'),
(13, 'Malang', 'Jawa Timur', '2025-12-09 09:44:03'),
(14, 'Bekasi', 'Jawa Barat', '2025-12-09 09:44:03'),
(15, 'Tangerang', 'Banten', '2025-12-09 09:44:03');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `employee_code` varchar(30) NOT NULL COMMENT 'EMP-YYYY-NNN',
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `department` varchar(100) NOT NULL,
  `POSITION` varchar(100) NOT NULL,
  `employment_status_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `join_date` date NOT NULL,
  `resign_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Master data karyawan (HR) untuk distribusi dan kepemilikan aset';

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_code`, `full_name`, `email`, `phone`, `department`, `POSITION`, `employment_status_code`, `join_date`, `resign_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'EMP-2026-001', 'Andi Pratama', 'andi.pratama@company.com', '0812000001', 'IT', 'Software Engineer', 'EMP_ACTIVE', '2023-01-10', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(2, 'EMP-2026-002', 'Budi Santoso', 'budi.santoso@company.com', '0812000002', 'IT', 'System Analyst', 'EMP_ACTIVE', '2022-08-15', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(3, 'EMP-2026-003', 'Citra Lestari', 'citra.lestari@company.com', '0812000003', 'Finance', 'Accountant', 'EMP_ACTIVE', '2021-06-01', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(4, 'EMP-2026-004', 'Dewi Anggraini', 'dewi.anggraini@company.com', '0812000004', 'Finance', 'Finance Manager', 'EMP_ACTIVE', '2020-03-12', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(5, 'EMP-2026-005', 'Eko Saputra', 'eko.saputra@company.com', '0812000005', 'HR', 'HR Staff', 'EMP_ACTIVE', '2023-02-20', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(6, 'EMP-2026-006', 'Fajar Nugroho', 'fajar.nugroho@company.com', '0812000006', 'HR', 'HR Manager', 'EMP_ACTIVE', '2019-11-05', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(7, 'EMP-2026-007', 'Gilang Ramadhan', 'gilang.r@company.com', '0812000007', 'Procurement', 'Procurement Staff', 'EMP_ACTIVE', '2022-01-17', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(8, 'EMP-2026-008', 'Hendra Wijaya', 'hendra.w@company.com', '0812000008', 'Procurement', 'Procurement Manager', 'EMP_ACTIVE', '2018-09-01', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(9, 'EMP-2026-009', 'Intan Permata', 'intan.p@company.com', '0812000009', 'IT', 'QA Engineer', 'EMP_ACTIVE', '2023-05-09', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(10, 'EMP-2026-010', 'Joko Prasetyo', 'joko.p@company.com', '0812000010', 'Warehouse', 'Warehouse Staff', 'EMP_ACTIVE', '2021-12-01', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(11, 'EMP-2026-011', 'Kevin Adrian', 'kevin.a@company.com', '0812000011', 'IT', 'DevOps Engineer', 'EMP_ACTIVE', '2022-04-18', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(12, 'EMP-2026-012', 'Linda Marlina', 'linda.m@company.com', '0812000012', 'Finance', 'AP Officer', 'EMP_ACTIVE', '2023-07-03', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(13, 'EMP-2026-013', 'Maya Putri', 'maya.p@company.com', '0812000013', 'HR', 'Recruiter', 'EMP_ACTIVE', '2021-10-11', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(14, 'EMP-2026-014', 'Nanda Sapriadi', 'nanda.s@company.com', '0812000014', 'Warehouse', 'Inventory Control', 'EMP_ACTIVE', '2020-06-25', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(15, 'EMP-2026-015', 'Oki Setiawan', 'oki.s@company.com', '0812000015', 'IT', 'Network Engineer', 'EMP_ACTIVE', '2019-02-14', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(16, 'EMP-2026-016', 'Putri Ayu', 'putri.a@company.com', '0812000016', 'Finance', 'Finance Staff', 'EMP_ACTIVE', '2022-11-30', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(17, 'EMP-2026-017', 'Raka Firmansyah', 'raka.f@company.com', '0812000017', 'Procurement', 'Buyer', 'EMP_ACTIVE', '2023-01-02', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(18, 'EMP-2026-018', 'Sari Wulandari', 'sari.w@company.com', '0812000018', 'HR', 'People Development', 'EMP_ACTIVE', '2021-04-19', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(19, 'EMP-2026-019', 'Taufik Hidayat', 'taufik.h@company.com', '0812000019', 'Warehouse', 'Logistic Supervisor', 'EMP_ACTIVE', '2018-07-07', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(20, 'EMP-2026-020', 'Umar Fauzi', 'umar.f@company.com', '0812000020', 'IT', 'IT Support', 'EMP_ACTIVE', '2024-01-05', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(21, 'EMP-2026-021', 'Vina Kartika', 'vina.k@company.com', '0812000021', 'Finance', 'Tax Officer', 'EMP_ACTIVE', '2020-08-13', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(22, 'EMP-2026-022', 'Wahyu Prakoso', 'wahyu.p@company.com', '0812000022', 'Procurement', 'Vendor Management', 'EMP_ACTIVE', '2019-05-20', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(23, 'EMP-2026-023', 'Yoga Pratama', 'yoga.p@company.com', '0812000023', 'IT', 'Backend Developer', 'EMP_ACTIVE', '2022-09-09', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(24, 'EMP-2026-024', 'Zahra Nabila', 'zahra.n@company.com', '0812000024', 'HR', 'HR Admin', 'EMP_ACTIVE', '2023-03-27', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(25, 'EMP-2026-025', 'Arif Kurniawan', 'arif.k@company.com', '0812000025', 'Warehouse', 'Asset Controller', 'EMP_ACTIVE', '2020-10-10', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(26, 'EMP-2026-026', 'Bella Salsabila', 'bella.s@company.com', '0812000026', 'Finance', 'Finance Analyst', 'EMP_ACTIVE', '2021-01-15', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(27, 'EMP-2026-027', 'Dimas Setyo', 'dimas.s@company.com', '0812000027', 'IT', 'Frontend Developer', 'EMP_ACTIVE', '2022-06-21', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(28, 'EMP-2026-028', 'Erwin Maulana', 'erwin.m@company.com', '0812000028', 'Procurement', 'Procurement Analyst', 'EMP_ACTIVE', '2023-08-01', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(29, 'EMP-2026-029', 'Fitri Handayani', 'fitri.h@company.com', '0812000029', 'HR', 'HR Business Partner', 'EMP_ACTIVE', '2019-12-09', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(30, 'EMP-2026-030', 'Galih Perdana', 'galih.p@company.com', '0812000030', 'Warehouse', 'Warehouse Admin', 'EMP_ACTIVE', '2021-09-14', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(31, 'EMP-2026-031', 'Hana Safitri', 'hana.s@company.com', '0812000031', 'Finance', 'Cashier', 'EMP_ACTIVE', '2020-02-03', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(32, 'EMP-2026-032', 'Ilham Akbar', 'ilham.a@company.com', '0812000032', 'IT', 'Security Engineer', 'EMP_ACTIVE', '2018-04-30', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(33, 'EMP-2026-033', 'Jihan Ramadhani', 'jihan.r@company.com', '0812000033', 'HR', 'HR Officer', 'EMP_ACTIVE', '2022-12-12', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(34, 'EMP-2026-034', 'Kurnia Putra', 'kurnia.p@company.com', '0812000034', 'Procurement', 'Contract Officer', 'EMP_ACTIVE', '2019-06-06', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(35, 'EMP-2026-035', 'Lukman Hakim', 'lukman.h@company.com', '0812000035', 'Warehouse', 'Warehouse Manager', 'EMP_ACTIVE', '2017-01-01', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(36, 'EMP-2026-036', 'Mega Puspita', 'mega.p@company.com', '0812000036', 'Finance', 'Finance Controller', 'EMP_ACTIVE', '2018-11-11', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(37, 'EMP-2026-037', 'Niko Alamsyah', 'niko.a@company.com', '0812000037', 'IT', 'Mobile Developer', 'EMP_ACTIVE', '2023-04-04', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(38, 'EMP-2026-038', 'Olin Permadi', 'olin.p@company.com', '0812000038', 'Procurement', 'Strategic Sourcing', 'EMP_ACTIVE', '2021-07-22', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(39, 'EMP-2026-039', 'Putra Mahendra', 'putra.m@company.com', '0812000039', 'Warehouse', 'Asset Custodian', 'EMP_ACTIVE', '2020-05-05', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(40, 'EMP-2026-040', 'Qori Aisyah', 'qori.a@company.com', '0812000040', 'HR', 'Talent Management', 'EMP_ACTIVE', '2022-02-02', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(41, 'EMP-2026-041', 'Rendy Kurnia', 'rendy.k@company.com', '0812000041', 'IT', 'IT Manager', 'EMP_ACTIVE', '2016-08-08', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(42, 'EMP-2026-042', 'Salsa Nuraini', 'salsa.n@company.com', '0812000042', 'Finance', 'Budget Officer', 'EMP_ACTIVE', '2021-03-03', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(43, 'EMP-2026-043', 'Teguh Santoso', 'teguh.s@company.com', '0812000043', 'Procurement', 'Procurement Supervisor', 'EMP_ACTIVE', '2019-10-10', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(44, 'EMP-2026-044', 'Ulfah Rahma', 'ulfah.r@company.com', '0812000044', 'HR', 'Organization Development', 'EMP_ACTIVE', '2020-12-12', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(45, 'EMP-2026-045', 'Vicky Firmansyah', 'vicky.f@company.com', '0812000045', 'IT', 'Database Administrator', 'EMP_ACTIVE', '2018-05-18', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(46, 'EMP-2026-046', 'Wulan Sari', 'wulan.s@company.com', '0812000046', 'Finance', 'Finance Supervisor', 'EMP_ACTIVE', '2017-07-07', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(47, 'EMP-2026-047', 'Yudi Hartono', 'yudi.h@company.com', '0812000047', 'Warehouse', 'Logistic Staff', 'EMP_ACTIVE', '2022-10-20', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(48, 'EMP-2026-048', 'Zaki Ramadhan', 'zaki.r@company.com', '0812000048', 'IT', 'Cloud Engineer', 'EMP_ACTIVE', '2023-06-06', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(49, 'EMP-2026-049', 'Anisa Rahman', 'anisa.r@company.com', '0812000049', 'HR', 'HR Generalist', 'EMP_ACTIVE', '2021-08-18', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58'),
(50, 'EMP-2026-050', 'Bayu Prakoso', 'bayu.p@company.com', '0812000050', 'Procurement', 'Procurement Planner', 'EMP_ACTIVE', '2020-04-14', NULL, NULL, '2026-01-23 16:30:58', '2026-01-23 16:30:58');

-- --------------------------------------------------------

--
-- Table structure for table `goods_receipts`
--

CREATE TABLE `goods_receipts` (
  `id` int(11) NOT NULL,
  `gr_number` varchar(20) NOT NULL,
  `purchase_order_id` int(11) NOT NULL,
  `receipt_date` date DEFAULT NULL,
  `received_by` int(11) DEFAULT NULL,
  `status_code` varchar(30) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `goods_receipts`
--

INSERT INTO `goods_receipts` (`id`, `gr_number`, `purchase_order_id`, `receipt_date`, `received_by`, `status_code`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'GR-2026-00001', 1, '2026-01-22', 1, 'GR_POSTED', 'GR Pertama untuk PO PO-2026-00001', '2026-01-22 15:20:49', '2026-01-22 15:20:49'),
(2, 'GR-2026-00002', 2, '2026-01-22', 1, 'GR_POSTED', 'GR unutk po PO-2026-00002', '2026-01-22 15:21:49', '2026-01-22 15:21:49'),
(3, 'GR-2026-00003', 1, '2026-01-22', 1, 'GR_POSTED', 'GR', '2026-01-22 16:52:53', '2026-01-22 16:52:53'),
(4, 'GR-2026-00004', 2, '2026-01-22', 1, 'GR_POSTED', '', '2026-01-22 16:56:31', '2026-01-22 16:56:31'),
(5, 'GR-2026-00005', 3, '2026-01-24', 1, 'GR_POSTED', 'GR KE 1 bimbingan ke 5', '2026-01-24 08:04:18', '2026-01-24 08:04:18'),
(7, 'GR-2026-00006', 3, '2026-01-24', 1, 'GR_POSTED', 'vgfsa', '2026-01-24 08:06:19', '2026-01-24 08:06:19'),
(8, 'GR-2026-00007', 4, '2026-01-27', 1, 'GR_POSTED', 'PENERIMAAN PERTAMA', '2026-01-26 17:04:14', '2026-01-26 17:04:14'),
(9, 'GR-2026-00008', 3, '2026-05-09', 1, 'GR_POSTED', 'SUB', '2026-05-09 09:11:58', '2026-05-09 09:11:58');

-- --------------------------------------------------------

--
-- Table structure for table `goods_receipt_details`
--

CREATE TABLE `goods_receipt_details` (
  `id` int(11) NOT NULL,
  `goods_receipt_id` int(11) NOT NULL,
  `purchase_order_detail_id` int(11) NOT NULL,
  `qty_received` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `goods_receipt_details`
--

INSERT INTO `goods_receipt_details` (`id`, `goods_receipt_id`, `purchase_order_detail_id`, `qty_received`, `created_at`) VALUES
(1, 1, 1, 5, '2026-01-22 15:20:49'),
(2, 1, 2, 2, '2026-01-22 15:20:49'),
(3, 2, 4, 6, '2026-01-22 15:21:49'),
(4, 2, 5, 3, '2026-01-22 15:21:49'),
(5, 3, 2, 1, '2026-01-22 16:52:53'),
(6, 4, 4, 4, '2026-01-22 16:56:31'),
(7, 5, 7, 3, '2026-01-24 08:04:18'),
(8, 5, 8, 3, '2026-01-24 08:04:18'),
(10, 7, 7, 1, '2026-01-24 08:06:19'),
(11, 7, 8, 1, '2026-01-24 08:06:19'),
(12, 8, 10, 1, '2026-01-26 17:04:14'),
(13, 8, 11, 2, '2026-01-26 17:04:14'),
(14, 9, 7, 1, '2026-05-09 09:11:58'),
(15, 9, 8, 1, '2026-05-09 09:11:58');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `purchase_order_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `status_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `verify_notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_number`, `purchase_order_id`, `vendor_id`, `invoice_date`, `due_date`, `total_amount`, `status_code`, `created_by`, `verified_by`, `verified_at`, `paid_at`, `notes`, `verify_notes`, `created_at`, `updated_at`) VALUES
(1, 'INV-SJA-00001', 1, 1, '2026-01-22', '2026-02-14', '59350000.00', 'INV_PAID', 2, 6, '2026-01-22 22:26:16', NULL, 'ok', 'verify', '2026-01-22 22:24:12', '2026-01-22 22:28:00'),
(2, 'INV-SJA-00002', 1, 1, '2026-01-22', '2026-02-14', '40850000.00', 'INV_REJECTED', 2, 6, '2026-01-22 22:27:03', NULL, '', 'reject', '2026-01-22 22:26:45', '2026-01-22 22:27:03'),
(4, 'INV-SJA-00003', 1, 1, '2026-01-22', '2026-01-31', '40850000.00', 'INV_PAID', 2, 6, '2026-01-22 23:49:49', NULL, 'INV KE 3', '', '2026-01-22 23:49:18', '2026-01-22 23:51:05'),
(5, 'INV-SJA-00004', 1, 1, '2026-01-22', '2026-02-07', '3660000.00', 'INV_PAID', 2, 6, '2026-01-22 23:54:25', NULL, 'invoice ke 4', 'ok ', '2026-01-22 23:53:56', '2026-01-22 23:55:13'),
(6, 'INV-GLB-001', 2, 4, '2026-01-30', '2026-02-14', '473000.00', 'INV_PAID', 2, 6, '2026-01-22 23:58:27', NULL, 'Discount dan kenaikan harga', 'OK masih masuk di budget', '2026-01-22 23:57:53', '2026-01-22 23:59:33'),
(7, 'INV-BIMBINGAN-KE-5', 3, 1, '2026-01-24', '2026-01-31', '45200000.00', 'INV_REJECTED', 2, 6, '2026-01-24 15:15:03', NULL, 'diskon asus', 'barang tidak sesuai', '2026-01-24 15:07:48', '2026-01-24 15:15:03'),
(8, 'BIMBINGAN-REJECT', 3, 1, '2026-01-31', '2026-02-07', '53595488.00', 'INV_PAID', 2, 6, '2026-01-24 15:18:03', NULL, '', 'SESUAI', '2026-01-24 15:16:53', '2026-01-24 15:19:30'),
(9, 'INV-VENDOR-1', 4, 1, '2026-01-27', '2026-02-27', '42697744.00', 'INV_PAID', 2, 6, '2026-01-27 00:07:01', NULL, 'OK', '', '2026-01-27 00:05:47', '2026-01-27 00:07:32');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_details`
--

CREATE TABLE `invoice_details` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `purchase_order_detail_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_details`
--

INSERT INTO `invoice_details` (`id`, `invoice_id`, `purchase_order_detail_id`, `product_id`, `qty`, `unit_price`, `subtotal`, `created_at`) VALUES
(1, 1, 1, 1, 3, '18500000.00', '55500000.00', '2026-01-22 22:24:12'),
(2, 1, 2, 2, 1, '3850000.00', '3850000.00', '2026-01-22 22:24:12'),
(3, 2, 1, 1, 2, '18500000.00', '37000000.00', '2026-01-22 22:26:45'),
(4, 2, 2, 2, 1, '3850000.00', '3850000.00', '2026-01-22 22:26:45'),
(5, 4, 1, 1, 2, '18500000.00', '37000000.00', '2026-01-22 23:49:18'),
(6, 4, 2, 2, 1, '3850000.00', '3850000.00', '2026-01-22 23:49:18'),
(7, 5, 2, 2, 1, '3660000.00', '3660000.00', '2026-01-22 23:53:56'),
(8, 6, 4, 4, 10, '44000.00', '440000.00', '2026-01-22 23:57:53'),
(9, 6, 5, 13, 3, '11000.00', '33000.00', '2026-01-22 23:57:53'),
(10, 7, 7, 14, 4, '10000000.00', '40000000.00', '2026-01-24 15:07:48'),
(11, 7, 8, 3, 4, '1300000.00', '5200000.00', '2026-01-24 15:07:48'),
(12, 8, 7, 14, 4, '12098872.00', '48395488.00', '2026-01-24 15:16:53'),
(13, 8, 8, 3, 4, '1300000.00', '5200000.00', '2026-01-24 15:16:53'),
(14, 9, 10, 1, 1, '18500000.00', '18500000.00', '2026-01-27 00:05:47'),
(15, 9, 11, 14, 2, '12098872.00', '24197744.00', '2026-01-27 00:05:47');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_price_audits`
--

CREATE TABLE `invoice_price_audits` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `invoice_detail_id` int(11) NOT NULL,
  `purchase_order_detail_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `po_unit_price` decimal(15,2) NOT NULL,
  `invoice_unit_price` decimal(15,2) NOT NULL,
  `price_diff` decimal(15,2) NOT NULL,
  `change_type` enum('DISCOUNT','PRICE_ADJUSTMENT') NOT NULL,
  `reason` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_price_audits`
--

INSERT INTO `invoice_price_audits` (`id`, `invoice_id`, `invoice_detail_id`, `purchase_order_detail_id`, `product_id`, `po_unit_price`, `invoice_unit_price`, `price_diff`, `change_type`, `reason`, `created_by`, `created_at`) VALUES
(1, 5, 7, 2, 2, '3850000.00', '3660000.00', '-190000.00', 'DISCOUNT', 'invoice ke 4', 2, '2026-01-22 23:53:56'),
(2, 6, 8, 4, 4, '45000.00', '44000.00', '-1000.00', 'DISCOUNT', 'Discount dan kenaikan harga', 2, '2026-01-22 23:57:53'),
(3, 6, 9, 5, 13, '10000.00', '11000.00', '1000.00', 'PRICE_ADJUSTMENT', 'Discount dan kenaikan harga', 2, '2026-01-22 23:57:53'),
(4, 7, 10, 7, 14, '12098872.00', '10000000.00', '-2098872.00', 'DISCOUNT', 'diskon asus', 2, '2026-01-24 15:07:48');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `menu_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `title`, `icon`, `url`, `parent_id`, `menu_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard', 'home', 'dashboard', NULL, 1, 1, '2025-12-11 12:59:30', '2025-12-11 12:59:30'),
(2, 'Master Data', 'database', '#', NULL, 2, 1, '2025-12-11 12:59:30', '2025-12-11 12:59:30'),
(3, 'Purchase Requisition', 'shopping-bag', 'purchase', NULL, 3, 1, '2025-12-11 12:59:30', '2025-12-14 16:26:53'),
(4, 'Vendor Management', 'users', 'vendors', 2, 1, 1, '2025-12-11 12:59:30', '2026-01-26 17:23:04'),
(5, 'User Management', 'user-circle', 'users', 2, 2, 0, '2025-12-11 12:59:30', '2025-12-11 14:49:27'),
(6, 'Product Management', 'package', 'product', 2, 3, 1, '2025-12-11 12:59:30', '2025-12-11 13:49:45'),
(7, 'Category Management', 'grid', 'categories', 2, 4, 0, '2025-12-11 12:59:30', '2025-12-11 14:49:36'),
(8, 'Management Order', 'clipboard', '#', NULL, 5, 1, '2025-12-17 09:07:00', '2026-02-15 02:09:01'),
(9, 'My Orders', 'user', 'ordersrequest', 8, 1, 1, '2025-12-17 09:08:58', '2026-02-16 15:37:07'),
(10, 'Approval Order', NULL, 'ordersapproval', 8, 2, 1, '2025-12-18 13:53:36', '2025-12-18 13:54:24'),
(11, 'Goods Receipts', 'package', 'goodsReceipts', NULL, 6, 1, '2025-12-29 04:31:53', '2025-12-29 04:35:13'),
(12, 'Purchase Order', 'file-text', 'purchaseOrders', NULL, 7, 1, '2025-12-30 08:50:13', '2025-12-31 03:33:29'),
(13, 'Approval PO', 'check-circle', 'ApprovalPo', 8, 3, 1, '2026-01-01 14:54:27', '2026-01-01 14:57:34'),
(14, 'Finance', 'dollar-sign', NULL, NULL, 8, 1, '2026-01-04 12:09:40', '2026-01-04 12:09:40'),
(15, 'Invoice', NULL, 'Invoice', 14, 1, 1, '2026-01-04 12:10:19', '2026-01-04 12:45:43'),
(16, 'Payment', NULL, 'Payment', 14, 3, 1, '2026-01-04 12:10:19', '2026-01-14 08:38:47'),
(17, 'Invoice Verification', NULL, 'InvoiceVerification', 14, 2, 1, '2026-01-14 04:38:03', '2026-01-14 08:41:40'),
(18, 'Asset Management', 'package', '#', NULL, 9, 1, '2026-01-23 16:50:25', '2026-01-24 06:24:29'),
(19, 'Overview Asset', NULL, 'Assets', 18, 1, 1, '2026-01-24 06:24:16', '2026-01-24 06:24:32'),
(20, 'Asset Stock', NULL, 'Assets/Stock', 18, 2, 1, '2026-01-24 06:24:16', '2026-01-24 06:29:00'),
(21, 'Asset History', NULL, 'Assets/History', 18, 3, 1, '2026-01-24 06:24:16', '2026-01-24 06:29:07');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `invoice_id`, `payment_date`, `paid_amount`, `payment_method`, `reference_number`, `payment_proof`, `notes`, `created_by`, `created_at`) VALUES
(1, 1, '2026-01-22', '59350000.00', 'Transfer', '1234567890', 'uploads/payments/2026/01/pay_69724200c7cbd5.29117769.pdf', 'pembayaran unutk invoice INV-SJA-00001', 6, '2026-01-22 22:28:00'),
(2, 4, '2026-01-22', '40850000.00', 'Transfer', '1234567890', 'uploads/payments/2026/01/pay_6972557902be78.05141123.pdf', 'Pembayran ke 2', 6, '2026-01-22 23:51:05'),
(3, 5, '2026-01-22', '3660000.00', 'Transfer', '1234567890', 'uploads/payments/2026/01/pay_69725671992c12.75377652.jpeg', 'pembayran ke 3', 6, '2026-01-22 23:55:13'),
(4, 6, '2026-01-22', '473000.00', 'Transfer', '3344556677', 'uploads/payments/2026/01/pay_69725775a2fe59.64754122.png', 'tf ke cimb niaga untuk invoice INV-GLB-001', 6, '2026-01-22 23:59:33'),
(5, 8, '2026-01-24', '53595488.00', 'Transfer', '1234567890', 'uploads/payments/2026/01/pay_69748092427271.72851941.pdf', 'PEMBAYRAN PERTAMA', 6, '2026-01-24 15:19:30'),
(6, 9, '2026-01-27', '42697744.00', 'Transfer', 'bebas', 'uploads/payments/2026/01/pay_69779f54805234.39553448.jpg', '', 6, '2026-01-27 00:07:32');

-- --------------------------------------------------------

--
-- Table structure for table `payment_terms`
--

CREATE TABLE `payment_terms` (
  `id` int(11) NOT NULL,
  `payment_code` varchar(20) NOT NULL,
  `payment_name` varchar(50) NOT NULL,
  `payment_days` int(11) DEFAULT 0,
  `payment_description` varchar(255) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT 1,
  `createdAt` datetime DEFAULT current_timestamp(),
  `updatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_terms`
--

INSERT INTO `payment_terms` (`id`, `payment_code`, `payment_name`, `payment_days`, `payment_description`, `isActive`, `createdAt`, `updatedAt`) VALUES
(1, 'NET7', 'Net 7', 7, 'Pembayaran 7 hari setelah invoice', 1, '2025-12-11 15:10:07', '2025-12-11 15:10:07'),
(2, 'NET14', 'Net 14', 14, 'Pembayaran 14 hari setelah invoice', 1, '2025-12-11 15:10:07', '2025-12-11 15:10:07'),
(3, 'NET30', 'Net 30', 30, 'Pembayaran 30 hari setelah invoice', 1, '2025-12-11 15:10:07', '2025-12-11 15:10:07'),
(4, 'NET45', 'Net 45', 45, 'Pembayaran 45 hari setelah invoice', 1, '2025-12-11 15:10:07', '2025-12-11 15:10:07'),
(5, 'NET60', 'Net 60', 60, 'Pembayaran 60 hari setelah invoice', 1, '2025-12-11 15:10:07', '2025-12-11 15:10:07'),
(6, 'COD', 'Cash On Delivery', 0, 'Pembayaran saat barang diterima', 1, '2025-12-11 15:10:07', '2025-12-11 15:10:07'),
(7, 'CBD', 'Cash Before Delivery', 0, 'Pembayaran sebelum pengiriman', 1, '2025-12-11 15:10:07', '2025-12-11 15:10:07'),
(8, 'CIA', 'Cash In Advance', 0, 'Pembayaran penuh di awal', 1, '2025-12-11 15:10:07', '2025-12-11 15:10:07');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `unit_of_measure` varchar(20) DEFAULT 'pcs',
  `status_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `code`, `name`, `description`, `category_id`, `unit_of_measure`, `status_code`, `created_at`, `updated_at`) VALUES
(1, '', 'Laptop Dell XPS 13', 'Laptop premium dengan prosesor Intel i7, RAM 16GB, SSD 512GB, layar 13.4 inch', 1, 'Unit', 'PRODUCT_ACTIVE', '2025-12-12 06:36:03', '2026-01-16 03:59:44'),
(2, '', 'Printer HP LaserJet Pro M404dn', 'Printer laser hitam putih, cetak 40 halaman/menit, duplex printing', 1, 'Unit', 'PRODUCT_ACTIVE', '2025-12-12 06:36:03', '2026-01-16 03:59:44'),
(3, '', 'Meja Kantor Minimalis 120x60', 'Meja kerja dengan bahan particle board tebal, finishing laminate', 3, 'Pcs', 'PRODUCT_ACTIVE', '2025-12-12 06:36:03', '2026-01-16 03:59:44'),
(4, '', 'Kertas A4 70gr PaperOne', 'Kertas fotokopi kualitas premium 70 gram, 1 rim (500 lembar)', 2, 'Rim', 'PRODUCT_ACTIVE', '2025-12-12 06:36:03', '2026-01-16 03:59:44'),
(5, '', 'Laptop Dell XPS 13', 'Laptop premium dengan prosesor Intel i7, RAM 16GB, SSD 512GB, layar 13.4 inch', 1, 'Unit', 'PRODUCT_ACTIVE', '2025-12-12 06:36:13', '2026-01-16 03:59:44'),
(6, '', 'Printer HP LaserJet Pro M404dn', 'Printer laser hitam putih, cetak 40 halaman/menit, duplex printing', 1, 'Unit', 'PRODUCT_ACTIVE', '2025-12-12 06:36:13', '2026-01-16 03:59:44'),
(7, '', 'Meja Kantor Minimalis 120x60', 'Meja kerja dengan bahan particle board tebal, finishing laminate', 3, 'Pcs', 'PRODUCT_ACTIVE', '2025-12-12 06:36:13', '2026-01-16 03:59:44'),
(8, '', 'Kertas A4 70gr PaperOne', 'Kertas fotokopi kualitas premium 70 gram, 1 rim (500 lembar)', 2, 'Rim', 'PRODUCT_ACTIVE', '2025-12-12 06:36:13', '2026-01-16 03:59:44'),
(9, '', 'Jasa Konsultan Sistem ERP', 'Implementasi sistem ERP untuk perusahaan kecil-menengah, termasuk training', 4, 'Pcs', 'PRODUCT_ACTIVE', '2025-12-12 06:36:13', '2026-01-16 03:59:44'),
(13, '', 'DELL Latitude 5240', 'DELL Latitude 5240 Unit', 1, 'Unit', 'PRODUCT_ACTIVE', '2025-12-12 17:31:42', '2026-01-16 03:59:44'),
(14, '', 'ASUS ROG UPDATE', 'ASUS ROG', 1, 'Unit', 'PRODUCT_ACTIVE', '2025-12-12 17:38:15', '2026-01-16 03:59:44'),
(15, '', 'SEPATU', 'test', 1, 'Pcs', 'PRODUCT_ACTIVE', '2026-01-14 12:15:29', '2026-01-16 03:59:44');

-- --------------------------------------------------------

--
-- Table structure for table `product_vendor_prices`
--

CREATE TABLE `product_vendor_prices` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `unit_price` decimal(15,2) DEFAULT NULL,
  `valid_from` date DEFAULT NULL,
  `valid_to` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_vendor_prices`
--

INSERT INTO `product_vendor_prices` (`id`, `product_id`, `vendor_id`, `unit_price`, `valid_from`, `valid_to`, `is_active`) VALUES
(1, 1, 1, '18500000.00', '2024-01-01', '2024-12-31', 1),
(2, 1, 2, '18250000.00', '2024-01-01', '2024-12-31', 1),
(3, 2, 1, '3850000.00', '2024-01-01', '2024-12-31', 1),
(4, 3, 3, '1250000.00', '2024-01-01', '2024-06-30', 1),
(5, 3, 1, '1300000.00', '2024-01-01', '2024-12-31', 1),
(6, 4, 4, '45000.00', '2024-01-01', '2024-12-31', 1),
(7, 5, 5, '25000000.00', '2024-01-01', '2024-12-31', 1),
(11, 13, 4, '10000.00', '2025-12-31', '2029-11-14', 1),
(12, 14, 1, '12098872.00', '2025-12-01', '2025-12-15', 1),
(13, 15, 6, '1000000.00', '2026-01-24', '2026-04-23', 1);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL,
  `po_number` varchar(30) NOT NULL COMMENT 'PO-YYYY-NNNNN',
  `purchase_request_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `po_date` date DEFAULT NULL,
  `payment_terms_id` int(11) DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'PO_DRAFT',
  `notes` text DEFAULT NULL,
  `received_by` int(11) DEFAULT NULL,
  `current_approval_level` int(11) DEFAULT NULL,
  `max_approval_level` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `po_number`, `purchase_request_id`, `vendor_id`, `po_date`, `payment_terms_id`, `total_amount`, `status_code`, `notes`, `received_by`, `current_approval_level`, `max_approval_level`, `created_at`, `updated_at`) VALUES
(1, 'PO-2026-00001', 1, 1, '2026-01-22', 2, '104050000.00', 'PO_COMPLETED', 'PO 1', 1, 3, 3, '2026-01-22 14:51:09', '2026-01-22 16:52:53'),
(2, 'PO-2026-00002', 1, 4, '2026-01-22', 1, '210000.00', 'PO_COMPLETED', 'PO 2', 1, 1, 1, '2026-01-22 14:51:09', '2026-01-22 16:56:31'),
(3, 'PO-2026-00003', 2, 1, '2026-01-24', 1, '68294360.00', 'PO_PARTIAL', 'BIMBINGAN KE 5', 1, 3, 3, '2026-01-24 08:00:55', '2026-01-24 08:04:18'),
(4, 'PO-2026-00004', 3, 1, '2026-01-27', 3, '73296616.00', 'PO_PARTIAL', 'OKWOKWOKOWKO', 1, 3, 3, '2026-01-26 16:56:19', '2026-01-26 17:04:14');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders_approvals`
--

CREATE TABLE `purchase_orders_approvals` (
  `id` int(11) NOT NULL,
  `purchase_order_id` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `approver_id` int(11) DEFAULT NULL,
  `status_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders_approvals`
--

INSERT INTO `purchase_orders_approvals` (`id`, `purchase_order_id`, `level`, `approver_id`, `status_code`, `approved_at`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 3, 'APR_APPROVED', '2026-01-22 22:16:18', 'OK approve', '2026-01-22 15:15:46', '2026-01-22 15:16:18'),
(2, 1, 1, 3, 'APR_APPROVED', '2026-01-22 22:16:31', 'oka pprove by manager', '2026-01-22 15:16:00', '2026-01-22 15:16:31'),
(3, 1, 2, 4, 'APR_APPROVED', '2026-01-22 22:17:02', 'head approve', '2026-01-22 15:16:00', '2026-01-22 15:17:02'),
(4, 1, 3, 5, 'APR_APPROVED', '2026-01-22 22:17:21', 'approve', '2026-01-22 15:16:00', '2026-01-22 15:17:21'),
(5, 3, 1, 3, 'APR_APPROVED', '2026-01-24 15:01:57', 'BIMBINGAN KE 5', '2026-01-24 08:01:33', '2026-01-24 08:01:57'),
(6, 3, 2, 4, 'APR_APPROVED', '2026-01-24 15:02:16', 'BIMBINGAN KE 5', '2026-01-24 08:01:33', '2026-01-24 08:02:16'),
(7, 3, 3, 5, 'APR_APPROVED', '2026-01-24 15:02:36', 'BIMBINGAN KE 5', '2026-01-24 08:01:33', '2026-01-24 08:02:36'),
(8, 4, 1, 3, 'APR_APPROVED', '2026-01-27 00:01:56', 'PO 1 APPROVE', '2026-01-26 17:01:29', '2026-01-26 17:01:56'),
(9, 4, 2, 4, 'APR_APPROVED', '2026-01-27 00:02:14', 'FDS', '2026-01-26 17:01:29', '2026-01-26 17:02:14'),
(10, 4, 3, 5, 'APR_APPROVED', '2026-01-27 00:02:48', 'OK', '2026-01-26 17:01:29', '2026-01-26 17:02:48');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_details`
--

CREATE TABLE `purchase_order_details` (
  `id` int(11) NOT NULL,
  `purchase_order_id` int(11) NOT NULL,
  `purchase_request_detail_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_order_details`
--

INSERT INTO `purchase_order_details` (`id`, `purchase_order_id`, `purchase_request_detail_id`, `product_id`, `quantity`, `unit`, `unit_price`, `subtotal`, `created_at`) VALUES
(1, 1, 1, 1, 5, 'Unit', '18500000.00', '92500000.00', '2026-01-22 14:51:09'),
(2, 1, 2, 2, 3, 'Unit', '3850000.00', '11550000.00', '2026-01-22 14:51:09'),
(4, 2, 4, 4, 10, 'Rim', '45000.00', '180000.00', '2026-01-22 14:51:09'),
(5, 2, 3, 13, 3, 'Unit', '10000.00', '30000.00', '2026-01-22 14:51:09'),
(7, 3, 5, 14, 5, 'Unit', '12098872.00', '60494360.00', '2026-01-24 08:00:55'),
(8, 3, 6, 3, 6, 'Pcs', '1300000.00', '7800000.00', '2026-01-24 08:00:55'),
(10, 4, 7, 1, 2, 'Unit', '18500000.00', '37000000.00', '2026-01-26 16:56:19'),
(11, 4, 8, 14, 3, 'Unit', '12098872.00', '36296616.00', '2026-01-26 16:56:19');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_requests`
--

CREATE TABLE `purchase_requests` (
  `id` int(11) NOT NULL,
  `pr_number` varchar(30) NOT NULL COMMENT 'PR-YYYY-NNN',
  `title` varchar(200) NOT NULL,
  `department` enum('IT & Infrastructure','Finance','HR') NOT NULL,
  `requested_by` int(11) NOT NULL COMMENT 'User ID requester',
  `request_date` date NOT NULL,
  `status_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_estimated` decimal(15,2) NOT NULL DEFAULT 0.00,
  `current_approval_level` int(11) DEFAULT 0,
  `max_approval_level` int(11) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_requests`
--

INSERT INTO `purchase_requests` (`id`, `pr_number`, `title`, `department`, `requested_by`, `request_date`, `status_code`, `total_estimated`, `current_approval_level`, `max_approval_level`, `notes`, `billing_address`, `shipping_address`, `created_at`, `updated_at`) VALUES
(1, 'PR-2026-00001', 'TESTING PE TO PAYMENT ', 'IT & Infrastructure', 7, '2026-02-05', 'PR_APPROVED', '104260000.00', 3, 3, 'TEST END TO END', 'BILLING ADDRESS', 'SHIPPING ADDRESS', '2026-01-22 14:49:48', '2026-01-22 14:51:08'),
(2, 'PR-2026-00002', 'BIMBINGAN KE 5', 'IT & Infrastructure', 7, '2026-01-24', 'PR_APPROVED', '68294360.00', 3, 3, 'BIMBINGAN KE 5', 'BIMBINGAN KE 5', 'BIMBINGAN KE 5', '2026-01-24 07:59:53', '2026-01-24 08:00:55'),
(3, 'PR-2026-00003', 'RFEDW', 'IT & Infrastructure', 7, '2026-01-26', 'PR_APPROVED', '73296616.00', 3, 3, 'RDS', 'GEDUNG A', 'JALANAN', '2026-01-26 16:53:14', '2026-01-26 16:56:19');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_request_approvals`
--

CREATE TABLE `purchase_request_approvals` (
  `id` int(11) NOT NULL,
  `purchase_request_id` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `approver_id` int(11) DEFAULT NULL,
  `status_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_request_approvals`
--

INSERT INTO `purchase_request_approvals` (`id`, `purchase_request_id`, `level`, `approver_id`, `status_code`, `approved_at`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, 'APR_APPROVED', '2026-01-22 21:50:14', 'approve by andi pratama', '2026-01-22 14:49:48', '2026-01-22 14:50:14'),
(2, 1, 2, 4, 'APR_APPROVED', '2026-01-22 21:50:43', 'approve by head', '2026-01-22 14:49:48', '2026-01-22 14:50:43'),
(3, 1, 3, 5, 'APR_APPROVED', '2026-01-22 21:51:08', 'approve by director', '2026-01-22 14:49:48', '2026-01-22 14:51:08'),
(4, 2, 1, 3, 'APR_APPROVED', '2026-01-24 15:00:22', 'BIMBINGAN KE 5', '2026-01-24 07:59:53', '2026-01-24 08:00:22'),
(5, 2, 2, 4, 'APR_APPROVED', '2026-01-24 15:00:38', 'BIMBINGAN KE 5', '2026-01-24 07:59:53', '2026-01-24 08:00:38'),
(6, 2, 3, 5, 'APR_APPROVED', '2026-01-24 15:00:55', 'BIMBINGAN KE 5', '2026-01-24 07:59:53', '2026-01-24 08:00:55'),
(7, 3, 1, 3, 'APR_APPROVED', '2026-01-26 23:54:25', 'ok bang approve', '2026-01-26 16:53:14', '2026-01-26 16:54:25'),
(8, 3, 2, 4, 'APR_APPROVED', '2026-01-26 23:55:53', 'OKOKOK HEAD APPROVE', '2026-01-26 16:53:14', '2026-01-26 16:55:53'),
(9, 3, 3, 5, 'APR_APPROVED', '2026-01-26 23:56:19', 'OK IKUT BANG', '2026-01-26 16:53:14', '2026-01-26 16:56:19');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_request_details`
--

CREATE TABLE `purchase_request_details` (
  `id` int(11) NOT NULL,
  `purchase_request_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `product_description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `estimated_price` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_request_details`
--

INSERT INTO `purchase_request_details` (`id`, `purchase_request_id`, `product_id`, `vendor_id`, `product_description`, `quantity`, `unit`, `estimated_price`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'DELL', 5, 'Unit', '18500000.00', '92500000.00', '2026-01-22 14:49:48', '2026-01-22 14:49:48'),
(2, 1, 2, 1, 'PRINTER', 3, 'Unit', '3850000.00', '11550000.00', '2026-01-22 14:49:48', '2026-01-22 14:49:48'),
(3, 1, 13, 4, '', 3, 'Unit', '10000.00', '30000.00', '2026-01-22 14:49:48', '2026-01-22 14:49:48'),
(4, 1, 4, 4, 'S', 10, 'Rim', '45000.00', '180000.00', '2026-01-22 14:49:48', '2026-01-22 15:18:56'),
(5, 2, 14, 1, 'BIMBINGAN KE 5', 5, 'Unit', '12098872.00', '60494360.00', '2026-01-24 07:59:53', '2026-01-24 07:59:53'),
(6, 2, 3, 1, 'BIMBINGAN KE 5', 6, 'Pcs', '1300000.00', '7800000.00', '2026-01-24 07:59:53', '2026-01-24 07:59:53'),
(7, 3, 1, 1, 'GFDSA', 2, 'Unit', '18500000.00', '37000000.00', '2026-01-26 16:53:14', '2026-01-26 16:53:14'),
(8, 3, 14, 1, 'OKO', 3, 'Unit', '12098872.00', '36296616.00', '2026-01-26 16:53:14', '2026-01-26 16:53:14');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_code` varchar(50) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_code`, `role_name`, `description`, `is_active`, `created_at`) VALUES
(1, 'admin', 'Administrator', 'Full system access', 1, '2025-12-11 12:53:13'),
(2, 'procurement', 'Procurement', 'Invoice Management Access', 1, '2025-12-11 12:53:13'),
(3, 'requestor', 'Requester', 'PR Only', 1, '2025-12-11 12:53:13'),
(4, 'manager', 'Manager', 'Manager Approval', 1, '2025-12-14 08:51:19'),
(5, 'finance', 'Finance', 'Verify Invoice & Payment', 1, '2025-12-14 08:51:19'),
(6, 'director', 'Director', 'Director Approval', 1, '2025-12-14 08:52:14'),
(7, 'head', 'Head', 'Head Of Users', 1, '2026-01-04 09:02:17');

-- --------------------------------------------------------

--
-- Table structure for table `role_menus`
--

CREATE TABLE `role_menus` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `can_view` tinyint(1) DEFAULT 1,
  `can_create` tinyint(1) DEFAULT 0,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_menus`
--

INSERT INTO `role_menus` (`id`, `role_id`, `menu_id`, `can_view`, `can_create`, `can_edit`, `can_delete`, `created_at`) VALUES
(1, 1, 1, 1, 1, 1, 1, '2025-12-11 13:00:14'),
(2, 1, 2, 1, 1, 1, 1, '2025-12-11 13:00:14'),
(3, 1, 3, 1, 1, 1, 1, '2025-12-11 13:00:14'),
(4, 1, 4, 1, 1, 1, 1, '2025-12-11 13:00:14'),
(5, 1, 5, 1, 1, 1, 1, '2025-12-11 13:00:14'),
(6, 1, 6, 1, 1, 1, 1, '2025-12-11 13:00:14'),
(7, 1, 7, 1, 1, 1, 1, '2025-12-11 13:00:14'),
(8, 2, 1, 1, 1, 1, 0, '2025-12-11 13:00:25'),
(9, 5, 4, 1, 1, 1, 0, '2025-12-11 13:00:25'),
(10, 1, 8, 1, 1, 1, 1, '2025-12-17 15:32:51'),
(11, 1, 9, 1, 1, 1, 1, '2025-12-17 15:34:16'),
(12, 1, 10, 1, 1, 1, 1, '2025-12-18 13:54:07'),
(13, 4, 8, 1, 1, 1, 1, '2025-12-23 03:34:05'),
(14, 4, 10, 1, 1, 1, 1, '2025-12-23 03:34:05'),
(15, 7, 8, 1, 1, 1, 1, '2025-12-23 09:00:46'),
(16, 7, 10, 1, 1, 1, 1, '2025-12-23 09:00:46'),
(17, 6, 8, 1, 1, 1, 1, '2025-12-27 06:24:59'),
(18, 6, 10, 1, 1, 1, 1, '2025-12-27 06:24:59'),
(19, 1, 11, 1, 1, 1, 1, '2025-12-29 04:33:05'),
(20, 1, 12, 1, 1, 1, 1, '2025-12-30 08:50:40'),
(22, 1, 13, 1, 1, 1, 1, '2026-01-01 14:56:55'),
(23, 4, 13, 1, 1, 1, 1, '2026-01-01 15:20:53'),
(24, 7, 13, 1, 1, 1, 1, '2026-01-01 15:20:53'),
(25, 6, 13, 1, 1, 1, 1, '2026-01-02 14:07:28'),
(26, 2, 14, 1, 1, 1, 1, '2026-01-04 12:11:16'),
(27, 2, 15, 1, 1, 1, 1, '2026-01-04 12:11:16'),
(28, 5, 16, 1, 1, 1, 1, '2026-01-04 12:11:16'),
(29, 5, 14, 1, 1, 1, 1, '2026-01-14 04:39:04'),
(30, 5, 17, 1, 1, 1, 1, '2026-01-14 04:39:04'),
(31, 3, 3, 1, 1, 1, 1, '2026-01-17 15:02:01'),
(32, 3, 8, 1, 1, 1, 1, '2026-01-17 15:02:01'),
(33, 3, 9, 1, 1, 1, 1, '2026-01-17 15:02:01'),
(34, 1, 18, 1, 1, 1, 1, '2026-01-23 16:53:48'),
(35, 1, 19, 1, 1, 1, 1, '2026-01-24 06:25:38'),
(36, 1, 20, 1, 1, 1, 1, '2026-01-24 06:25:38'),
(37, 1, 21, 1, 1, 1, 1, '2026-01-24 06:25:38');

-- --------------------------------------------------------

--
-- Table structure for table `status_codes`
--

CREATE TABLE `status_codes` (
  `id` int(11) NOT NULL,
  `module_code` varchar(20) NOT NULL COMMENT 'Prefix module: VEND, PO, PR',
  `status_code` varchar(30) NOT NULL COMMENT 'Kode unik: VEND_ACTIVE, PO_PENDING',
  `status_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_default` tinyint(1) DEFAULT 0 COMMENT 'Status default untuk module',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `status_codes`
--

INSERT INTO `status_codes` (`id`, `module_code`, `status_code`, `status_name`, `description`, `is_active`, `is_default`, `created_at`) VALUES
(1, 'VEND', 'VEND_PENDING', 'Pending Review', 'Vendor baru, menunggu verifikasi dokumen', 1, 1, '2025-12-09 09:44:03'),
(2, 'VEND', 'VEND_ACTIVE', 'Active', 'Vendor aktif dan bisa bertransaksi', 1, 0, '2025-12-09 09:44:03'),
(3, 'VEND', 'VEND_INACTIVE', 'Inactive', 'Vendor tidak aktif sementara', 1, 0, '2025-12-09 09:44:03'),
(4, 'VEND', 'VEND_SUSPENDED', 'Suspended', 'Vendor ditangguhkan karena masalah', 1, 0, '2025-12-09 09:44:03'),
(5, 'VEND', 'VEND_BLACKLISTED', 'Blacklisted', 'Vendor diblacklist permanen', 1, 0, '2025-12-09 09:44:03'),
(6, 'PO', 'PO_DRAFT', 'Draft', 'PO dalam proses draft', 1, 0, '2025-12-09 09:44:03'),
(7, 'PO', 'PO_SUBMITTED', 'Submitted', 'PO sudah disubmit dan menunggu proses approval', 1, 0, '2025-12-09 09:44:03'),
(8, 'PO', 'PO_APPROVED', 'Approved', 'PO sudah disetujui', 1, 0, '2025-12-09 09:44:03'),
(9, 'PO', 'PO_REJECTED', 'Rejected', 'PO ditolak oleh approver', 1, 0, '2025-12-09 09:44:03'),
(10, 'PO', 'PO_PROCESS', 'In Process', 'PO sedang dalam proses approval', 1, 0, '2025-12-09 09:44:03'),
(12, 'PO', 'PO_COMPLETED', 'Completed', 'Semua barang sudah diterima', 1, 0, '2025-12-09 09:44:03'),
(14, 'PR', 'PR_DRAFT', 'Draft', 'PR dalam proses draft', 1, 0, '2025-12-09 09:44:03'),
(15, 'PR', 'PR_PENDING', 'Pending', 'Menunggu review', 1, 0, '2025-12-09 09:44:03'),
(16, 'PR', 'PR_APPROVED', 'Approved', 'PR disetujui', 1, 0, '2025-12-09 09:44:03'),
(17, 'PR', 'PR_REJECTED', 'Rejected', 'PR ditolak', 1, 0, '2025-12-09 09:44:03'),
(18, 'PR', 'PR_CONVERTED', 'Converted to PO', 'Sudah dikonversi ke PO', 1, 0, '2025-12-09 09:44:03'),
(19, 'PR', 'PR_CANCELLED', 'Cancelled', 'PR dibatalkan', 1, 0, '2025-12-09 09:44:03'),
(22, 'PRODUCT', 'PRODUCT_ACTIVE', 'Active', 'Kondisi barang masih aktif', 1, 0, '2025-12-12 06:49:30'),
(23, 'PRODUCT', 'PRODUCT_INACTIVE', 'Inactive', 'Barang sudah Inactive', 1, 0, '2025-12-12 06:49:30'),
(24, 'PRODUCT', 'PRODUCT_EXP', 'Expired', 'Barang sudah Expired', 1, 0, '2025-12-12 18:59:25'),
(25, 'APR', 'APR_PENDING', 'Pending', 'Approval masih pending', 1, 0, '2025-12-21 04:06:37'),
(26, 'APR', 'APR_PROCESS', 'Process', 'Approval masih proses', 1, 0, '2025-12-21 04:06:37'),
(27, 'APR', 'APR_APPROVED', 'Approve', 'Approver sudah melakukan approve', 1, 0, '2025-12-21 04:07:30'),
(28, 'APR', 'APR_REJECTED', 'Reject', 'Approver melakukan Reject', 1, 0, '2025-12-21 04:07:30'),
(29, 'GR', 'GR_DRAFT', 'Draft', 'GR Draft', 1, 0, '2025-12-29 03:15:57'),
(30, 'GR', 'GR_PROCESS', 'Process', 'GR Process', 1, 0, '2025-12-29 03:15:57'),
(31, 'GR', 'GR_POSTED', 'Posted', 'GR Posted', 1, 0, '2025-12-29 03:31:04'),
(32, 'ASSET', 'ASSET_DRAFT', 'Draft', 'Asset Draft saat approval complete', 1, 1, '2025-12-29 03:32:56'),
(33, 'ASSET', 'ASSET_IN_STOCK', 'In Stock', 'Asset tersedia di gudang', 1, 1, '2025-12-29 03:32:56'),
(34, 'ASSET', 'ASSET_ASSIGNED', 'Assigned', 'Asset sudah didistribusikan ke user', 1, 0, '2025-12-29 03:32:56'),
(35, 'ASSET', 'ASSET_RETURNED', 'Returned', 'Asset dikembalikan ke gudang', 1, 0, '2025-12-29 03:32:56'),
(36, 'ASSET', 'ASSET_DAMAGED', 'Damaged', 'Asset rusak dan tidak dapat digunakan', 1, 0, '2025-12-29 03:32:56'),
(37, 'ASSET', 'ASSET_LOST', 'Lost', 'Asset hilang', 1, 0, '2025-12-29 03:32:56'),
(38, 'ASSET', 'ASSET_DISPOSED', 'Disposed', 'Asset sudah dihapuskan', 1, 0, '2025-12-29 03:32:56'),
(39, 'PO', 'PO_PARTIAL', 'Partial', 'PO Partial', 1, 0, '2026-01-03 11:20:11'),
(40, 'INV', 'INV_DRAFT', 'Draft', 'Invoice Draft', 1, 0, '2026-01-04 08:18:33'),
(41, 'INV', 'INV_VERIFIED', 'Verified', 'Invoice Verify', 1, 0, '2026-01-04 08:18:33'),
(42, 'INV', 'INV_PAID', 'Paid', 'Invoice Paid', 1, 0, '2026-01-04 08:18:33'),
(43, 'INV', 'INV_REJECTED', 'Rejected', 'Invoice Rejected', 1, 0, '2026-01-21 09:51:55'),
(44, 'EMPLOYEE', 'EMP_ACTIVE', 'Active', NULL, 1, 0, '2026-01-23 14:49:31'),
(45, 'EMPLOYEE', 'EMP_RESIGNED', 'Resigned', NULL, 1, 0, '2026-01-23 14:49:31'),
(46, 'EMPLOYEE', 'EMP_TERMINATED', 'Terminated', NULL, 1, 0, '2026-01-23 14:49:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `last_login_attempt` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role_id`, `login_attempts`, `last_login_attempt`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$hGL4XFXjy6r30NZ1r8kkROv5gSGIgQG76T0/FQ3z3RhrYiLp8UT1C', 'Administrator', 'admin@eprocurement.com', 1, 0, NULL, 1, '2025-12-07 17:00:00', '2026-05-25 08:29:36'),
(2, 'procurement', '$2y$10$hGL4XFXjy6r30NZ1r8kkROv5gSGIgQG76T0/FQ3z3RhrYiLp8UT1C', 'Procurement Staff', 'proc@eprocurement.com', 2, 0, NULL, 1, '2025-12-08 14:17:56', '2026-01-26 17:04:31'),
(3, 'manager01', '$2y$10$hGL4XFXjy6r30NZ1r8kkROv5gSGIgQG76T0/FQ3z3RhrYiLp8UT1C', 'Andi Pratama', 'bngkt378@gmail.com', 4, 0, NULL, 1, '2025-12-14 09:34:12', '2026-01-26 17:01:40'),
(4, 'head01', '$2y$10$hGL4XFXjy6r30NZ1r8kkROv5gSGIgQG76T0/FQ3z3RhrYiLp8UT1C', 'Siti Aisyah', 'goo44186@gmail.com', 7, 0, NULL, 1, '2025-12-14 09:34:12', '2026-01-26 17:02:04'),
(5, 'director01', '$2y$10$hGL4XFXjy6r30NZ1r8kkROv5gSGIgQG76T0/FQ3z3RhrYiLp8UT1C', 'Rudi Hartono', 'menggelo25@gmail.com', 6, 0, NULL, 1, '2025-12-14 09:34:12', '2026-01-26 17:02:21'),
(6, 'finance02', '$2y$10$hGL4XFXjy6r30NZ1r8kkROv5gSGIgQG76T0/FQ3z3RhrYiLp8UT1C', 'Siti Romlah', 'romlah@gmail.com', 5, 0, NULL, 1, '2026-01-04 08:57:17', '2026-02-02 07:27:37'),
(7, 'requestor', '$2y$10$hGL4XFXjy6r30NZ1r8kkROv5gSGIgQG76T0/FQ3z3RhrYiLp8UT1C', 'Rudi Haryanto', 'lsqbangkit@gmail.com', 3, 0, NULL, 1, '2026-01-17 15:00:21', '2026-01-26 16:48:44');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `vendor_code` varchar(20) NOT NULL COMMENT 'Format: VEND-YYYY-NNN',
  `company_name` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `city_id` int(11) NOT NULL,
  `business_type_id` int(11) NOT NULL,
  `status_code` varchar(30) NOT NULL DEFAULT 'VEND_PENDING',
  `tax_number` varchar(25) DEFAULT NULL COMMENT 'NPWP format: 12.345.678.9-012.345',
  `payment_terms_id` int(11) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `vendor_code`, `company_name`, `email`, `phone`, `address`, `city_id`, `business_type_id`, `status_code`, `tax_number`, `payment_terms_id`, `website`, `created_at`, `updated_at`) VALUES
(1, 'VND-2025-001', 'PT Supplier Jaya Abadi', 'oraruhya@gmail.com', '021-1234567', 'Jl. Sudirman No. 123, Kav. 45, Lt. 8', 1, 1, 'VEND_ACTIVE', '01.234.567.8-912.000', 0, 'www.supplierjaya.com', '2025-12-09 09:44:03', '2026-01-23 12:46:11'),
(2, 'VND-2025-002', 'CV Mandiri Sejahtera', 'kitharibang@gmail.com', '022-7654321', 'Jl. Dago No. 456, Bandung', 6, 2, 'VEND_ACTIVE', '02.345.678.9-123.000', 0, 'www.mandirisejahtera.co.id', '2025-12-09 09:44:03', '2026-01-23 12:46:30'),
(3, 'VND-2025-003', 'UD Sumber Makmur', 'ud.sumbermakmur@gmail.com', '031-9876543', 'Jl. Raya Darmo No. 789, Surabaya', 7, 1, 'VEND_PENDING', '03.456.789.0-234.000', 0, NULL, '2025-12-09 09:44:03', '2025-12-11 08:03:42'),
(4, 'VD-2025-004', 'PT Global Teknik Indonesia update', 'tannatos222@gmail.com', '021-5551234', 'Jl. Gatot Subroto No. 321, Jakarta Selatan, Jakarta Selatan, DKI Jakarta', 1, 6, 'VEND_ACTIVE', '04.567.890.1-345.000', 8, 'www.globalteknik.co.id', '2025-12-09 09:44:03', '2026-01-23 12:47:17'),
(5, 'VND-2025-005', 'CV Logistik Cepat', 'cs@logistikcepat.com', '024-1237890', 'Jl. Pemuda No. 56, Semarang', 9, 4, 'VEND_ACTIVE', '05.678.901.2-456.000', 0, 'www.logistikcepat.com', '2025-12-09 09:44:03', '2025-12-11 08:03:42'),
(6, 'VND-2025-006', 'Bangkit Hari', 'lsqbangkit@gmail.com', '6543234567', '', 2, 2, 'VEND_ACTIVE', '65432345', 0, 'http://localhost/e-procurement/vendor/create', '2025-12-10 19:18:34', '2025-12-11 08:03:42'),
(7, 'VND-2025-007', 'PT Maju Jaya Abadi', 'majumju@maju.com', '9836746738', '', 6, 3, 'VEND_ACTIVE', '', 0, 'https://maumaju.com', '2025-12-11 04:58:36', '2025-12-11 08:03:42'),
(8, 'VND-2025-008', 'PT Berilan Solution', 'berilan@soution.com', '978397486743', '', 11, 6, 'VEND_ACTIVE', '0936837387783', 0, 'http://localhost/e-procurement/vendor/create', '2025-12-11 05:00:57', '2025-12-11 08:03:42'),
(9, 'VND-2025-009', 'PT Payment Terms', 'terms@mail.com', '9837683389', '', 2, 5, 'VEND_ACTIVE', '09978673', 1, 'http://localhost/e-procurement/vendor/create', '2025-12-11 08:20:59', '2025-12-11 08:32:03'),
(10, 'VND-2025-010', 'PT Payment Terms Vendor', 'term2s@mail.com', '98376833892', '', 2, 5, 'VEND_ACTIVE', '099786735432', 0, 'http://localhost/e-procurement/vendor/create', '2025-12-11 08:32:56', '2025-12-11 08:32:56'),
(11, 'VND-2025-011', 'PT Payment Terms Namam', 'termsMai@mail.com', '98376833893', '', 14, 3, 'VEND_ACTIVE', '09978673', 2, 'http://localhost/e-procurement/vendor/create', '2025-12-11 08:35:31', '2025-12-11 08:35:31');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_bank_accounts`
--

CREATE TABLE `vendor_bank_accounts` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `bank_code` varchar(10) DEFAULT NULL COMMENT 'Kode bank: BCA, BRI, etc',
  `account_number` varchar(50) NOT NULL,
  `account_name` varchar(100) NOT NULL,
  `account_type` enum('savings','current','others') DEFAULT 'current',
  `currency` varchar(10) DEFAULT 'IDR',
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_bank_accounts`
--

INSERT INTO `vendor_bank_accounts` (`id`, `vendor_id`, `bank_name`, `bank_code`, `account_number`, `account_name`, `account_type`, `currency`, `is_primary`, `created_at`) VALUES
(1, 1, 'Bank Central Asia', 'BCA', '1234567890', 'PT Supplier Jaya Abadi', 'current', 'IDR', 1, '2025-12-09 09:44:03'),
(2, 2, 'Bank BRI', 'BRI', '1122334455', 'CV Mandiri Sejahtera', 'current', 'IDR', 1, '2025-12-09 09:44:03'),
(3, 3, 'Bank BNI', 'BNI', '2233445566', 'UD Sumber Makmur', 'current', 'IDR', 1, '2025-12-09 09:44:03'),
(4, 4, 'Bank CIMB Niaga', 'CIMB', '3344556677', 'PT Global Teknik Indonesia', 'current', 'IDR', 1, '2025-12-09 09:44:03'),
(5, 5, 'Bank Permata', 'PERMATA', '4455667788', 'CV Logistik Cepat', 'current', 'IDR', 1, '2025-12-09 09:44:03');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_contacts`
--

CREATE TABLE `vendor_contacts` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `contact_name` varchar(100) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0 COMMENT '1 = Kontak utama',
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_contacts`
--

INSERT INTO `vendor_contacts` (`id`, `vendor_id`, `contact_name`, `position`, `department`, `contact_phone`, `contact_email`, `is_primary`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'Budi Santoso', 'Director', 'Management', '0812-3456-7890', 'budi@supplierjaya.com', 1, NULL, '2025-12-09 16:44:03', '2025-12-09 16:44:03'),
(2, 1, 'Siti Rahayu', 'Sales Manager', 'Sales & Marketing', '0813-4567-8901', 'siti@supplierjaya.com', 0, NULL, '2025-12-09 16:44:03', '2025-12-09 16:44:03'),
(3, 2, 'Ahmad Rizki', 'Owner', 'Management', '0821-2345-6789', 'ahmad@mandirisejahtera.co.id', 1, NULL, '2025-12-09 16:44:03', '2025-12-09 16:44:03'),
(4, 3, 'Dewi Anggraeni', 'Manager', 'Management', '0831-3456-7890', 'dewi@sumbermakmur.com', 1, NULL, '2025-12-09 16:44:03', '2025-12-09 16:44:03'),
(5, 4, 'Rudi Hartono', 'Sales Director', 'Sales', '0815-1234-5678', 'rudi@globalteknik.co.id', 1, NULL, '2025-12-09 16:44:03', '2025-12-09 16:44:03'),
(6, 5, 'Joko Prasetyo', 'Operations Manager', 'Operations', '0817-3456-7890', 'joko@logistikcepat.com', 1, NULL, '2025-12-09 16:44:03', '2025-12-09 16:44:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approval_levels`
--
ALTER TABLE `approval_levels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `approval_level_roles`
--
ALTER TABLE `approval_level_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_level_role_level` (`approval_level_id`);

--
-- Indexes for table `approval_rules`
--
ALTER TABLE `approval_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `asset_assignments`
--
ALTER TABLE `asset_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_asset_active_assignment` (`asset_unit_id`,`returned_at`),
  ADD KEY `fk_assignment_employee` (`employee_id`),
  ADD KEY `fk_assignment_user` (`assigned_by`);

--
-- Indexes for table `asset_units`
--
ALTER TABLE `asset_units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_asset_serial` (`serial_number`),
  ADD KEY `idx_asset_gr_detail` (`goods_receipt_detail_id`),
  ADD KEY `idx_asset_status` (`status_code`),
  ADD KEY `fk_asset_product` (`product_id`),
  ADD KEY `fk_asset_employee` (`employee_id`);

--
-- Indexes for table `business_types`
--
ALTER TABLE `business_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_code` (`type_code`),
  ADD KEY `idx_type_code` (`type_code`),
  ADD KEY `idx_type_name` (`type_name`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_city_province` (`city_name`,`province_name`),
  ADD KEY `idx_city_name` (`city_name`),
  ADD KEY `idx_province` (`province_name`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_code` (`employee_code`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_employees_status` (`employment_status_code`);

--
-- Indexes for table `goods_receipts`
--
ALTER TABLE `goods_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_gr_status` (`status_code`),
  ADD KEY `idx_gr_date` (`receipt_date`),
  ADD KEY `idx_gr_po` (`purchase_order_id`);

--
-- Indexes for table `goods_receipt_details`
--
ALTER TABLE `goods_receipt_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_grd_receipt` (`goods_receipt_id`),
  ADD KEY `idx_pod_id` (`purchase_order_detail_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_vendor_invoice` (`vendor_id`,`invoice_number`),
  ADD KEY `fk_inv_po` (`purchase_order_id`),
  ADD KEY `fk_inv_status` (`status_code`);

--
-- Indexes for table `invoice_details`
--
ALTER TABLE `invoice_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_inv_id` (`invoice_id`),
  ADD KEY `fk_inv_pod` (`purchase_order_detail_id`),
  ADD KEY `fk_inv_prod` (`product_id`);

--
-- Indexes for table `invoice_price_audits`
--
ALTER TABLE `invoice_price_audits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `invoice_detail_id` (`invoice_detail_id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_menus_parent_id` (`parent_id`),
  ADD KEY `idx_menus_menu_order` (`menu_order`),
  ADD KEY `idx_menus_is_active` (`is_active`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `payment_terms`
--
ALTER TABLE `payment_terms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_vendor_prices`
--
ALTER TABLE `product_vendor_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_po_number` (`po_number`),
  ADD KEY `fk_po_pr` (`purchase_request_id`),
  ADD KEY `fk_po_vendor` (`vendor_id`),
  ADD KEY `fk_po_payment` (`payment_terms_id`),
  ADD KEY `fk_po_status` (`status_code`),
  ADD KEY `fk_po_user` (`received_by`);

--
-- Indexes for table `purchase_orders_approvals`
--
ALTER TABLE `purchase_orders_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pr_approval_user` (`approver_id`);

--
-- Indexes for table `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pod_po` (`purchase_order_id`),
  ADD KEY `fk_pod_prd` (`purchase_request_detail_id`),
  ADD KEY `fk_pod_product` (`product_id`);

--
-- Indexes for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_pr_number` (`pr_number`),
  ADD KEY `fk_pr_requested_by` (`requested_by`);

--
-- Indexes for table `purchase_request_approvals`
--
ALTER TABLE `purchase_request_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pr_approval_user` (`approver_id`);

--
-- Indexes for table `purchase_request_details`
--
ALTER TABLE `purchase_request_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pr_detail_pr` (`purchase_request_id`),
  ADD KEY `fk_pr_detail_vendor` (`vendor_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_code` (`role_code`);

--
-- Indexes for table `role_menus`
--
ALTER TABLE `role_menus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_menu` (`role_id`,`menu_id`),
  ADD KEY `idx_role_menus_role_id` (`role_id`),
  ADD KEY `idx_role_menus_menu_id` (`menu_id`);

--
-- Indexes for table `status_codes`
--
ALTER TABLE `status_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `status_code` (`status_code`),
  ADD UNIQUE KEY `unique_module_status` (`module_code`,`status_name`),
  ADD KEY `idx_module_code` (`module_code`),
  ADD KEY `idx_status_code` (`status_code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendor_code` (`vendor_code`),
  ADD KEY `idx_vendor_code` (`vendor_code`),
  ADD KEY `idx_company_name` (`company_name`),
  ADD KEY `idx_status_code` (`status_code`),
  ADD KEY `idx_city_id` (`city_id`),
  ADD KEY `idx_business_type_id` (`business_type_id`);

--
-- Indexes for table `vendor_bank_accounts`
--
ALTER TABLE `vendor_bank_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_account` (`bank_name`,`account_number`),
  ADD KEY `idx_vendor_id` (`vendor_id`),
  ADD KEY `idx_is_primary` (`is_primary`);

--
-- Indexes for table `vendor_contacts`
--
ALTER TABLE `vendor_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_vendor_id` (`vendor_id`),
  ADD KEY `idx_contact_name` (`contact_name`),
  ADD KEY `idx_is_primary` (`is_primary`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approval_levels`
--
ALTER TABLE `approval_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `approval_level_roles`
--
ALTER TABLE `approval_level_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `approval_rules`
--
ALTER TABLE `approval_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `asset_assignments`
--
ALTER TABLE `asset_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `asset_units`
--
ALTER TABLE `asset_units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `business_types`
--
ALTER TABLE `business_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `goods_receipts`
--
ALTER TABLE `goods_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `goods_receipt_details`
--
ALTER TABLE `goods_receipt_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `invoice_details`
--
ALTER TABLE `invoice_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `invoice_price_audits`
--
ALTER TABLE `invoice_price_audits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payment_terms`
--
ALTER TABLE `payment_terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `product_vendor_prices`
--
ALTER TABLE `product_vendor_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `purchase_orders_approvals`
--
ALTER TABLE `purchase_orders_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `purchase_request_approvals`
--
ALTER TABLE `purchase_request_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `purchase_request_details`
--
ALTER TABLE `purchase_request_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `role_menus`
--
ALTER TABLE `role_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `status_codes`
--
ALTER TABLE `status_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `vendor_bank_accounts`
--
ALTER TABLE `vendor_bank_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vendor_contacts`
--
ALTER TABLE `vendor_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approval_level_roles`
--
ALTER TABLE `approval_level_roles`
  ADD CONSTRAINT `fk_level_role_level` FOREIGN KEY (`approval_level_id`) REFERENCES `approval_levels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `asset_assignments`
--
ALTER TABLE `asset_assignments`
  ADD CONSTRAINT `fk_assignment_asset` FOREIGN KEY (`asset_unit_id`) REFERENCES `asset_units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_assignment_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_assignment_user` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `asset_units`
--
ALTER TABLE `asset_units`
  ADD CONSTRAINT `fk_asset_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_employees_status` FOREIGN KEY (`employment_status_code`) REFERENCES `status_codes` (`status_code`) ON UPDATE CASCADE;

--
-- Constraints for table `goods_receipt_details`
--
ALTER TABLE `goods_receipt_details`
  ADD CONSTRAINT `fk_grd_receipt` FOREIGN KEY (`goods_receipt_id`) REFERENCES `goods_receipts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_inv_po` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`),
  ADD CONSTRAINT `fk_inv_status` FOREIGN KEY (`status_code`) REFERENCES `status_codes` (`status_code`);

--
-- Constraints for table `invoice_details`
--
ALTER TABLE `invoice_details`
  ADD CONSTRAINT `fk_inv_id` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
  ADD CONSTRAINT `fk_inv_pod` FOREIGN KEY (`purchase_order_detail_id`) REFERENCES `purchase_order_details` (`id`),
  ADD CONSTRAINT `fk_inv_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `menus` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_vendor_prices`
--
ALTER TABLE `product_vendor_prices`
  ADD CONSTRAINT `product_vendor_prices_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `product_vendor_prices_ibfk_2` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`);

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `fk_po_payment` FOREIGN KEY (`payment_terms_id`) REFERENCES `payment_terms` (`id`),
  ADD CONSTRAINT `fk_po_pr` FOREIGN KEY (`purchase_request_id`) REFERENCES `purchase_requests` (`id`),
  ADD CONSTRAINT `fk_po_status` FOREIGN KEY (`status_code`) REFERENCES `status_codes` (`status_code`),
  ADD CONSTRAINT `fk_po_user` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_po_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`);

--
-- Constraints for table `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  ADD CONSTRAINT `fk_pod_po` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pod_prd` FOREIGN KEY (`purchase_request_detail_id`) REFERENCES `purchase_request_details` (`id`),
  ADD CONSTRAINT `fk_pod_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  ADD CONSTRAINT `fk_pr_requested_by` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `purchase_request_approvals`
--
ALTER TABLE `purchase_request_approvals`
  ADD CONSTRAINT `fk_pr_approval_user` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `purchase_request_details`
--
ALTER TABLE `purchase_request_details`
  ADD CONSTRAINT `fk_pr_detail_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_menus`
--
ALTER TABLE `role_menus`
  ADD CONSTRAINT `role_menus_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_menus_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendors`
--
ALTER TABLE `vendors`
  ADD CONSTRAINT `vendors_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vendors_ibfk_2` FOREIGN KEY (`business_type_id`) REFERENCES `business_types` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vendors_ibfk_3` FOREIGN KEY (`status_code`) REFERENCES `status_codes` (`status_code`) ON UPDATE CASCADE;

--
-- Constraints for table `vendor_bank_accounts`
--
ALTER TABLE `vendor_bank_accounts`
  ADD CONSTRAINT `vendor_bank_accounts_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vendor_contacts`
--
ALTER TABLE `vendor_contacts`
  ADD CONSTRAINT `vendor_contacts_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

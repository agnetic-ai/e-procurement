-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2025 at 04:40 AM
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
  `level` int(11) NOT NULL,
  `level_name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approval_levels`
--

INSERT INTO `approval_levels` (`id`, `level`, `level_name`, `created_at`) VALUES
(1, 1, 'Manager Approval', '2025-12-14 08:47:27'),
(2, 2, 'Finance Approval', '2025-12-14 08:47:27'),
(3, 3, 'Director Approval', '2025-12-14 08:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `approval_level_roles`
--

CREATE TABLE `approval_level_roles` (
  `id` int(11) NOT NULL,
  `approval_level_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approval_level_roles`
--

INSERT INTO `approval_level_roles` (`id`, `approval_level_id`, `role_id`, `created_at`) VALUES
(1, 1, 4, '2025-12-14 08:48:05'),
(2, 2, 5, '2025-12-14 08:48:05'),
(3, 3, 6, '2025-12-14 08:48:05');

-- --------------------------------------------------------

--
-- Table structure for table `approval_rules`
--

CREATE TABLE `approval_rules` (
  `id` int(11) NOT NULL,
  `min_amount` decimal(15,2) NOT NULL,
  `max_amount` decimal(15,2) DEFAULT NULL,
  `total_level` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approval_rules`
--

INSERT INTO `approval_rules` (`id`, `min_amount`, `max_amount`, `total_level`, `is_active`, `created_at`) VALUES
(1, '0.00', '10000000.00', 1, 1, '2025-12-14 08:46:57'),
(2, '10000001.00', '50000000.00', 2, 1, '2025-12-14 08:46:57'),
(3, '50000001.00', NULL, 3, 1, '2025-12-14 08:46:57');

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
  `assigned_to` int(11) DEFAULT NULL COMMENT 'User ID jika sudah didistribusikan',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `asset_units`
--

INSERT INTO `asset_units` (`id`, `goods_receipt_detail_id`, `product_id`, `serial_number`, `status_code`, `assigned_to`, `created_at`, `updated_at`) VALUES
(1, 1, 14, NULL, 'ASSET_DRAFT', NULL, '2025-12-29 03:34:20', '2025-12-29 03:39:48'),
(2, 1, 14, NULL, 'ASSET_DRAFT', NULL, '2025-12-29 03:34:20', '2025-12-29 03:39:52');

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
-- Table structure for table `goods_receipts`
--

CREATE TABLE `goods_receipts` (
  `id` int(11) NOT NULL,
  `purchase_request_id` int(11) NOT NULL,
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

INSERT INTO `goods_receipts` (`id`, `purchase_request_id`, `receipt_date`, `received_by`, `status_code`, `notes`, `created_at`, `updated_at`) VALUES
(8, 6, NULL, NULL, 'GR_DRAFT', NULL, '2025-12-29 03:34:20', '2025-12-29 03:34:20');

-- --------------------------------------------------------

--
-- Table structure for table `goods_receipt_details`
--

CREATE TABLE `goods_receipt_details` (
  `id` int(11) NOT NULL,
  `goods_receipt_id` int(11) NOT NULL,
  `purchase_request_detail_id` int(11) NOT NULL,
  `qty_received` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `goods_receipt_details`
--

INSERT INTO `goods_receipt_details` (`id`, `goods_receipt_id`, `purchase_request_detail_id`, `qty_received`, `created_at`) VALUES
(1, 8, 5, 0, '2025-12-29 03:34:20'),
(2, 8, 6, 0, '2025-12-29 03:34:20');

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
(4, 'Vendor Management', 'users', 'vendor', 2, 1, 1, '2025-12-11 12:59:30', '2025-12-11 12:59:30'),
(5, 'User Management', 'user-circle', 'users', 2, 2, 0, '2025-12-11 12:59:30', '2025-12-11 14:49:27'),
(6, 'Product Management', 'package', 'product', 2, 3, 1, '2025-12-11 12:59:30', '2025-12-11 13:49:45'),
(7, 'Category Management', 'grid', 'categories', 2, 4, 0, '2025-12-11 12:59:30', '2025-12-11 14:49:36'),
(8, 'Order Management', 'clipboard', '#', NULL, 5, 1, '2025-12-17 09:07:00', '2025-12-17 15:42:00'),
(9, 'Request Order', 'user', 'ordersrequest', 8, 1, 1, '2025-12-17 09:08:58', '2025-12-18 13:47:41'),
(10, 'Approval Order', NULL, 'ordersapproval', 8, 2, 1, '2025-12-18 13:53:36', '2025-12-18 13:54:24');

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
(1, '', 'Laptop Dell XPS 13', 'Laptop premium dengan prosesor Intel i7, RAM 16GB, SSD 512GB, layar 13.4 inch', 1, 'unit', 'PRODUCT_ACTIVE', '2025-12-12 06:36:03', '2025-12-12 06:52:28'),
(2, '', 'Printer HP LaserJet Pro M404dn', 'Printer laser hitam putih, cetak 40 halaman/menit, duplex printing', 1, 'unit', 'PRODUCT_ACTIVE', '2025-12-12 06:36:03', '2025-12-12 06:52:28'),
(3, '', 'Meja Kantor Minimalis 120x60', 'Meja kerja dengan bahan particle board tebal, finishing laminate', 3, 'unit', 'PRODUCT_ACTIVE', '2025-12-12 06:36:03', '2025-12-12 06:52:28'),
(4, '', 'Kertas A4 70gr PaperOne', 'Kertas fotokopi kualitas premium 70 gram, 1 rim (500 lembar)', 2, 'rim', 'PRODUCT_ACTIVE', '2025-12-12 06:36:03', '2025-12-14 14:48:17'),
(5, '', 'Laptop Dell XPS 13', 'Laptop premium dengan prosesor Intel i7, RAM 16GB, SSD 512GB, layar 13.4 inch', 1, 'unit', 'PRODUCT_ACTIVE', '2025-12-12 06:36:13', '2025-12-12 06:52:28'),
(6, '', 'Printer HP LaserJet Pro M404dn', 'Printer laser hitam putih, cetak 40 halaman/menit, duplex printing', 1, 'unit', 'PRODUCT_ACTIVE', '2025-12-12 06:36:13', '2025-12-12 06:52:28'),
(7, '', 'Meja Kantor Minimalis 120x60', 'Meja kerja dengan bahan particle board tebal, finishing laminate', 3, 'unit', 'PRODUCT_ACTIVE', '2025-12-12 06:36:13', '2025-12-12 06:52:28'),
(8, '', 'Kertas A4 70gr PaperOne', 'Kertas fotokopi kualitas premium 70 gram, 1 rim (500 lembar)', 2, 'rim', 'PRODUCT_ACTIVE', '2025-12-12 06:36:13', '2025-12-12 06:52:28'),
(9, '', 'Jasa Konsultan Sistem ERP', 'Implementasi sistem ERP untuk perusahaan kecil-menengah, termasuk training', 4, 'paket', 'PRODUCT_ACTIVE', '2025-12-12 06:36:13', '2025-12-12 06:52:28'),
(13, '', 'DELL Latitude 5240', 'DELL Latitude 5240 Unit', 1, 'unit', 'PRODUCT_ACTIVE', '2025-12-12 17:31:42', '2025-12-14 14:48:17'),
(14, '', 'ASUS ROG UPDATE', 'ASUS ROG', 1, 'pkg', 'PRODUCT_ACTIVE', '2025-12-12 17:38:15', '2025-12-14 14:48:17');

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
(11, 13, 4, '15234098.00', '2025-12-31', '2029-11-14', 1),
(12, 14, 1, '12098872.00', '2025-12-01', '2025-12-15', 1);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_requests`
--

CREATE TABLE `purchase_requests` (
  `id` int(11) NOT NULL,
  `pr_number` varchar(30) NOT NULL COMMENT 'PR-YYYY-NNN',
  `title` varchar(200) NOT NULL,
  `department` enum('IT','Finance','HR') NOT NULL,
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
(1, 'PR-2025-00001', 'PEMBELIAN PERTAMA TAHUN 2026', 'IT', 1, '2025-12-27', 'PR_APPROVED', '82670490.00', 3, 3, 'PEMBALIAN PERTAMA PADA JANURI 2026 UNUTK KEBUTUHAN DEPARTMENT IT ', 'SHIPPING ADDRESS', 'BILLING ADDRESS', '2025-12-27 06:11:26', '2025-12-27 06:25:23'),
(2, 'PR-2025-00002', 'PEMBELIAN KE 2 PADA JANUARI 2026', 'HR', 1, '2025-12-27', 'PR_PENDING', '27000000.00', 1, 2, 'PEMBELIAN KE 2 PADA JANUARI 2026 UNUTK KEBUTUHAN DEPARTMENT HR', '', '', '2025-12-27 06:16:33', '2025-12-27 06:21:59'),
(3, 'PR-2025-00003', 'PEMBELIAN KE 3 2026', 'Finance', 1, '2026-01-10', 'PR_PENDING', '2500000.00', 1, 1, 'PEMBELIAN KE 3 2026 UNTUK DIVISI FINANCE', 'BILLING ADDRESS', 'SHIPPING ADDRESS', '2025-12-27 06:20:36', '2025-12-27 06:20:36'),
(4, 'PR-2025-00004', 'PEMBELIAN KE 4', 'HR', 1, '2026-01-18', 'PR_APPROVED', '18340000.00', 2, 2, 'PEMBELIAN KE 4 DIVISI HR', 'BILLING ADDRESS', 'SHIPPING ADDRESS', '2025-12-28 12:09:58', '2025-12-28 12:15:54'),
(5, 'PR-2025-00005', 'Pembelian ke 6', 'Finance', 1, '2026-01-04', 'PR_APPROVED', '30558196.00', 2, 2, 'notes', 'billing', 'shipping ', '2025-12-28 12:19:33', '2025-12-28 12:34:44'),
(6, 'PR-2025-00006', 'y', 'Finance', 1, '2026-01-04', 'PR_APPROVED', '24287744.00', 2, 2, 'NOT', 'fvdcxz', 'gfvcdx', '2025-12-28 13:23:52', '2025-12-29 03:34:20');

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
(1, 4, 1, 3, 'APR_APPROVED', '2025-12-28 19:10:27', 'ok approve', '2025-12-28 12:09:58', '2025-12-28 12:10:27'),
(2, 4, 2, 4, 'APR_APPROVED', '2025-12-28 19:15:54', 'OK sesuai', '2025-12-28 12:09:58', '2025-12-28 12:15:54'),
(3, 5, 1, 3, 'APR_APPROVED', '2025-12-28 19:19:55', 'ok', '2025-12-28 12:19:33', '2025-12-28 12:19:55'),
(4, 5, 2, 4, 'APR_APPROVED', '2025-12-28 19:34:44', 'Remarks', '2025-12-28 12:19:33', '2025-12-28 12:34:44'),
(5, 6, 1, 3, 'APR_APPROVED', '2025-12-28 20:24:15', '', '2025-12-28 13:23:52', '2025-12-28 13:24:15'),
(6, 6, 2, 4, 'APR_APPROVED', '2025-12-29 10:34:20', 'OK', '2025-12-28 13:23:52', '2025-12-29 03:34:20');

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
(1, 4, 1, 1, 'DESC', 1, 'Unit', '18250000.00', '18250000.00', '2025-12-28 12:09:58', '2025-12-28 12:09:58'),
(2, 4, 4, 4, 'DESC 2', 2, 'Pcs', '45000.00', '90000.00', '2025-12-28 12:09:58', '2025-12-28 12:09:58'),
(3, 5, 13, 4, 'unit', 2, 'Unit', '15234098.00', '30468196.00', '2025-12-28 12:19:33', '2025-12-28 12:19:33'),
(4, 5, 4, 4, 'Product', 2, 'Pcs', '45000.00', '90000.00', '2025-12-28 12:19:33', '2025-12-28 12:19:33'),
(5, 6, 14, 1, 'ok', 2, 'Unit', '12098872.00', '24197744.00', '2025-12-28 13:23:52', '2025-12-28 13:23:52'),
(6, 6, 4, 4, 'fvdcx', 2, 'Pcs', '45000.00', '90000.00', '2025-12-28 13:23:52', '2025-12-28 13:23:52');

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
(2, 'procurement', 'Procurement', 'Vendor management access', 1, '2025-12-11 12:53:13'),
(3, 'viewer', 'Viewer', 'Read-only access', 1, '2025-12-11 12:53:13'),
(4, 'manager', 'Manager', 'Manager Approval', 1, '2025-12-14 08:51:19'),
(5, 'finance', 'Finance', 'Finance Approval', 1, '2025-12-14 08:51:19'),
(6, 'director', 'Director', 'Director Approval', 1, '2025-12-14 08:52:14');

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
(9, 2, 4, 1, 1, 1, 0, '2025-12-11 13:00:25'),
(10, 1, 8, 1, 1, 1, 1, '2025-12-17 15:32:51'),
(11, 1, 9, 1, 1, 1, 1, '2025-12-17 15:34:16'),
(12, 1, 10, 1, 1, 1, 1, '2025-12-18 13:54:07'),
(13, 4, 8, 1, 1, 1, 1, '2025-12-23 03:34:05'),
(14, 4, 10, 1, 1, 1, 1, '2025-12-23 03:34:05'),
(15, 5, 8, 1, 1, 1, 1, '2025-12-23 09:00:46'),
(16, 5, 10, 1, 1, 1, 1, '2025-12-23 09:00:46'),
(17, 6, 8, 1, 1, 1, 1, '2025-12-27 06:24:59'),
(18, 6, 10, 1, 1, 1, 1, '2025-12-27 06:24:59');

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
  `color` varchar(20) DEFAULT '#6c757d' COMMENT 'Warna untuk UI',
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `is_default` tinyint(1) DEFAULT 0 COMMENT 'Status default untuk module',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `status_codes`
--

INSERT INTO `status_codes` (`id`, `module_code`, `status_code`, `status_name`, `description`, `color`, `display_order`, `is_active`, `is_default`, `created_at`) VALUES
(1, 'VEND', 'VEND_PENDING', 'Pending Review', 'Vendor baru, menunggu verifikasi dokumen', '#ffc107', 1, 1, 1, '2025-12-09 09:44:03'),
(2, 'VEND', 'VEND_ACTIVE', 'Active', 'Vendor aktif dan bisa bertransaksi', '#28a745', 2, 1, 0, '2025-12-09 09:44:03'),
(3, 'VEND', 'VEND_INACTIVE', 'Inactive', 'Vendor tidak aktif sementara', '#6c757d', 3, 1, 0, '2025-12-09 09:44:03'),
(4, 'VEND', 'VEND_SUSPENDED', 'Suspended', 'Vendor ditangguhkan karena masalah', '#dc3545', 4, 1, 0, '2025-12-09 09:44:03'),
(5, 'VEND', 'VEND_BLACKLISTED', 'Blacklisted', 'Vendor diblacklist permanen', '#343a40', 5, 1, 0, '2025-12-09 09:44:03'),
(6, 'PO', 'PO_DRAFT', 'Draft', 'PO dalam proses draft', '#6c757d', 1, 1, 0, '2025-12-09 09:44:03'),
(7, 'PO', 'PO_PENDING', 'Pending Approval', 'Menunggu approval dari manager', '#ffc107', 2, 1, 0, '2025-12-09 09:44:03'),
(8, 'PO', 'PO_APPROVED', 'Approved', 'PO sudah disetujui', '#20c997', 3, 1, 0, '2025-12-09 09:44:03'),
(9, 'PO', 'PO_REJECTED', 'Rejected', 'PO ditolak oleh approver', '#dc3545', 4, 1, 0, '2025-12-09 09:44:03'),
(10, 'PO', 'PO_ORDERED', 'Ordered', 'PO sudah dikirim ke vendor', '#0dcaf0', 5, 1, 0, '2025-12-09 09:44:03'),
(11, 'PO', 'PO_PARTIAL', 'Partially Received', 'Barang sebagian sudah diterima', '#fd7e14', 6, 1, 0, '2025-12-09 09:44:03'),
(12, 'PO', 'PO_COMPLETED', 'Completed', 'Semua barang sudah diterima', '#28a745', 7, 1, 0, '2025-12-09 09:44:03'),
(13, 'PO', 'PO_CANCELLED', 'Cancelled', 'PO dibatalkan', '#343a40', 8, 1, 0, '2025-12-09 09:44:03'),
(14, 'PR', 'PR_DRAFT', 'Draft', 'PR dalam proses draft', '#6c757d', 1, 1, 0, '2025-12-09 09:44:03'),
(15, 'PR', 'PR_PENDING', 'Pending', 'Menunggu review', '#ffc107', 2, 1, 0, '2025-12-09 09:44:03'),
(16, 'PR', 'PR_APPROVED', 'Approved', 'PR disetujui', '#28a745', 3, 1, 0, '2025-12-09 09:44:03'),
(17, 'PR', 'PR_REJECTED', 'Rejected', 'PR ditolak', '#dc3545', 4, 1, 0, '2025-12-09 09:44:03'),
(18, 'PR', 'PR_CONVERTED', 'Converted to PO', 'Sudah dikonversi ke PO', '#0d6efd', 5, 1, 0, '2025-12-09 09:44:03'),
(19, 'PR', 'PR_CANCELLED', 'Cancelled', 'PR dibatalkan', '#343a40', 6, 1, 0, '2025-12-09 09:44:03'),
(22, 'PRODUCT', 'PRODUCT_ACTIVE', 'Active', 'Kondisi barang masih aktif', '#6c757d', 1, 1, 0, '2025-12-12 06:49:30'),
(23, 'PRODUCT', 'PRODUCT_INACTIVE', 'Inactive', 'Barang sudah Inactive', '#6c757d', 2, 1, 0, '2025-12-12 06:49:30'),
(24, 'PRODUCT', 'PRODUCT_EXP', 'Expired', 'Barang sudah Expired', '#6c757d', 3, 1, 0, '2025-12-12 18:59:25'),
(25, 'APR', 'APR_PENDING', 'Pending', 'Approval masih pending', '#6c757d', 1, 1, 0, '2025-12-21 04:06:37'),
(26, 'APR', 'APR_PROCESS', 'Process', 'Approval masih proses', '#6c757d', 2, 1, 0, '2025-12-21 04:06:37'),
(27, 'APR', 'APR_APPROVED', 'Approve', 'Approver sudah melakukan approve', '#6c757d', 3, 1, 0, '2025-12-21 04:07:30'),
(28, 'APR', 'APR_REJECTED', 'Reject', 'Approver melakukan Reject', '#6c757d', 4, 1, 0, '2025-12-21 04:07:30'),
(29, 'GR', 'GR_DRAFT', 'Draft', 'GR Draft', '#6c757d', 1, 1, 0, '2025-12-29 03:15:57'),
(30, 'GR', 'GR_PROCESS', 'Process', 'GR Process', '#6c757d', 2, 1, 0, '2025-12-29 03:15:57'),
(31, 'GR', 'GR_COMPLETE', 'Complete', 'GR Complete', '#6c757d', 3, 1, 0, '2025-12-29 03:31:04'),
(32, 'ASSET', 'ASSET_DRAFT', 'Draft', 'Asset Draft saat approval complete', '#0dcaf0', 1, 1, 1, '2025-12-29 03:32:56'),
(33, 'ASSET', 'ASSET_IN_STOCK', 'In Stock', 'Asset tersedia di gudang', '#0dcaf0', 2, 1, 1, '2025-12-29 03:32:56'),
(34, 'ASSET', 'ASSET_ASSIGNED', 'Assigned', 'Asset sudah didistribusikan ke user', '#20c997', 3, 1, 0, '2025-12-29 03:32:56'),
(35, 'ASSET', 'ASSET_RETURNED', 'Returned', 'Asset dikembalikan ke gudang', '#6c757d', 4, 1, 0, '2025-12-29 03:32:56'),
(36, 'ASSET', 'ASSET_DAMAGED', 'Damaged', 'Asset rusak dan tidak dapat digunakan', '#ffc107', 5, 1, 0, '2025-12-29 03:32:56'),
(37, 'ASSET', 'ASSET_LOST', 'Lost', 'Asset hilang', '#dc3545', 6, 1, 0, '2025-12-29 03:32:56'),
(38, 'ASSET', 'ASSET_DISPOSED', 'Disposed', 'Asset sudah dihapuskan', '#343a40', 7, 1, 0, '2025-12-29 03:32:56');

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
(1, 'admin', '$2y$10$hGL4XFXjy6r30NZ1r8kkROv5gSGIgQG76T0/FQ3z3RhrYiLp8UT1C', 'Administrator', 'admin@eprocurement.com', 1, 0, NULL, 1, '2025-12-07 17:00:00', '2025-12-28 13:23:14'),
(2, 'procurement', '$2y$10$hGL4XFXjy6r30NZ1r8kkROv5gSGIgQG76T0/FQ3z3RhrYiLp8UT1C', 'Procurement Staff', 'proc@eprocurement.com', 2, 0, NULL, 1, '2025-12-08 14:17:56', '2025-12-23 08:24:59'),
(3, 'manager01', '$2y$10$hGL4XFXjy6r30NZ1r8kkROv5gSGIgQG76T0/FQ3z3RhrYiLp8UT1C', 'Andi Pratama', 'andi.manager@company.com', 4, 0, NULL, 1, '2025-12-14 09:34:12', '2025-12-28 13:24:02'),
(4, 'finance01', '$2y$10$hGL4XFXjy6r30NZ1r8kkROv5gSGIgQG76T0/FQ3z3RhrYiLp8UT1C', 'Siti Aisyah', 'siti.finance@company.com', 5, 0, NULL, 1, '2025-12-14 09:34:12', '2025-12-29 03:10:11'),
(5, 'director01', '$2y$10$hGL4XFXjy6r30NZ1r8kkROv5gSGIgQG76T0/FQ3z3RhrYiLp8UT1C', 'Rudi Hartono', 'rudi.director@company.com', 6, 0, NULL, 1, '2025-12-14 09:34:12', '2025-12-27 06:24:22');

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
(1, 'VND-2025-001', 'PT Supplier Jaya Abadi', 'info@supplierjaya.com', '021-1234567', 'Jl. Sudirman No. 123, Kav. 45, Lt. 8', 1, 1, 'VEND_ACTIVE', '01.234.567.8-912.000', 0, 'www.supplierjaya.com', '2025-12-09 09:44:03', '2025-12-11 08:03:42'),
(2, 'VND-2025-002', 'CV Mandiri Sejahtera', 'contact@mandirisejahtera.co.id', '022-7654321', 'Jl. Dago No. 456, Bandung', 6, 2, 'VEND_ACTIVE', '02.345.678.9-123.000', 0, 'www.mandirisejahtera.co.id', '2025-12-09 09:44:03', '2025-12-11 08:03:42'),
(3, 'VND-2025-003', 'UD Sumber Makmur', 'ud.sumbermakmur@gmail.com', '031-9876543', 'Jl. Raya Darmo No. 789, Surabaya', 7, 1, 'VEND_PENDING', '03.456.789.0-234.000', 0, NULL, '2025-12-09 09:44:03', '2025-12-11 08:03:42'),
(4, 'VD-2025-004', 'PT Global Teknik Indonesia update', 'sales@globalteknik.co.id', '021-5551234', 'Jl. Gatot Subroto No. 321, Jakarta Selatan, Jakarta Selatan, DKI Jakarta', 1, 6, 'VEND_ACTIVE', '04.567.890.1-345.000', 8, 'www.globalteknik.co.id', '2025-12-09 09:44:03', '2025-12-11 09:33:42'),
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_contacts`
--

INSERT INTO `vendor_contacts` (`id`, `vendor_id`, `contact_name`, `position`, `department`, `contact_phone`, `contact_email`, `is_primary`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'Budi Santoso', 'Director', 'Management', '0812-3456-7890', 'budi@supplierjaya.com', 1, NULL, '2025-12-09 09:44:03', '2025-12-09 09:44:03'),
(2, 1, 'Siti Rahayu', 'Sales Manager', 'Sales & Marketing', '0813-4567-8901', 'siti@supplierjaya.com', 0, NULL, '2025-12-09 09:44:03', '2025-12-09 09:44:03'),
(3, 2, 'Ahmad Rizki', 'Owner', 'Management', '0821-2345-6789', 'ahmad@mandirisejahtera.co.id', 1, NULL, '2025-12-09 09:44:03', '2025-12-09 09:44:03'),
(4, 3, 'Dewi Anggraeni', 'Manager', 'Management', '0831-3456-7890', 'dewi@sumbermakmur.com', 1, NULL, '2025-12-09 09:44:03', '2025-12-09 09:44:03'),
(5, 4, 'Rudi Hartono', 'Sales Director', 'Sales', '0815-1234-5678', 'rudi@globalteknik.co.id', 1, NULL, '2025-12-09 09:44:03', '2025-12-09 09:44:03'),
(6, 5, 'Joko Prasetyo', 'Operations Manager', 'Operations', '0817-3456-7890', 'joko@logistikcepat.com', 1, NULL, '2025-12-09 09:44:03', '2025-12-09 09:44:03');

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
-- Indexes for table `asset_units`
--
ALTER TABLE `asset_units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_asset_serial` (`serial_number`),
  ADD KEY `idx_asset_gr_detail` (`goods_receipt_detail_id`),
  ADD KEY `idx_asset_status` (`status_code`),
  ADD KEY `fk_asset_product` (`product_id`);

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
-- Indexes for table `goods_receipts`
--
ALTER TABLE `goods_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_gr_pr` (`purchase_request_id`),
  ADD KEY `idx_gr_status` (`status_code`),
  ADD KEY `idx_gr_date` (`receipt_date`);

--
-- Indexes for table `goods_receipt_details`
--
ALTER TABLE `goods_receipt_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_grd_receipt` (`goods_receipt_id`),
  ADD KEY `idx_grd_pr_detail` (`purchase_request_detail_id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_menus_parent_id` (`parent_id`),
  ADD KEY `idx_menus_menu_order` (`menu_order`),
  ADD KEY `idx_menus_is_active` (`is_active`);

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
  ADD UNIQUE KEY `uk_pr_level` (`purchase_request_id`,`level`),
  ADD KEY `fk_pr_approval_user` (`approver_id`);

--
-- Indexes for table `purchase_request_details`
--
ALTER TABLE `purchase_request_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pr_detail_vendor` (`vendor_id`),
  ADD KEY `fk_pr_detail_pr` (`purchase_request_id`);

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
  ADD KEY `idx_status_code` (`status_code`),
  ADD KEY `idx_display_order` (`display_order`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `approval_level_roles`
--
ALTER TABLE `approval_level_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `approval_rules`
--
ALTER TABLE `approval_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `asset_units`
--
ALTER TABLE `asset_units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT for table `goods_receipts`
--
ALTER TABLE `goods_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `goods_receipt_details`
--
ALTER TABLE `goods_receipt_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payment_terms`
--
ALTER TABLE `payment_terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `product_vendor_prices`
--
ALTER TABLE `product_vendor_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `purchase_request_approvals`
--
ALTER TABLE `purchase_request_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `purchase_request_details`
--
ALTER TABLE `purchase_request_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `role_menus`
--
ALTER TABLE `role_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `status_codes`
--
ALTER TABLE `status_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- Constraints for table `asset_units`
--
ALTER TABLE `asset_units`
  ADD CONSTRAINT `fk_asset_gr_detail` FOREIGN KEY (`goods_receipt_detail_id`) REFERENCES `goods_receipt_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_asset_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_asset_status` FOREIGN KEY (`status_code`) REFERENCES `status_codes` (`status_code`) ON UPDATE CASCADE;

--
-- Constraints for table `goods_receipts`
--
ALTER TABLE `goods_receipts`
  ADD CONSTRAINT `fk_gr_purchase_request` FOREIGN KEY (`purchase_request_id`) REFERENCES `purchase_requests` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_gr_status` FOREIGN KEY (`status_code`) REFERENCES `status_codes` (`status_code`) ON UPDATE CASCADE;

--
-- Constraints for table `goods_receipt_details`
--
ALTER TABLE `goods_receipt_details`
  ADD CONSTRAINT `fk_grd_receipt` FOREIGN KEY (`goods_receipt_id`) REFERENCES `goods_receipts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `menus` (`id`) ON DELETE SET NULL;

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

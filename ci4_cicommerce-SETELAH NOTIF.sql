-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 11, 2026 at 07:18 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ci4_cicommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE `attributes` (
  `id` smallint UNSIGNED NOT NULL,
  `code` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `validation` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_unique` tinyint(1) NOT NULL DEFAULT '0',
  `is_filterable` tinyint(1) NOT NULL DEFAULT '0',
  `is_configurable` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`id`, `code`, `name`, `type`, `validation`, `is_required`, `is_unique`, `is_filterable`, `is_configurable`, `created_at`, `updated_at`) VALUES
(5, 'varian-dimsum', 'Varian Dimsum', 'select', '', 1, 0, 1, 1, '2026-05-05 02:51:59', '2026-05-05 02:51:59');

-- --------------------------------------------------------

--
-- Table structure for table `attribute_categories`
--

CREATE TABLE `attribute_categories` (
  `id` mediumint UNSIGNED NOT NULL,
  `attribute_id` smallint UNSIGNED NOT NULL,
  `category_id` mediumint UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attribute_options`
--

CREATE TABLE `attribute_options` (
  `id` smallint UNSIGNED NOT NULL,
  `attribute_id` smallint UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` mediumint UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `parent_id` mediumint UNSIGNED DEFAULT NULL,
  `prefix` char(3) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `parent_id`, `prefix`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Bahan Baku', 'bahan-baku', NULL, 'BBK', '2026-04-19 03:53:33', '2026-04-19 03:53:33', NULL),
(2, 'Produk Jadi', 'produk-jadi', NULL, 'PRD', '2026-04-19 04:07:02', '2026-04-19 04:07:02', NULL),
(3, 'Tepung', 'tepung', 1, 'TPG', '2026-04-19 04:08:06', '2026-04-19 04:08:06', NULL),
(4, 'Bahan Baku Segar', 'bahan-baku-segar', 1, 'BBS', '2026-04-19 09:56:24', '2026-06-28 09:18:37', NULL),
(5, 'Mochi', 'mochi', 2, 'MCH', '2026-04-19 18:48:37', '2026-04-19 18:48:37', NULL),
(6, 'Dimsum', 'dimsum', 2, 'DSM', '2026-04-26 03:29:38', '2026-04-26 03:29:38', NULL),
(7, 'Saus', 'saus', 1, 'SAU', '2026-04-27 18:57:07', '2026-04-27 18:57:07', NULL),
(8, 'kulit dimsum', 'kulit-dimsum', 1, 'KDM', '2026-05-10 15:39:05', '2026-05-10 15:39:05', NULL),
(9, 'Risoles', 'risoles', 2, 'RSL', '2026-06-21 18:08:54', '2026-06-23 13:07:19', NULL),
(12, 'telur', 'telur', 1, 'TLR', '2026-06-22 09:26:02', '2026-06-22 09:26:02', NULL),
(13, 'Bahan Tambahan Pangan', 'bahan-tambahan-pangan', 1, 'BTP', '2026-06-22 09:30:02', '2026-06-22 09:30:02', NULL),
(14, 'manisan', 'manisan', 1, 'MNS', '2026-06-22 09:43:26', '2026-06-22 09:50:54', NULL),
(15, 'minyak', 'minyak', 1, 'MYK', '2026-06-28 09:20:35', '2026-06-28 09:20:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Super Administrator (Full Access)'),
(2, 'pelanggan', 'User E-Business / Customer'),
(3, 'produksi', 'Manajemen Dapur & Pengolahan Bahan'),
(4, 'penjualan', 'Staf Kasir & Pelayanan Pelanggan'),
(5, 'gudang', 'Manajemen Stok & Inventaris'),
(6, 'pemilik', 'Owner / Monitoring Bisnis'),
(7, 'Kurir', 'Pengiriman Barang');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int UNSIGNED NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `login` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `time` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '20181211100537', 'App\\Database\\Migrations\\Migration_Install_ion_auth_custom', 'default', 'App', 1776563310, 1),
(2, '2026-03-26-000003', 'App\\Database\\Migrations\\TAbsensi', 'default', 'App', 1776563310, 1),
(3, '2026-03-26-010106', 'App\\Database\\Migrations\\CreateCategoriesTable', 'default', 'App', 1776563310, 1),
(4, '2026-03-26-010141', 'App\\Database\\Migrations\\CreateAttributesTable', 'default', 'App', 1776563310, 1),
(5, '2026-03-26-010213', 'App\\Database\\Migrations\\CreateAttrributeOptionsTable', 'default', 'App', 1776563310, 1),
(6, '2026-03-26-010304', 'App\\Database\\Migrations\\MBahanBaku', 'default', 'App', 1776563310, 1),
(7, '2026-03-26-010305', 'App\\Database\\Migrations\\CreateProductsTable', 'default', 'App', 1776563310, 1),
(8, '2026-03-26-010306', 'App\\Database\\Migrations\\MRecipes', 'default', 'App', 1776563310, 1),
(9, '2026-03-26-010308', 'App\\Database\\Migrations\\TProduksi', 'default', 'App', 1776563310, 1),
(10, '2026-03-26-010309', 'App\\Database\\Migrations\\TProduksiDetail', 'default', 'App', 1776563310, 1),
(11, '2026-03-26-010326', 'App\\Database\\Migrations\\CreateProductCategoriesTable', 'default', 'App', 1776563310, 1),
(12, '2026-03-26-010340', 'App\\Database\\Migrations\\CreateProductInventoriesTable', 'default', 'App', 1776563310, 1),
(13, '2026-03-26-010351', 'App\\Database\\Migrations\\CreateProductAttributeValuesTable', 'default', 'App', 1776563310, 1),
(14, '2026-03-26-010403', 'App\\Database\\Migrations\\CreateProductImagesTable', 'default', 'App', 1776563310, 1),
(15, '2026-04-03-043806', 'App\\Database\\Migrations\\TPenjualan', 'default', 'App', 1776563310, 1),
(16, '2026-04-03-043817', 'App\\Database\\Migrations\\TPenjualanDetail', 'default', 'App', 1776563310, 1),
(17, '2026-04-03-083330', 'App\\Database\\Migrations\\TStokMasuk', 'default', 'App', 1776563310, 1),
(18, '2026-05-05-034623', 'App\\Database\\Migrations\\CreateAttributeCategoriesTable', 'default', 'App', 1777963662, 2),
(19, '2026-05-12-122410', 'App\\Database\\Migrations\\AddEcommerceFieldsToTPenjualan', 'default', 'App', 1778602170, 3),
(20, '2026-05-12-123139', 'App\\Database\\Migrations\\AddCustomerProfileFieldsToUsers', 'default', 'App', 1778602170, 3),
(21, '2026-05-12-165544', 'App\\Database\\Migrations\\CreateProductReviews', 'default', 'App', 1778605086, 4),
(23, '2026-06-07-034024', 'App\\Database\\Migrations\\AddMidtransToTPenjualan', 'default', 'App', 1780810398, 5),
(24, '2026-06-08-071244', 'App\\Database\\Migrations\\CreateSettingsTable', 'default', 'App', 1780968151, 6),
(25, '2026-06-28-010511', 'App\\Database\\Migrations\\MShippingArea', 'default', 'App', 1782614066, 7),
(26, '2026-06-28-010643', 'App\\Database\\Migrations\\AlterTPenjualanDelivery', 'default', 'App', 1782614066, 7),
(27, '2026-07-11-172558', 'App\\Database\\Migrations\\CreateNotificationsTable', 'default', 'App', 1783790796, 8);

-- --------------------------------------------------------

--
-- Table structure for table `m_bahan_baku`
--

CREATE TABLE `m_bahan_baku` (
  `id` mediumint UNSIGNED NOT NULL,
  `kode_bahan` char(7) COLLATE utf8mb4_general_ci NOT NULL,
  `category_id` mediumint UNSIGNED DEFAULT NULL,
  `nama_bahan` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `satuan` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `harga_beli_satuan` mediumint NOT NULL DEFAULT '0',
  `stok_sekarang` decimal(8,2) NOT NULL DEFAULT '0.00',
  `stok_minimal` decimal(6,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `m_bahan_baku`
--

INSERT INTO `m_bahan_baku` (`id`, `kode_bahan`, `category_id`, `nama_bahan`, `satuan`, `harga_beli_satuan`, `stok_sekarang`, `stok_minimal`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'TPG-001', 3, 'Tepung Terigu Segitiga Biru', 'gr', 15, 1955.00, 1000.00, '2026-04-19 08:26:36', '2026-07-10 16:42:39', NULL),
(2, 'TPG-002', 3, 'Tepung Tapioka', 'gr', 10, 2320.00, 1000.00, '2026-04-19 09:50:54', '2026-07-10 16:42:39', NULL),
(3, 'AYM-001', 4, 'Ayam Filet', 'gr', 70, 300.00, 2000.00, '2026-04-19 09:59:52', '2026-07-10 16:42:39', NULL),
(4, 'KDM-001', 8, 'Kulit Dimsum', 'pcs', 200, 42.00, 30.00, '2026-05-10 15:42:41', '2026-07-11 23:06:40', NULL),
(5, 'TPG-003', 3, 'Tepung Panir', 'gr', 13, 40.00, 20.00, '2026-06-22 09:23:41', '2026-06-22 09:23:41', NULL),
(6, 'TLR-001', 12, 'telur ayam', 'pcs', 2500, 30.00, 10.00, '2026-06-22 09:36:34', '2026-06-22 09:36:34', NULL),
(7, 'MNS-001', 14, 'coklat batangan', 'gr', 50, 2000.00, 1000.00, '2026-06-22 09:54:18', '2026-06-22 09:54:18', NULL),
(8, 'MNS-002', 14, 'Keju batangan', 'gr', 156, 800.00, 480.00, '2026-06-27 06:40:54', '2026-06-27 06:45:23', NULL),
(9, 'MNS-003', 14, 'matcha batangan', 'gr', 150, 850.00, 340.00, '2026-06-27 06:49:38', '2026-06-27 06:49:38', NULL),
(10, 'BTP-001', 13, 'pewarna makanan', 'ml', 200, 75.00, 30.00, '2026-06-28 03:59:05', '2026-06-28 03:59:05', NULL),
(11, 'TPG-004', 3, 'tepung ketan', 'gr', 18, 2000.00, 1000.00, '2026-06-28 09:21:58', '2026-06-28 09:48:34', NULL),
(12, 'TPG-005', 3, 'tepung maizena', 'gr', 20, 2000.00, 1000.00, '2026-06-28 09:22:46', '2026-06-28 09:22:46', NULL),
(13, 'BTP-002', 13, 'gula pasir', 'gr', 15, 2000.00, 1000.00, '2026-06-28 09:23:33', '2026-06-28 09:48:15', NULL),
(14, 'BBS-002', 4, 'susu uht full cream', 'ml', 1, 6480.00, 2700.00, '2026-06-28 09:26:05', '2026-06-28 09:48:00', NULL),
(15, 'BBS-003', 4, 'buah stroberi', 'pcs', 2500, 30.00, 10.00, '2026-06-28 09:27:07', '2026-06-28 09:46:53', NULL),
(16, 'MNS-004', 14, 'whip cream bubuk', 'gr', 120, 1800.00, 750.00, '2026-06-28 09:29:51', '2026-06-28 09:45:53', NULL),
(17, 'BBS-004', 4, 'Daging Ayam', 'gr', 43, 2000.00, 500.00, '2026-07-05 16:50:56', '2026-07-05 16:50:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `m_recipes`
--

CREATE TABLE `m_recipes` (
  `id` mediumint UNSIGNED NOT NULL,
  `product_id` mediumint UNSIGNED NOT NULL,
  `bahan_baku_id` mediumint UNSIGNED NOT NULL,
  `jumlah_kebutuhan` decimal(6,3) NOT NULL,
  `keterangan` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `m_recipes`
--

INSERT INTO `m_recipes` (`id`, `product_id`, `bahan_baku_id`, `jumlah_kebutuhan`, `keterangan`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 9, 3, 180.000, 'daging di haluskan', '2026-05-10 15:51:19', '2026-05-10 15:57:13', NULL),
(2, 9, 4, 6.000, '', '2026-05-10 15:54:46', '2026-05-10 15:57:34', NULL),
(3, 9, 2, 12.000, '', '2026-05-10 15:59:32', '2026-05-10 15:59:32', NULL),
(4, 9, 1, 3.000, '', '2026-05-10 16:01:20', '2026-05-10 16:01:20', NULL),
(6, 53, 1, 12.500, '', '2026-06-27 06:37:27', '2026-06-27 06:37:27', NULL),
(7, 53, 6, 0.250, '', '2026-06-27 06:38:31', '2026-06-27 06:38:31', NULL),
(8, 53, 7, 15.000, '', '2026-06-27 06:38:53', '2026-06-27 06:38:53', NULL),
(10, 53, 8, 10.000, '', '2026-06-27 06:45:44', '2026-06-27 06:45:44', NULL),
(11, 53, 5, 12.000, '', '2026-06-27 06:52:18', '2026-06-27 06:52:18', NULL),
(12, 54, 1, 12.500, '', '2026-06-27 06:53:19', '2026-06-27 06:53:19', NULL),
(13, 54, 6, 0.250, '', '2026-06-27 06:53:36', '2026-06-27 06:53:36', NULL),
(14, 54, 9, 10.000, '', '2026-06-27 06:53:50', '2026-06-27 06:53:50', NULL),
(15, 54, 5, 12.000, '', '2026-06-27 06:54:12', '2026-06-27 06:54:12', NULL),
(16, 66, 16, 10.000, '', '2026-06-28 09:30:24', '2026-06-28 09:46:20', NULL),
(17, 66, 15, 1.000, '', '2026-06-28 09:48:51', '2026-06-28 09:49:19', NULL),
(18, 66, 14, 50.000, '', '2026-06-28 09:49:38', '2026-06-28 09:50:50', NULL),
(19, 66, 12, 10.000, '', '2026-06-28 09:51:22', '2026-06-28 09:51:22', NULL),
(20, 66, 13, 20.000, '', '2026-06-28 09:52:20', '2026-06-28 09:52:20', NULL),
(21, 66, 11, 50.000, '', '2026-06-28 09:52:36', '2026-06-28 09:52:36', NULL),
(22, 62, 1, 12.500, '', '2026-07-05 16:46:10', '2026-07-05 16:46:10', NULL),
(23, 62, 5, 12.000, '', '2026-07-05 16:46:34', '2026-07-05 16:46:34', NULL),
(24, 62, 6, 0.250, '', '2026-07-05 16:47:09', '2026-07-05 16:47:09', NULL),
(25, 62, 17, 40.000, '', '2026-07-05 16:55:22', '2026-07-05 16:55:22', NULL),
(26, 55, 11, 1.000, '', '2026-07-07 07:45:29', '2026-07-07 07:45:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `m_shipping_area`
--

CREATE TABLE `m_shipping_area` (
  `id` smallint UNSIGNED NOT NULL,
  `area_name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `ongkir` smallint UNSIGNED NOT NULL DEFAULT '0',
  `estimasi` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `m_shipping_area`
--

INSERT INTO `m_shipping_area` (`id`, `area_name`, `ongkir`, `estimasi`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Surjo', 5000, '10-15 menit', 1, '2026-06-28 06:39:22', '2026-06-28 06:39:22', NULL),
(2, 'Reban', 20000, '90-100 menit', 1, '2026-06-28 06:42:02', '2026-06-28 06:47:36', NULL),
(3, 'limpung', 15000, '17-20 menit', 1, '2026-06-28 06:43:06', '2026-06-28 06:47:23', NULL),
(4, 'Bawang', 1000, '5-10 menit', 1, '2026-06-29 00:28:51', '2026-06-29 00:28:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `reference_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `reference_id`, `title`, `message`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 2, 'new_order', '76', 'Pesanan Baru: AES-20260712-001', 'Pesanan baru dari Super senilai Rp 7.500', 0, '2026-07-12 01:44:04', '2026-07-12 01:44:04'),
(2, 2, 'new_order', '77', 'Pesanan Baru: AES-20260712-002', 'Pesanan baru dari Penjualan senilai Rp 25.000', 0, '2026-07-12 01:59:32', '2026-07-12 01:59:32'),
(3, 7, 'new_order', '77', 'Pesanan Baru: AES-20260712-002', 'Pesanan baru dari Penjualan senilai Rp 25.000', 1, '2026-07-12 01:59:32', '2026-07-12 02:05:33');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` mediumint UNSIGNED NOT NULL,
  `parent_id` mediumint UNSIGNED DEFAULT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `sku` char(8) COLLATE utf8mb4_general_ci NOT NULL,
  `type` enum('simple','configurable') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `price` mediumint UNSIGNED NOT NULL,
  `hpp_total` mediumint NOT NULL DEFAULT '0',
  `weight` decimal(5,2) DEFAULT NULL,
  `length` decimal(5,2) DEFAULT NULL,
  `width` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `short_description` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `parent_id`, `user_id`, `published_at`, `sku`, `type`, `name`, `slug`, `price`, `hpp_total`, `weight`, `length`, `width`, `height`, `short_description`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(9, NULL, 2, '2026-06-29 02:30:31', 'DSM-0001', 'simple', 'Dimsum Original', 'dimsum-original', 22000, 13965, 0.00, 0.00, 0.00, 0.00, 'bagai bintang di surga ', 'Nikmati kelezatan Dimsum Ayam Original kualitas resto yang dibuat dengan bahan-bahan berkualitas tinggi. Cocok untuk camilan keluarga maupun hidangan acara spesial.\r\n\r\nKenapa harus Dimsum Original kami?\r\n\r\n100% Daging Ayam Segar: Tanpa banyak campuran tepung.\r\n\r\nKulit Lembut: Tidak keras meski sudah dingin.\r\n\r\nTanpa Pengawet: Aman dikonsumsi siapa saja.\r\n\r\nSudah Termasuk Saus: Paket lengkap dengan saus chili oil/sambal spesial.\r\n\r\nSaran Penyajian:\r\nKukus selama 10-15 menit dalam keadaan air mendidih agar tekstur kembali kenyal dan nikmat.', '2026-04-26 19:29:21', '2026-06-29 02:30:31', NULL),
(52, NULL, 2, '2026-07-08 23:09:59', 'RSL-0006', 'configurable', 'Risoles', 'risoles', 0, 0, 0.00, 0.00, 0.00, 0.00, 'Risoles adalah camilan gurih yang terbuat dari kulit tipis seperti crepe yang diisi dengan berbagai ', 'Risoles Choco Cheese: Risoles dengan isian cokelat dan keju, memberikan perpaduan rasa manis dan gurih.\r\nRisoles Matcha Cheese: Risoles berisi krim matcha dan keju, cocok untuk pecinta rasa teh hijau.\r\nRisoles Cheese Crunchy: Risoles dengan isian keju dan tekstur renyah di luar.\r\nRisoles Tiramisu Oreo: Risoles manis berisi krim tiramisu dan remahan biskuit Oreo.\r\nRisoles Seblak: Risoles dengan isian seblak pedas khas Indonesia.\r\nRisoles Ayam Suwir: Risoles berisi ayam suwir berbumbu gurih.\r\nRisoles Bolognese: Risoles dengan isian saus bolognese daging cincang.\r\nRisoles Rogout: Risoles berisi ragout (campuran sayuran dan daging dengan saus kental).\r\nRisoles Mayo: Risoles dengan isian mayones gurih.\r\nRisoles Mentai: Risoles berisi saus mentai (mayones bercampur saus pedas khas Jepang).', '2026-06-23 13:06:40', '2026-07-08 23:09:59', NULL),
(53, 52, 2, '2026-07-08 23:09:59', 'RSL-0007', 'simple', 'Risoles Choco Cheese', 'risoles-choco-cheese', 4500, 3279, 0.00, 0.00, 0.00, 0.00, 'kkkkk', 'nkkknkk', '2026-06-23 13:13:01', '2026-07-08 23:09:59', NULL),
(54, 52, 2, '2026-07-08 23:09:59', 'RSL-0008', 'simple', 'Risoles Matcha Cheese', 'risoles-matcha-cheese', 4500, 2469, 0.00, 0.00, 0.00, 0.00, '', 'm', '2026-06-23 13:13:20', '2026-07-08 23:09:59', NULL),
(55, 52, 2, '2026-07-08 23:09:59', 'RSL-0009', 'simple', 'Risoles Cheese Crunchy', 'risoles-cheese-crunchy', 4500, 18, 0.00, 0.00, 0.00, 0.00, '', 'haaa?', '2026-06-23 13:30:51', '2026-07-08 23:09:59', NULL),
(56, 52, 2, '2026-07-08 23:09:59', 'RSL-0010', 'simple', 'Risoles Tiramisu Oreo', 'risoles-tiramisu-oreo', 4500, 0, 0.00, 0.00, 0.00, 0.00, 'p', '', '2026-06-23 13:31:22', '2026-07-08 23:09:59', NULL),
(57, 52, 2, '2026-07-08 23:09:59', 'RSL-0011', 'simple', 'Risoles Seblak', 'risoles-seblak', 4000, 0, 0.00, 0.00, 0.00, 0.00, 'lll', 'ok', '2026-06-23 13:31:58', '2026-07-08 23:09:59', NULL),
(58, 52, 2, '2026-07-08 23:09:59', 'RSL-0012', 'simple', 'Risoles Mayo', 'risoles-mayo', 4000, 0, 0.00, 0.00, 0.00, 0.00, '', 'l', '2026-06-23 13:32:46', '2026-07-08 23:09:59', NULL),
(59, 52, 2, '2026-07-08 23:09:59', 'RSL-0013', 'simple', 'Risoles Rogout', 'risoles-rogout', 4000, 0, 0.00, 0.00, 0.00, 0.00, '', 'm', '2026-06-23 13:33:01', '2026-07-08 23:09:59', NULL),
(60, 52, 2, '2026-07-08 23:09:59', 'RSL-0014', 'simple', 'Risoles Bolognese', 'risoles-bolognese', 4000, 0, 0.00, 0.00, 0.00, 0.00, '', 'm', '2026-06-23 13:33:27', '2026-07-08 23:09:59', NULL),
(61, 52, 2, '2026-07-08 23:09:59', 'RSL-0015', 'simple', 'Risoles Mentai', 'risoles-mentai', 4000, 0, 0.00, 0.00, 0.00, 0.00, '', 'o', '2026-06-23 13:34:00', '2026-07-08 23:09:59', NULL),
(62, 52, 2, '2026-07-08 23:09:59', 'RSL-0016', 'simple', 'Risoles Ayam Suwir', 'risoles-ayam-suwir', 4000, 2689, 0.00, 0.00, 0.00, 0.00, 'ok', 'll', '2026-06-23 13:43:24', '2026-07-08 23:09:59', NULL),
(65, NULL, 2, '2026-06-28 09:53:21', 'MCH-0017', 'configurable', 'Mochi', 'mochi', 0, 0, 0.00, 0.00, 0.00, 0.00, '', '', '2026-06-28 08:55:33', '2026-07-10 16:58:55', '2026-07-10 16:58:55'),
(66, 65, 2, '2026-06-28 09:53:21', 'MCH-0018', 'simple', 'mochi strawberry cream', 'mochi-strawberry-cream', 6000, 5150, 0.00, 0.00, 0.00, 0.00, '', '', '2026-06-28 08:56:10', '2026-07-10 16:58:55', '2026-07-10 16:58:55'),
(67, 65, 2, '2026-07-08 16:38:22', 'MCH-0019', 'simple', 'Mochi Anggur Cream', 'mochi-anggur-cream', 6000, 0, 0.00, 0.00, 0.00, 0.00, '', '', '2026-07-06 19:22:47', '2026-07-10 16:58:55', '2026-07-10 16:58:55');

-- --------------------------------------------------------

--
-- Table structure for table `product_attribute_values`
--

CREATE TABLE `product_attribute_values` (
  `id` int UNSIGNED NOT NULL,
  `parent_product_id` mediumint UNSIGNED DEFAULT NULL,
  `product_id` mediumint UNSIGNED DEFAULT NULL,
  `attribute_id` smallint UNSIGNED DEFAULT NULL,
  `attribute_option_id` smallint UNSIGNED DEFAULT NULL,
  `text_value` text COLLATE utf8mb4_general_ci,
  `boolean_value` tinyint(1) DEFAULT NULL,
  `integer_value` mediumint UNSIGNED DEFAULT NULL,
  `float_value` decimal(6,3) DEFAULT NULL,
  `datetime_value` datetime DEFAULT NULL,
  `date_value` date DEFAULT NULL,
  `json_value` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int UNSIGNED NOT NULL,
  `product_id` mediumint UNSIGNED NOT NULL,
  `category_id` mediumint UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `product_id`, `category_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(82, 52, 9, NULL, NULL, NULL),
(101, 55, 9, NULL, NULL, NULL),
(102, 56, 9, NULL, NULL, NULL),
(104, 58, 9, NULL, NULL, NULL),
(105, 59, 9, NULL, NULL, NULL),
(106, 60, 9, NULL, NULL, NULL),
(108, 57, 9, NULL, NULL, NULL),
(109, 62, 9, NULL, NULL, NULL),
(112, 54, 9, NULL, NULL, NULL),
(114, 53, 9, NULL, NULL, NULL),
(121, 61, 9, NULL, NULL, NULL),
(124, 65, 5, NULL, NULL, NULL),
(126, 66, 5, NULL, NULL, NULL),
(127, 9, 6, NULL, NULL, NULL),
(131, 67, 5, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int UNSIGNED NOT NULL,
  `product_id` mediumint UNSIGNED NOT NULL,
  `original` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `large` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `medium` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `small` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `original`, `large`, `medium`, `small`, `created_at`, `updated_at`) VALUES
(4, 9, 'uploads/products/1778379251_c471f7308d39b3c636a7.jpg', 'uploads/products/1778379251_c471f7308d39b3c636a7_large.jpg', 'uploads/products/1778379251_c471f7308d39b3c636a7_medium.jpg', 'uploads/products/1778379251_c471f7308d39b3c636a7_small.jpg', '2026-05-10 02:14:11', '2026-05-10 02:14:11'),
(9, 52, 'uploads/products/1782220324_810717174dd59086f913.png', 'uploads/products/1782220324_810717174dd59086f913_large.png', 'uploads/products/1782220324_810717174dd59086f913_medium.png', 'uploads/products/1782220324_810717174dd59086f913_small.png', '2026-06-23 13:12:06', '2026-06-23 13:12:06'),
(14, 65, 'uploads/products/1783503611_7130f1d2f8530e27556b.jpg', 'uploads/products/1783503611_7130f1d2f8530e27556b_large.jpg', 'uploads/products/1783503611_7130f1d2f8530e27556b_medium.jpg', 'uploads/products/1783503611_7130f1d2f8530e27556b_small.jpg', '2026-07-08 16:40:11', '2026-07-08 16:40:11');

-- --------------------------------------------------------

--
-- Table structure for table `product_inventories`
--

CREATE TABLE `product_inventories` (
  `id` mediumint UNSIGNED NOT NULL,
  `product_id` mediumint UNSIGNED NOT NULL,
  `qty` mediumint UNSIGNED NOT NULL,
  `low_stock_threshold` smallint UNSIGNED NOT NULL DEFAULT '10',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_inventories`
--

INSERT INTO `product_inventories` (`id`, `product_id`, `qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES
(103, 55, 4, 10, NULL, '2026-07-09 00:20:14'),
(104, 56, 4, 10, NULL, '2026-07-08 23:09:59'),
(106, 58, 0, 10, NULL, '2026-07-08 23:09:59'),
(107, 59, 5, 10, NULL, '2026-07-08 23:09:59'),
(108, 60, 4, 10, NULL, '2026-07-08 23:09:59'),
(110, 57, 5, 10, NULL, '2026-07-08 23:09:59'),
(111, 62, 5, 10, NULL, '2026-07-08 23:32:59'),
(114, 54, 5, 10, NULL, '2026-07-08 23:09:59'),
(116, 53, 5, 10, NULL, '2026-07-12 01:44:04'),
(123, 61, 4, 10, NULL, '2026-07-09 00:36:16'),
(127, 66, 7, 10, NULL, '2026-07-08 18:05:45'),
(128, 9, 9, 10, NULL, '2026-07-12 01:59:32'),
(132, 67, 3, 10, NULL, '2026-07-08 23:31:50');

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int UNSIGNED NOT NULL,
  `product_id` mediumint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL DEFAULT '5',
  `ulasan` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `product_id`, `user_id`, `rating`, `ulasan`, `created_at`, `updated_at`) VALUES
(1, 65, 2, 5, 'yummy', '2026-07-04 18:14:34', '2026-07-04 18:14:34');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` smallint UNSIGNED NOT NULL,
  `setting_key` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_general_ci,
  `setting_group` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'general',
  `label` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `label`, `created_at`, `updated_at`) VALUES
(1, 'payment_midtrans_enabled', '1', 'payment', 'Aktifkan Pembayaran Midtrans', NULL, '2026-07-07 08:19:01'),
(2, 'payment_transfer_enabled', '1', 'payment', 'Aktifkan Transfer Bank Manual', NULL, '2026-07-07 08:19:01'),
(3, 'payment_qris_enabled', '1', 'payment', 'Aktifkan QRIS Manual (Upload Bukti)', NULL, '2026-07-07 08:19:01'),
(4, 'manual_bank_name', 'BCA', 'payment', 'Nama Bank Transfer', NULL, NULL),
(5, 'manual_account_number', '1234567890', 'payment', 'Nomor Rekening', NULL, NULL),
(6, 'manual_account_name', 'Annyeong Foodie', 'payment', 'Nama Pemilik Rekening', NULL, NULL),
(7, 'payment_cash_enabled', '1', 'general', NULL, '2026-06-26 02:28:11', '2026-07-07 08:19:01'),
(8, 'handling_fee', '2000', 'general', NULL, '2026-06-28 10:52:16', '2026-07-06 20:27:13');

-- --------------------------------------------------------

--
-- Table structure for table `t_absensi`
--

CREATE TABLE `t_absensi` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `jam_masuk` datetime DEFAULT NULL,
  `status_masuk` enum('Tepat Waktu','Terlambat') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jam_keluar` datetime DEFAULT NULL,
  `status_keluar` enum('Normal','Pulang Cepat') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total_jam` decimal(4,2) DEFAULT NULL,
  `keterangan` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_absensi`
--

INSERT INTO `t_absensi` (`id`, `user_id`, `jam_masuk`, `status_masuk`, `jam_keluar`, `status_keluar`, `total_jam`, `keterangan`, `created_at`, `updated_at`) VALUES
(3, 2, '2026-07-05 09:22:19', 'Terlambat', '2026-07-05 09:22:34', 'Pulang Cepat', 0.00, 'Hadir', '2026-07-05 09:22:19', '2026-07-05 09:22:34'),
(4, 2, '2026-07-07 00:39:17', 'Tepat Waktu', '2026-07-07 07:41:52', 'Pulang Cepat', 7.04, 'Hadir', '2026-07-07 00:39:17', '2026-07-07 07:41:52');

-- --------------------------------------------------------

--
-- Table structure for table `t_penjualan`
--

CREATE TABLE `t_penjualan` (
  `id` int UNSIGNED NOT NULL,
  `invoice_no` char(16) COLLATE utf8mb4_general_ci NOT NULL,
  `midtrans_order_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subtotal_produk` int DEFAULT NULL,
  `diskon` mediumint NOT NULL DEFAULT '0',
  `grand_total` int DEFAULT NULL,
  `pembayaran` enum('Cash','QRIS','Transfer','Midtrans') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Cash',
  `uang_diterima` int UNSIGNED NOT NULL DEFAULT '0',
  `kasir_id` int UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `customer_id` int UNSIGNED DEFAULT NULL,
  `order_status` enum('pending','processing','shipped','completed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `payment_status` enum('unpaid','pending_verification','paid','rejected','expired','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'unpaid',
  `payment_proof` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `snap_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `midtrans_transaction_id` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `midtrans_payment_type` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `midtrans_status` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shipping_address` text COLLATE utf8mb4_general_ci,
  `delivery_type` enum('pickup','delivery') COLLATE utf8mb4_general_ci DEFAULT 'pickup',
  `shipping_area_id` smallint UNSIGNED DEFAULT NULL,
  `shipping_cost` mediumint UNSIGNED DEFAULT '0',
  `handling_fee` mediumint UNSIGNED DEFAULT '0',
  `delivery_courier_id` int UNSIGNED DEFAULT NULL,
  `catatan_customer` text COLLATE utf8mb4_general_ci,
  `verified_at` datetime DEFAULT NULL,
  `verified_by` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_penjualan`
--

INSERT INTO `t_penjualan` (`id`, `invoice_no`, `midtrans_order_id`, `subtotal_produk`, `diskon`, `grand_total`, `pembayaran`, `uang_diterima`, `kasir_id`, `created_at`, `updated_at`, `customer_id`, `order_status`, `payment_status`, `payment_proof`, `snap_token`, `midtrans_transaction_id`, `midtrans_payment_type`, `midtrans_status`, `shipping_address`, `delivery_type`, `shipping_area_id`, `shipping_cost`, `handling_fee`, `delivery_courier_id`, `catatan_customer`, `verified_at`, `verified_by`) VALUES
(14, 'AES-20260624-001', NULL, 4000, 0, 4000, 'Midtrans', 0, 2, '2026-06-24 04:49:20', '2026-06-27 08:01:02', 2, 'completed', 'unpaid', NULL, '29ccf11d-3df6-4884-9bc0-7d77ac20f5dd', NULL, NULL, NULL, 'coba config', 'pickup', NULL, 0, 0, NULL, '', NULL, NULL),
(15, 'AES-20260624-002', NULL, 8500, 0, 8500, 'Midtrans', 0, 2, '2026-06-24 04:53:24', '2026-06-27 08:00:20', 2, 'completed', 'unpaid', NULL, 'abbe38f6-49f7-4fd1-9316-6c3f940e2962', NULL, NULL, NULL, 'jalan jalan ', 'pickup', NULL, 0, 0, NULL, '', NULL, NULL),
(17, 'AES-20260624-004', NULL, 22000, 0, 22000, 'Midtrans', 0, 2, '2026-06-24 05:02:56', '2026-06-27 07:19:55', 2, 'cancelled', 'unpaid', NULL, '017798e9-5d52-423c-8e85-984faa0da60f', NULL, NULL, NULL, 'jalan', 'pickup', NULL, 0, 0, NULL, '', NULL, NULL),
(25, 'AES-20260624-005', NULL, 4000, 0, 4000, 'Midtrans', 0, 2, '2026-06-24 10:24:02', '2026-06-27 07:59:06', 2, 'completed', 'unpaid', NULL, 'eadbff00-4ca0-4bba-85b9-b802ac179618', NULL, NULL, NULL, 'coba stok', 'pickup', NULL, 0, 0, NULL, '', NULL, NULL),
(26, 'AES-20260624-006', NULL, 4000, 0, 4000, 'Midtrans', 0, 2, '2026-06-24 10:30:31', '2026-06-24 10:30:31', 2, 'pending', 'unpaid', NULL, '58645475-f871-4869-b419-3df2fe24720a', NULL, NULL, NULL, 'coba stok', 'pickup', NULL, 0, 0, NULL, '', NULL, NULL),
(28, 'AES-20260626-001', NULL, 22000, 0, 22000, 'Transfer', 0, 2, '2026-06-26 02:28:46', '2026-06-27 07:19:04', 2, 'completed', 'pending_verification', 'uploads/bukti_bayar/1782440943_21fb7a7057efd9dcb472.png', NULL, NULL, NULL, NULL, 'coba bukti pembayaran', 'pickup', NULL, 0, 0, NULL, '', NULL, NULL),
(36, 'INV-20260627-001', NULL, 22000, 0, 22000, 'Transfer', 25000, 2, '2026-06-27 05:29:30', '2026-06-27 05:29:30', NULL, 'completed', 'paid', NULL, NULL, NULL, NULL, NULL, NULL, 'pickup', NULL, 0, 0, NULL, NULL, NULL, NULL),
(37, 'INV-20260627-002', NULL, 22000, 0, 22000, 'Cash', 50000, 2, '2026-06-27 05:30:08', '2026-06-27 05:30:08', NULL, 'completed', 'paid', NULL, NULL, NULL, NULL, NULL, NULL, 'pickup', NULL, 0, 0, NULL, NULL, NULL, NULL),
(39, 'AES-20260628-001', NULL, 22000, 0, 34000, 'Transfer', 0, 2, '2026-06-28 20:03:53', '2026-06-29 07:42:00', 2, 'completed', 'paid', 'uploads/bukti_bayar/1782677105_db542bdd07f7627f45dd.jpeg', NULL, NULL, NULL, NULL, 'coba ongkir dummy', 'delivery', NULL, 10000, 2000, NULL, '', '2026-06-28 20:07:39', 2),
(42, 'AES-20260629-001', NULL, 6000, 0, 12000, 'Transfer', 0, 2, '2026-06-29 00:29:31', '2026-06-29 07:41:45', 2, 'completed', 'paid', 'uploads/bukti_bayar/1782695293_4b29695d40288b618cb1.jpeg', NULL, NULL, NULL, NULL, 'coba id area', 'delivery', 4, 1000, 5000, 4, '', '2026-06-29 01:08:34', 2),
(50, 'AES-20260629-002', 'AES-20260629-002-4F31D2', 22000, 0, 27000, 'Midtrans', 0, 2, '2026-06-29 03:49:28', '2026-06-29 03:49:29', 2, 'pending', 'unpaid', NULL, '0c181081-1a20-4348-a923-da604bf0c014', NULL, NULL, NULL, 'jl sunan ampel kecamatan bawang, Batang, Jawa Tengah', 'pickup', NULL, 0, 5000, NULL, '', NULL, NULL),
(51, 'AES-20260706-001', 'AES-20260706-001-FA372A', 4500, 0, 9500, 'QRIS', 0, 2, '2026-07-06 19:12:42', '2026-07-06 20:25:46', 2, 'pending', 'pending_verification', 'uploads/bukti_bayar/1783369546_ea9356f05396424e5929.png', NULL, NULL, NULL, NULL, 'jl sunan ampel kecamatan bawang, Batang, Jawa Tengah', 'pickup', NULL, 0, 5000, NULL, '', NULL, NULL),
(59, 'AES-20260707-001', 'AES-20260707-001-448BC3', 6000, 0, 9000, 'Midtrans', 0, 2, '2026-07-07 00:36:30', '2026-07-07 00:38:43', 2, 'shipped', 'paid', NULL, '61bc5ef9-009a-4377-8d34-e6fba9064012', NULL, NULL, NULL, 'jl sunan ampel kecamatan bawang', 'delivery', 4, 1000, 2000, NULL, '', '2026-07-07 00:38:20', 2),
(60, 'INV-20260707-001', NULL, 6000, 0, 6000, 'Cash', 10000, 2, '2026-07-07 07:44:20', '2026-07-07 07:44:20', NULL, 'completed', 'paid', NULL, NULL, NULL, NULL, NULL, NULL, 'pickup', NULL, 0, 0, NULL, NULL, NULL, NULL),
(61, 'AES-20260707-002', 'AES-20260707-002-759368', 8000, 0, 15000, 'Midtrans', 0, 7, '2026-07-07 08:23:26', '2026-07-07 08:23:27', 7, 'pending', 'unpaid', NULL, '573e26d1-67cc-4319-ac36-f923bb1159ee', NULL, NULL, NULL, 'jalan jalan dengan sepatu rodaku', 'delivery', 1, 5000, 2000, NULL, '', NULL, NULL),
(62, 'AES-20260708-001', 'AES-20260708-001-E4A7D9', 6000, 0, 9000, 'Midtrans', 0, 2, '2026-07-08 16:42:37', '2026-07-08 18:05:45', 2, 'cancelled', 'paid', NULL, 'b9402a96-0d8b-4356-bbec-2b282426277e', NULL, NULL, NULL, 'jl sunan ampel kecamatan bawang', 'delivery', 4, 1000, 2000, NULL, '', '2026-07-08 18:05:37', 2),
(63, 'AES-20260708-002', 'AES-20260708-002-765A54', 6000, 0, 9000, 'Midtrans', 0, 2, '2026-07-08 17:48:09', '2026-07-08 17:49:08', 2, 'processing', 'paid', NULL, '5d9d91a4-22d0-4d4e-bfa6-79a04d973ca6', '3ec6cc4a-d159-42b1-9ff9-fb8ba5bf08fe', 'qris', 'settlement', 'jl sunan ampel kecamatan bawang', 'delivery', 4, 1000, 2000, NULL, '', '2026-07-08 17:49:08', NULL),
(68, 'AES-20260708-003', 'AES-20260708-003-5BB58F', 4000, 0, 7000, 'Midtrans', 0, 2, '2026-07-08 21:05:51', '2026-07-08 21:05:51', 2, 'pending', 'unpaid', NULL, '7a8c1725-cf4c-4f36-8980-2970df64b888', NULL, NULL, NULL, 'jl sunan ampel kecamatan bawang', 'delivery', 4, 1000, 2000, NULL, '', NULL, NULL),
(69, 'AES-20260708-004', 'AES-20260708-004-509649', 4000, 0, 7000, 'Midtrans', 0, 2, '2026-07-08 21:23:32', '2026-07-08 21:23:33', 2, 'pending', 'unpaid', NULL, '330ddf5b-62cd-4cad-bd20-4e2d40a1c9d8', NULL, NULL, NULL, 'jl sunan ampel kecamatan bawang', 'delivery', 4, 1000, 2000, NULL, '', NULL, NULL),
(74, 'AES-20260709-001', 'AES-20260709-001-416AC2', 4000, 0, 7000, 'Midtrans', 0, 2, '2026-07-09 00:36:16', '2026-07-09 00:36:16', 2, 'pending', 'unpaid', NULL, '3b302004-ca92-4d23-aa5a-33ef10d702e7', NULL, NULL, NULL, 'jl sunan ampel kecamatan bawang', 'delivery', 4, 1000, 2000, NULL, 'slow', NULL, NULL),
(75, 'AES-20260709-002', 'AES-20260709-002-BE4B20', 22000, 0, 25000, 'Midtrans', 0, 2, '2026-07-09 00:49:00', '2026-07-09 00:51:58', 2, 'processing', 'paid', NULL, 'e04f2c4a-882e-44d8-88f9-76b087516583', '1ca81117-31ec-4fd3-bfd2-f3fb9eb73dfa', 'qris', 'refund', 'jl sunan ampel kecamatan bawang', 'delivery', 4, 1000, 2000, NULL, 'semoga bisa', '2026-07-09 00:49:25', NULL),
(76, 'AES-20260712-001', 'AES-20260712-001-6F569E', 4500, 0, 7500, 'Midtrans', 0, 2, '2026-07-12 01:44:03', '2026-07-12 01:44:04', 2, 'pending', 'unpaid', NULL, 'f0630e5f-05d3-4b34-810a-f6b9badfa39d', NULL, NULL, NULL, 'jl sunan ampel kecamatan bawang', 'delivery', 4, 1000, 2000, NULL, '', NULL, NULL),
(77, 'AES-20260712-002', 'AES-20260712-002-AA8A87', 22000, 0, 25000, 'Transfer', 0, 7, '2026-07-12 01:59:32', '2026-07-12 02:15:34', 7, 'completed', 'paid', 'uploads/bukti_bayar/1783796711_4f9f89cc54a3a593b9d9.jpeg', NULL, NULL, NULL, NULL, 'jalan jalan dengan sepatu rodaku', 'delivery', 4, 1000, 2000, 4, '', '2026-07-12 02:06:04', 7);

-- --------------------------------------------------------

--
-- Table structure for table `t_penjualan_detail`
--

CREATE TABLE `t_penjualan_detail` (
  `id` int UNSIGNED NOT NULL,
  `penjualan_id` int UNSIGNED NOT NULL,
  `product_id` mediumint UNSIGNED NOT NULL,
  `qty` smallint UNSIGNED NOT NULL,
  `hpp_price` mediumint UNSIGNED NOT NULL DEFAULT '0',
  `selling_price` mediumint UNSIGNED NOT NULL,
  `subtotal` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_penjualan_detail`
--

INSERT INTO `t_penjualan_detail` (`id`, `penjualan_id`, `product_id`, `qty`, `hpp_price`, `selling_price`, `subtotal`) VALUES
(14, 14, 61, 1, 0, 4000, 4000),
(15, 15, 60, 1, 0, 4000, 4000),
(16, 15, 55, 1, 0, 4500, 4500),
(18, 17, 9, 1, 13965, 22000, 22000),
(22, 25, 58, 1, 0, 4000, 4000),
(23, 26, 58, 1, 0, 4000, 4000),
(25, 28, 9, 1, 13965, 22000, 22000),
(33, 36, 9, 1, 13965, 22000, 22000),
(34, 37, 9, 1, 13965, 22000, 22000),
(36, 39, 9, 1, 13965, 22000, 22000),
(41, 42, 66, 1, 5150, 6000, 6000),
(44, 50, 9, 1, 13965, 22000, 22000),
(45, 51, 56, 1, 0, 4500, 4500),
(53, 59, 67, 1, 0, 6000, 6000),
(54, 60, 66, 1, 5150, 6000, 6000),
(55, 61, 57, 2, 0, 4000, 8000),
(56, 62, 66, 1, 5150, 6000, 6000),
(57, 63, 66, 1, 5150, 6000, 6000),
(59, 68, 57, 1, 0, 4000, 4000),
(60, 69, 60, 1, 0, 4000, 4000),
(65, 74, 61, 1, 0, 4000, 4000),
(66, 75, 9, 1, 13965, 22000, 22000),
(67, 76, 53, 1, 3279, 4500, 4500),
(68, 77, 9, 1, 13965, 22000, 22000);

-- --------------------------------------------------------

--
-- Table structure for table `t_produksi`
--

CREATE TABLE `t_produksi` (
  `id` int UNSIGNED NOT NULL,
  `kode_produksi` char(16) COLLATE utf8mb4_general_ci NOT NULL,
  `product_id` mediumint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `qty_hasil` smallint NOT NULL DEFAULT '0',
  `tanggal_produksi` date NOT NULL,
  `status_qc` enum('Lolos','Reject') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Lolos',
  `catatan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_produksi`
--

INSERT INTO `t_produksi` (`id`, `kode_produksi`, `product_id`, `user_id`, `qty_hasil`, `tanggal_produksi`, `status_qc`, `catatan`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'PRD-20260510-001', 9, 2, 5, '2026-05-10', 'Lolos', 'untuk dijual di hari senin, 11 mei 2026', '2026-05-10 16:03:21', '2026-05-10 16:03:21', NULL),
(2, 'PRD-20260610-001', 9, 2, 9, '2026-06-10', 'Lolos', '', '2026-06-10 08:24:03', '2026-06-10 08:24:03', NULL),
(3, 'PRD-20260710-001', 9, 2, 1, '2026-07-10', 'Reject', '', '2026-07-10 16:41:02', '2026-07-10 16:41:02', NULL),
(4, 'PRD-20260710-002', 9, 2, 1, '2026-07-10', 'Lolos', '', '2026-07-10 16:42:39', '2026-07-10 16:42:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `t_produksi_detail`
--

CREATE TABLE `t_produksi_detail` (
  `id` int UNSIGNED NOT NULL,
  `produksi_id` int UNSIGNED NOT NULL,
  `bahan_baku_id` mediumint UNSIGNED NOT NULL,
  `qty_digunakan` decimal(7,3) NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_produksi_detail`
--

INSERT INTO `t_produksi_detail` (`id`, `produksi_id`, `bahan_baku_id`, `qty_digunakan`, `created_at`) VALUES
(1, 1, 1, 15.000, '2026-05-10 16:03:21'),
(2, 1, 2, 60.000, '2026-05-10 16:03:21'),
(3, 1, 3, 900.000, '2026-05-10 16:03:21'),
(4, 1, 4, 30.000, '2026-05-10 16:03:21'),
(5, 2, 1, 27.000, '2026-06-10 08:24:03'),
(6, 2, 2, 108.000, '2026-06-10 08:24:03'),
(7, 2, 3, 1620.000, '2026-06-10 08:24:03'),
(8, 2, 4, 54.000, '2026-06-10 08:24:03'),
(9, 3, 1, 3.000, '2026-07-10 16:41:02'),
(10, 3, 2, 12.000, '2026-07-10 16:41:02'),
(11, 3, 3, 180.000, '2026-07-10 16:41:02'),
(12, 3, 4, 6.000, '2026-07-10 16:41:02'),
(13, 4, 1, 3.000, '2026-07-10 16:42:39'),
(14, 4, 2, 12.000, '2026-07-10 16:42:39'),
(15, 4, 3, 180.000, '2026-07-10 16:42:39'),
(16, 4, 4, 6.000, '2026-07-10 16:42:39');

-- --------------------------------------------------------

--
-- Table structure for table `t_stok_masuk`
--

CREATE TABLE `t_stok_masuk` (
  `id` int UNSIGNED NOT NULL,
  `bahan_baku_id` mediumint UNSIGNED NOT NULL,
  `qty` mediumint UNSIGNED NOT NULL,
  `isi_per_satuan` decimal(10,2) NOT NULL DEFAULT '1.00',
  `harga_satuan` mediumint UNSIGNED NOT NULL,
  `total_harga` int UNSIGNED NOT NULL DEFAULT '0',
  `nama_supplier` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_masuk` date NOT NULL,
  `keterangan` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_stok_masuk`
--

INSERT INTO `t_stok_masuk` (`id`, `bahan_baku_id`, `qty`, `isi_per_satuan`, `harga_satuan`, `total_harga`, `nama_supplier`, `tanggal_masuk`, `keterangan`, `created_at`) VALUES
(3, 1, 2, 1000.00, 15000, 30000, 'toko mekar', '2026-04-23', NULL, '2026-04-23 21:28:56'),
(4, 2, 1, 1000.00, 16000, 16000, 'toko maryam', '2026-04-23', NULL, '2026-04-23 23:58:27'),
(5, 4, 36, 1.00, 200, 7200, 'toko maryam', '2026-07-11', NULL, '2026-07-11 23:06:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(254) COLLATE utf8mb4_general_ci NOT NULL,
  `activation_selector` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activation_code` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `forgotten_password_selector` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `forgotten_password_code` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `forgotten_password_time` int UNSIGNED DEFAULT NULL,
  `remember_selector` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `remember_code` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_on` int UNSIGNED NOT NULL,
  `last_login` int UNSIGNED DEFAULT NULL,
  `active` datetime DEFAULT NULL,
  `first_name` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_name` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `city` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `province` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `postal_code` char(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `email`, `activation_selector`, `activation_code`, `forgotten_password_selector`, `forgotten_password_code`, `forgotten_password_time`, `remember_selector`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `phone`, `address`, `city`, `province`, `postal_code`, `avatar`, `deleted_at`) VALUES
(1, '127.0.0.1', 'hani', '$2y$10$cjTyDN2smU6Gtqq911B.semQt06Un3znT1IhV1oNDSGKMIDrTZt3S', 'hanifa@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1776563353, 1780963545, '2026-04-19 01:49:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, '127.0.0.1', 'admin', '$2y$12$1TfCXs1S6nIYa7Q2cq9eyuu.c3oYPolU12KcrOFdy8x1/oZoeh60i', 'admin@annyeongfoodie.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1776563353, 1783795065, '2026-04-19 01:49:13', 'Super', 'Admin', '08123456789', 'jl sunan ampel kecamatan bawang', 'Batang', 'Jawa Tengah', '51234', NULL, NULL),
(3, '127.0.0.1', 'gudang', '$2y$10$XfLmviVQME2CrIdl53bARe16BJfh9QiorjXA0dHDkhurcI22LvdAO', 'gudang@annyeongfoodie.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1776563353, 1783659082, '2026-04-19 01:49:13', 'Gudang', 'User', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, '127.0.0.1', 'kurir', '$2y$10$P6Piwl/7jeH5leeWNvzbROYzO6Bm4fMDRt4lcVWsuIBqA4x3N7ADC', 'kurir@annyeongfoodie.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '0000-00-00 00:00:00', 'Joko', 'Kendil', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, '127.0.0.1', 'pemilik', '$2y$10$BoNXhHVUarhntBsDqnl5gen3cQIMiAgWoRB8zMFBWKekBdWzDmPCS', 'pemilik@annyeongfoodie.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1783061371, 1783677606, '2026-07-03 06:49:31', 'Pemilik', 'User', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, '127.0.0.1', 'produksi', '$2y$10$EdoEfIBfffk5WSFEERFFauegx6kY1A722lCBg2OqqCVj01BOdiBNS', 'produksi@annyeongfoodie.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1783061391, NULL, '2026-07-03 06:49:51', 'Produksi', 'User', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, '127.0.0.1', 'penjualan', '$2y$10$ro.VCcTWWTShR46MB4PgO.4roj/z6TYNUAsNy8c38SZQ6rUUQ.ldK', 'penjualan@annyeongfoodie.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1783061447, 1783795581, '2026-07-03 06:50:47', 'Penjualan', 'User', '085212345678', 'jalan jalan dengan sepatu rodaku', 'batang', 'jawa tengah', '51274', NULL, NULL),
(9, '::1', 'hanigudang', '$2y$10$THho1dCJBpzMHtvEXhksM.VG7e2s4MprIuReKPIyqqFjPmXv6klsW', 'hanigudang@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1783063240, 1783533475, '2026-07-03 07:20:40', 'hani', 'gudang', '08123456789', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_groups`
--

CREATE TABLE `users_groups` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `group_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_groups`
--

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
(1, 1, 2),
(3, 2, 1),
(4, 3, 5),
(5, 4, 7),
(6, 5, 6),
(7, 6, 3),
(8, 7, 4),
(10, 9, 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `attribute_categories`
--
ALTER TABLE `attribute_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attribute_id_category_id` (`attribute_id`,`category_id`),
  ADD KEY `attribute_categories_category_id_foreign` (`category_id`);

--
-- Indexes for table `attribute_options`
--
ALTER TABLE `attribute_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attribute_options_attribute_id_foreign` (`attribute_id`),
  ADD KEY `slug` (`slug`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`),
  ADD KEY `slug` (`slug`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_bahan_baku`
--
ALTER TABLE `m_bahan_baku`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_bahan` (`kode_bahan`),
  ADD KEY `m_bahan_baku_category_id_foreign` (`category_id`);

--
-- Indexes for table `m_recipes`
--
ALTER TABLE `m_recipes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id_bahan_baku_id` (`product_id`,`bahan_baku_id`),
  ADD KEY `m_recipes_bahan_baku_id_foreign` (`bahan_baku_id`);

--
-- Indexes for table `m_shipping_area`
--
ALTER TABLE `m_shipping_area`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_user_id_foreign` (`user_id`),
  ADD KEY `slug` (`slug`),
  ADD KEY `sku` (`sku`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `product_attribute_values`
--
ALTER TABLE `product_attribute_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_attribute_values_attribute_option_id_foreign` (`attribute_option_id`),
  ADD KEY `product_attribute_values_attribute_id_foreign` (`attribute_id`),
  ADD KEY `product_attribute_values_product_id_foreign` (`product_id`),
  ADD KEY `product_attribute_values_parent_product_id_foreign` (`parent_product_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_categories_product_id_foreign` (`product_id`),
  ADD KEY `product_categories_category_id_foreign` (`category_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_images_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_inventories`
--
ALTER TABLE `product_inventories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id_user_id` (`product_id`,`user_id`),
  ADD KEY `product_reviews_user_id_foreign` (`user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `t_absensi`
--
ALTER TABLE `t_absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_absensi_user_id_foreign` (`user_id`);

--
-- Indexes for table `t_penjualan`
--
ALTER TABLE `t_penjualan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`),
  ADD KEY `t_penjualan_kasir_id_foreign` (`kasir_id`),
  ADD KEY `fk_t_penjualan_customer` (`customer_id`),
  ADD KEY `fk_t_penjualan_verified_by` (`verified_by`),
  ADD KEY `fk_shipping_area` (`shipping_area_id`),
  ADD KEY `fk_delivery_courier` (`delivery_courier_id`);

--
-- Indexes for table `t_penjualan_detail`
--
ALTER TABLE `t_penjualan_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_penjualan_detail_penjualan_id_foreign` (`penjualan_id`),
  ADD KEY `t_penjualan_detail_product_id_foreign` (`product_id`);

--
-- Indexes for table `t_produksi`
--
ALTER TABLE `t_produksi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_produksi` (`kode_produksi`),
  ADD KEY `t_produksi_product_id_foreign` (`product_id`),
  ADD KEY `t_produksi_user_id_foreign` (`user_id`);

--
-- Indexes for table `t_produksi_detail`
--
ALTER TABLE `t_produksi_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_produksi_detail_produksi_id_foreign` (`produksi_id`),
  ADD KEY `t_produksi_detail_bahan_baku_id_foreign` (`bahan_baku_id`);

--
-- Indexes for table `t_stok_masuk`
--
ALTER TABLE `t_stok_masuk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_stok_masuk_bahan_baku_id_foreign` (`bahan_baku_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `activation_selector` (`activation_selector`),
  ADD UNIQUE KEY `forgotten_password_selector` (`forgotten_password_selector`),
  ADD UNIQUE KEY `remember_selector` (`remember_selector`);

--
-- Indexes for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_groups_user_id_foreign` (`user_id`),
  ADD KEY `users_groups_group_id_foreign` (`group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attributes`
--
ALTER TABLE `attributes`
  MODIFY `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `attribute_categories`
--
ALTER TABLE `attribute_categories`
  MODIFY `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attribute_options`
--
ALTER TABLE `attribute_options`
  MODIFY `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `m_bahan_baku`
--
ALTER TABLE `m_bahan_baku`
  MODIFY `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `m_recipes`
--
ALTER TABLE `m_recipes`
  MODIFY `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `m_shipping_area`
--
ALTER TABLE `m_shipping_area`
  MODIFY `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `product_attribute_values`
--
ALTER TABLE `product_attribute_values`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `product_inventories`
--
ALTER TABLE `product_inventories`
  MODIFY `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `t_absensi`
--
ALTER TABLE `t_absensi`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `t_penjualan`
--
ALTER TABLE `t_penjualan`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `t_penjualan_detail`
--
ALTER TABLE `t_penjualan_detail`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `t_produksi`
--
ALTER TABLE `t_produksi`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `t_produksi_detail`
--
ALTER TABLE `t_produksi_detail`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `t_stok_masuk`
--
ALTER TABLE `t_stok_masuk`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users_groups`
--
ALTER TABLE `users_groups`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attribute_categories`
--
ALTER TABLE `attribute_categories`
  ADD CONSTRAINT `attribute_categories_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `attribute_categories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `attribute_options`
--
ALTER TABLE `attribute_options`
  ADD CONSTRAINT `attribute_options_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints for table `m_bahan_baku`
--
ALTER TABLE `m_bahan_baku`
  ADD CONSTRAINT `m_bahan_baku_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints for table `m_recipes`
--
ALTER TABLE `m_recipes`
  ADD CONSTRAINT `m_recipes_bahan_baku_id_foreign` FOREIGN KEY (`bahan_baku_id`) REFERENCES `m_bahan_baku` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `m_recipes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints for table `product_attribute_values`
--
ALTER TABLE `product_attribute_values`
  ADD CONSTRAINT `product_attribute_values_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`),
  ADD CONSTRAINT `product_attribute_values_attribute_option_id_foreign` FOREIGN KEY (`attribute_option_id`) REFERENCES `attribute_options` (`id`),
  ADD CONSTRAINT `product_attribute_values_parent_product_id_foreign` FOREIGN KEY (`parent_product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `product_attribute_values_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD CONSTRAINT `product_categories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_categories_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `product_inventories`
--
ALTER TABLE `product_inventories`
  ADD CONSTRAINT `product_inventories_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `t_absensi`
--
ALTER TABLE `t_absensi`
  ADD CONSTRAINT `t_absensi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_penjualan`
--
ALTER TABLE `t_penjualan`
  ADD CONSTRAINT `fk_delivery_courier` FOREIGN KEY (`delivery_courier_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_shipping_area` FOREIGN KEY (`shipping_area_id`) REFERENCES `m_shipping_area` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_t_penjualan_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_t_penjualan_verified_by` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `t_penjualan_kasir_id_foreign` FOREIGN KEY (`kasir_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_penjualan_detail`
--
ALTER TABLE `t_penjualan_detail`
  ADD CONSTRAINT `t_penjualan_detail_penjualan_id_foreign` FOREIGN KEY (`penjualan_id`) REFERENCES `t_penjualan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `t_penjualan_detail_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_produksi`
--
ALTER TABLE `t_produksi`
  ADD CONSTRAINT `t_produksi_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_produksi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints for table `t_produksi_detail`
--
ALTER TABLE `t_produksi_detail`
  ADD CONSTRAINT `t_produksi_detail_bahan_baku_id_foreign` FOREIGN KEY (`bahan_baku_id`) REFERENCES `m_bahan_baku` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_produksi_detail_produksi_id_foreign` FOREIGN KEY (`produksi_id`) REFERENCES `t_produksi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `t_stok_masuk`
--
ALTER TABLE `t_stok_masuk`
  ADD CONSTRAINT `t_stok_masuk_bahan_baku_id_foreign` FOREIGN KEY (`bahan_baku_id`) REFERENCES `m_bahan_baku` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD CONSTRAINT `users_groups_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_groups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

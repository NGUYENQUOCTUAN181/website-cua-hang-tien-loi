-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th9 13, 2026 lúc 06:08 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `convenience_store`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `addresses`
--

CREATE TABLE `addresses` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `receiver_name` varchar(100) NOT NULL,
  `receiver_phone` varchar(20) NOT NULL,
  `province` varchar(100) NOT NULL,
  `district` varchar(100) NOT NULL,
  `ward` varchar(100) NOT NULL,
  `address_detail` varchar(255) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `banners`
--

CREATE TABLE `banners` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `image_url` varchar(500) NOT NULL,
  `link_url` varchar(500) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

CREATE TABLE `brands` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `brands`
--

INSERT INTO `brands` (`id`, `name`, `description`, `status`, `created_at`) VALUES
(1, 'Coca-Cola', 'Thương hiệu nước giải khát nổi tiếng', 'active', '2026-09-10 17:45:42'),
(2, 'Pepsi', 'Thương hiệu nước giải khát', 'active', '2026-09-10 17:45:42'),
(3, 'Aquafina', 'Thương hiệu nước uống đóng chai', 'active', '2026-09-10 17:45:42'),
(4, 'Omachi', 'Thương hiệu mì ăn liền cao cấp', 'active', '2026-09-10 17:45:42'),
(5, 'Hảo Hảo', 'Thương hiệu mì ăn liền phổ biến', 'active', '2026-09-10 17:45:42'),
(6, 'Oreo', 'Thương hiệu bánh quy nổi tiếng', 'active', '2026-09-10 17:45:42'),
(7, 'Lay\'s', 'Thương hiệu snack khoai tây', 'active', '2026-09-10 17:45:42'),
(8, 'Comfort', 'Thương hiệu nước xả vải', 'active', '2026-09-10 17:45:42'),
(9, '3 Miền', NULL, 'active', '2026-09-12 18:40:40'),
(10, '7Up', NULL, 'active', '2026-09-12 18:40:40'),
(11, 'Alpenliebe', NULL, 'active', '2026-09-12 18:40:40'),
(12, 'C2', NULL, 'active', '2026-09-12 18:40:40'),
(13, 'CP', NULL, 'active', '2026-09-12 18:40:40'),
(14, 'Chinsu', NULL, 'active', '2026-09-12 18:40:40'),
(15, 'ChocoPie', NULL, 'active', '2026-09-12 18:40:40'),
(16, 'Colgate', NULL, 'active', '2026-09-12 18:40:40'),
(17, 'Cosy', NULL, 'active', '2026-09-12 18:40:40'),
(18, 'Dove', NULL, 'active', '2026-09-12 18:40:40'),
(19, 'Dutch Lady', NULL, 'active', '2026-09-12 18:40:40'),
(20, 'Gift', NULL, 'active', '2026-09-12 18:40:40'),
(21, 'Head & Shoulders', NULL, 'active', '2026-09-12 18:40:40'),
(22, 'KitKat', NULL, 'active', '2026-09-12 18:40:40'),
(23, 'Knorr', NULL, 'active', '2026-09-12 18:40:40'),
(24, 'Kokomi', NULL, 'active', '2026-09-12 18:40:40'),
(25, 'Lifebuoy', NULL, 'active', '2026-09-12 18:40:40'),
(26, 'Mentos', NULL, 'active', '2026-09-12 18:40:40'),
(27, 'Milo', NULL, 'active', '2026-09-12 18:40:40'),
(28, 'Mirinda', NULL, 'active', '2026-09-12 18:40:40'),
(29, 'Monster', NULL, 'active', '2026-09-12 18:40:40'),
(30, 'Nam Ngư', NULL, 'active', '2026-09-12 18:40:40'),
(31, 'Number 1', NULL, 'active', '2026-09-12 18:40:40'),
(32, 'OMO', NULL, 'active', '2026-09-12 18:40:40'),
(33, 'Oishi', NULL, 'active', '2026-09-12 18:40:40'),
(34, 'P/S', NULL, 'active', '2026-09-12 18:40:40'),
(35, 'Phở Đệ Nhất', NULL, 'active', '2026-09-12 18:40:40'),
(36, 'Poca', NULL, 'active', '2026-09-12 18:40:40'),
(37, 'Pocky', NULL, 'active', '2026-09-12 18:40:40'),
(38, 'Red Bull', NULL, 'active', '2026-09-12 18:40:40'),
(39, 'Revive', NULL, 'active', '2026-09-12 18:40:40'),
(40, 'Sting', NULL, 'active', '2026-09-12 18:40:40'),
(41, 'Sunlight', NULL, 'active', '2026-09-12 18:40:40'),
(42, 'Sunsilk', NULL, 'active', '2026-09-12 18:40:40'),
(43, 'TH True Milk', NULL, 'active', '2026-09-12 18:40:40'),
(44, 'Vinamilk', NULL, 'active', '2026-09-12 18:40:40'),
(45, 'Vissan', NULL, 'active', '2026-09-12 18:40:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

CREATE TABLE `carts` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-09-10 18:13:38', '2026-09-10 18:13:38'),
(2, 2, '2026-09-11 04:37:45', '2026-09-11 04:37:45'),
(3, 3, '2026-09-12 16:56:08', '2026-09-12 16:56:08'),
(4, 44, '2026-09-12 19:40:33', '2026-09-12 19:40:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `cart_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Đồ uống', 'Các loại nước uống và đồ giải khát', 'active', '2026-09-10 17:45:42', '2026-09-10 17:45:42'),
(2, NULL, 'Mì & đồ ăn liền', 'Các loại thực phẩm và đồ ăn nhanh', 'active', '2026-09-10 17:45:42', '2026-09-12 19:31:37'),
(3, NULL, 'Bánh & kẹo', 'Bánh, kẹo và đồ ăn vặt', 'active', '2026-09-10 17:45:42', '2026-09-12 19:31:37'),
(4, NULL, 'Gia dụng', 'Các sản phẩm gia dụng hằng ngày', 'active', '2026-09-10 17:45:42', '2026-09-10 17:45:42'),
(5, 1, 'Nước ngọt', 'Nước ngọt có gas và không gas', 'active', '2026-09-10 17:45:42', '2026-09-10 17:45:42'),
(6, 1, 'Nước suối', 'Các loại nước đóng chai', 'active', '2026-09-10 17:45:42', '2026-09-10 17:45:42'),
(7, 2, 'Mì ăn liền', 'Các loại mì ăn liền', 'active', '2026-09-10 17:45:42', '2026-09-10 17:45:42'),
(8, 2, 'Đồ ăn nhanh', 'Thực phẩm ăn nhanh tiện lợi', 'active', '2026-09-10 17:45:42', '2026-09-10 17:45:42'),
(9, 1, 'Cà phê', 'Các loại cà phê', 'active', '2026-09-10 18:54:19', '2026-09-10 18:54:19'),
(12, NULL, 'Snack', NULL, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(13, NULL, 'Sữa & dinh dưỡng', NULL, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(14, NULL, 'Thực phẩm khô', NULL, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(15, NULL, 'Chăm sóc cá nhân', NULL, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `order_code` varchar(50) NOT NULL,
  `receiver_name` varchar(100) NOT NULL,
  `receiver_phone` varchar(20) NOT NULL,
  `shipping_address` varchar(500) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `shipping_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `voucher_code` varchar(50) DEFAULT NULL,
  `payment_method` enum('COD','BANK_TRANSFER') DEFAULT 'COD',
  `payment_status` enum('unpaid','paid','failed') DEFAULT 'unpaid',
  `status` enum('pending','confirmed','shipping','completed','cancelled') DEFAULT 'pending',
  `note` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_code`, `receiver_name`, `receiver_phone`, `shipping_address`, `subtotal`, `discount`, `shipping_fee`, `total`, `voucher_code`, `payment_method`, `payment_status`, `status`, `note`, `created_at`, `updated_at`) VALUES
(1, 1, 'CS-20260910-202647-790936', 'Nguyễn Quốc Tuấn', '0901234567', '123 Nguyễn Trãi, Quận 1, TP.HCM', 90000.00, 0.00, 0.00, 90000.00, NULL, 'COD', 'unpaid', 'cancelled', 'giờ hành chính', '2026-09-10 18:26:47', '2026-09-10 18:37:11'),
(2, 2, 'CS-20260911-065349-3AC17E', 'Ngân Phan', '0901234568', '68 Trường Sơn', 90000.00, 9000.00, 0.00, 81000.00, 'GIAM10', 'COD', 'unpaid', 'completed', '', '2026-09-11 04:53:49', '2026-09-12 17:37:26'),
(3, 2, 'CS-20260911-182140-2DEA22', 'Ngân Phan', '0901234568', '68 Trường Sơn', 168500.00, 16850.00, 0.00, 151650.00, 'GIAM10', 'COD', 'unpaid', 'completed', '', '2026-09-11 16:21:40', '2026-09-12 17:37:21'),
(4, 3, 'CS-20260912-185711-1A19AC', 'Bảo Hân', '0944083135', 'Số 3 Huyền Trân Công Chúa', 294000.00, 29400.00, 0.00, 264600.00, 'GIAM10', 'COD', 'unpaid', 'completed', '', '2026-09-12 16:57:11', '2026-09-12 17:37:13'),
(5, 44, 'CS-20260912-214109-F46DB5', 'Trung Tín', '0908313333', '12/4 Lê Trọng Tấn', 250000.00, 0.00, 0.00, 250000.00, NULL, 'COD', 'unpaid', 'completed', '', '2026-09-12 19:41:09', '2026-09-13 03:17:19'),
(6, 3, 'CS-20260913-050342-DB0A20', 'Bảo Hân', '0944083135', '12/3 Lê trọng tấn', 390000.00, 0.00, 0.00, 390000.00, NULL, 'COD', 'unpaid', 'completed', '', '2026-09-13 03:03:42', '2026-09-13 03:17:13');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `price`, `quantity`, `subtotal`) VALUES
(1, 1, 1, 'Coca-Cola Original 330ml', 9000.00, 10, 90000.00),
(2, 2, 1, 'Coca-Cola Original 330ml', 9000.00, 10, 90000.00),
(3, 3, 2, 'Pepsi Lon 330ml', 8500.00, 1, 8500.00),
(4, 3, 8, 'Bánh Oreo Socola', 16000.00, 10, 160000.00),
(5, 4, 9, 'Bánh Oreo Dâu', 16500.00, 10, 165000.00),
(6, 4, 12, 'Nước Xả Vải Comfort 800ml', 42000.00, 2, 84000.00),
(7, 4, 14, 'Mì Hảo Hảo Gà', 4500.00, 10, 45000.00),
(8, 5, 1, 'Coca-Cola Original 330ml', 9000.00, 10, 90000.00),
(9, 5, 41, 'Oreo Mini Chocolate 67g', 16000.00, 10, 160000.00),
(10, 6, 75, 'Cá Viên CP 200g', 39000.00, 10, 390000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `brand_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(220) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sale_price` decimal(12,2) DEFAULT NULL,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `brand_id`, `name`, `slug`, `description`, `price`, `sale_price`, `stock`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 'Coca-Cola Original 330ml', 'coca-cola-original-330ml', 'Nước ngọt Coca-Cola lon 330ml.', 10000.00, 9000.00, 75, 'active', '2026-09-10 17:45:42', '2026-09-12 19:41:09'),
(2, 5, 2, 'Pepsi Lon 330ml', 'pepsi-lon-330ml', 'Nước ngọt Pepsi lon 330ml.', 10000.00, 8500.00, 119, 'active', '2026-09-10 17:45:42', '2026-09-11 16:21:40'),
(3, 6, 3, 'Aquafina 500ml', 'aquafina-500ml', 'Nước tinh khiết Aquafina chai 500ml.', 6000.00, 5500.00, 200, 'active', '2026-09-10 17:45:42', '2026-09-10 17:45:42'),
(4, 6, 3, 'Aquafina 1.5L', 'aquafina-1-5l', 'Nước tinh khiết Aquafina chai 1.5L.', 10000.00, 9000.00, 150, 'active', '2026-09-10 17:45:42', '2026-09-10 17:45:42'),
(5, 7, 5, 'Mì Hảo Hảo Tôm Chua Cay', 'mi-hao-hao-tom-chua-cay', 'Mì ăn liền Hảo Hảo vị tôm chua cay.', 5000.00, 4500.00, 300, 'active', '2026-09-10 17:45:42', '2026-09-10 17:45:42'),
(6, 7, 5, 'Mì Hảo Hảo Sa Tế Hành', 'mi-hao-hao-sa-te-hanh', 'Mì Hảo Hảo vị sa tế hành.', 5000.00, 4500.00, 250, 'active', '2026-09-10 17:45:42', '2026-09-10 17:45:42'),
(7, 7, 4, 'Mì Omachi Sườn Hầm Ngũ Quả', 'mi-omachi-suon-ham-ngu-qua', 'Mì Omachi cao cấp vị sườn hầm ngũ quả.', 12000.00, 10500.00, 100, 'active', '2026-09-10 17:45:42', '2026-09-10 17:45:42'),
(8, 8, 6, 'Bánh Oreo Socola', 'banh-oreo-socola', 'Bánh quy Oreo vị socola.', 18000.00, 16000.00, 70, 'active', '2026-09-10 17:45:42', '2026-09-11 16:21:40'),
(9, 8, 6, 'Bánh Oreo Dâu', 'banh-oreo-dau', 'Bánh quy Oreo vị kem dâu.', 18000.00, 16500.00, 60, 'active', '2026-09-10 17:45:42', '2026-09-12 16:57:11'),
(10, 8, 7, 'Snack Lay\'s Classic', 'snack-lays-classic', 'Snack khoai tây Lay\'s vị truyền thống.', 15000.00, 13000.00, 90, 'active', '2026-09-10 17:45:42', '2026-09-10 17:45:42'),
(11, 8, 7, 'Snack Lay\'s BBQ', 'snack-lays-bbq', 'Snack khoai tây Lay\'s vị BBQ.', 15000.00, 13500.00, 90, 'active', '2026-09-10 17:45:42', '2026-09-10 17:45:42'),
(12, 4, 8, 'Nước Xả Vải Comfort 800ml', 'nuoc-xa-vai-comfort-800ml', 'Nước xả vải Comfort 800ml.', 45000.00, 42000.00, 48, 'active', '2026-09-10 17:45:42', '2026-09-12 16:57:11'),
(13, 4, 8, 'Nước Xả Vải Comfort 1.8L', 'nuoc-xa-vai-comfort-1-8l', 'Nước xả vải Comfort chai 1.8L.', 85000.00, 79000.00, 35, 'active', '2026-09-10 17:45:42', '2026-09-10 17:45:42'),
(14, 2, 5, 'Mì Hảo Hảo Gà', 'mi-hao-hao-ga', 'Mì ăn liền Hảo Hảo vị gà.', 5000.00, 4500.00, 210, 'active', '2026-09-10 17:45:42', '2026-09-12 16:57:11'),
(15, 2, 4, 'Mì Omachi Xốt Bò Hầm', 'mi-omachi-xot-bo-ham', 'Mì Omachi xốt bò hầm thơm ngon.', 12000.00, 10500.00, 80, 'active', '2026-09-10 17:45:42', '2026-09-10 17:45:42'),
(16, 1, NULL, 'Sting Dâu 330ml', 'sting-d-au-330ml', '', 10000.00, 9000.00, 2, 'inactive', '2026-09-10 18:48:56', '2026-09-10 18:49:22'),
(17, 1, 2, 'Pepsi Black 330ml', 'pepsi-black-330ml', 'Pepsi cola không đường lon 330ml.', 10000.00, 9000.00, 72, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(18, 1, 1, 'Coca-Cola Zero 330ml', 'coca-cola-zero-330ml', 'Coca-Cola Zero lon 330ml.', 10000.00, 9000.00, 65, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(19, 1, 40, 'Sting Vàng 330ml', 'sting-vang-330ml', 'Nước tăng lực Sting vị vàng.', 10000.00, 9000.00, 54, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(20, 1, 38, 'Red Bull 250ml', 'red-bull-250ml', 'Nước tăng lực Red Bull.', 15000.00, 13500.00, 48, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(21, 1, 29, 'Monster Energy 355ml', 'monster-energy-355ml', 'Nước tăng lực Monster Energy.', 35000.00, 32000.00, 28, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(22, 1, 10, '7Up Lemon Lime 330ml', '7up-330ml', 'Nước ngọt 7Up.', 9000.00, 8000.00, 60, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(23, 1, 28, 'Mirinda Cam 330ml', 'mirinda-cam-330ml', 'Nước ngọt Mirinda cam.', 9000.00, 8000.00, 58, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(24, 1, 12, 'C2 Trà Xanh 455ml', 'c2-tra-xanh-455ml', 'Trà xanh C2.', 10000.00, 8500.00, 73, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(25, 1, 39, 'Revive Chanh Muối 390ml', 'revive-chanh-muoi-390ml', 'Nước uống thể thao Revive.', 10000.00, 9000.00, 55, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(26, 1, 31, 'Number 1 330ml', 'number-1-330ml', 'Nước tăng lực Number 1.', 10000.00, 9000.00, 49, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(27, 2, 5, 'Mì Hảo Hảo Tôm Sa Tế 75g', 'mi-hao-hao-tom-sa-te-75g', 'Mì ăn liền Hảo Hảo vị tôm sa tế.', 5000.00, 4500.00, 180, 'active', '2026-09-12 18:40:40', '2026-09-12 19:31:37'),
(28, 2, 9, 'Mì 3 Miền Tôm Chua Cay 65g', 'mi-3-mien-tom-chua-cay-65g', 'Mì 3 Miền tôm chua cay.', 4500.00, 4000.00, 165, 'active', '2026-09-12 18:40:40', '2026-09-12 19:31:37'),
(29, 2, 9, 'Mì 3 Miền Bò Hầm 65g', 'mi-3-mien-bo-ham-65g', 'Mì 3 Miền bò hầm.', 4500.00, 4000.00, 150, 'active', '2026-09-12 18:40:40', '2026-09-12 19:31:37'),
(30, 2, 24, 'Mì Kokomi Tôm Chua Cay 65g', 'mi-kokomi-tom-chua-cay-65g', 'Mì Kokomi tôm chua cay.', 4000.00, 3500.00, 210, 'active', '2026-09-12 18:40:40', '2026-09-12 19:31:37'),
(31, 2, 4, 'Mì Omachi Spaghetti Sốt Cà Chua', 'mi-omachi-spaghetti-sot-ca-chua', 'Mì Omachi spaghetti.', 12000.00, 10500.00, 82, 'active', '2026-09-12 18:40:40', '2026-09-12 19:31:37'),
(32, 2, 4, 'Mì Omachi Khoai Tây Tôm Hùm', 'mi-omachi-tom-hum', 'Mì Omachi vị tôm hùm.', 15000.00, 13500.00, 60, 'active', '2026-09-12 18:40:40', '2026-09-12 19:31:37'),
(33, 2, 35, 'Phở Đệ Nhất Bò Hầm', 'pho-de-nhat-bo-ham', 'Phở ăn liền bò hầm.', 9000.00, 8000.00, 95, 'active', '2026-09-12 18:40:40', '2026-09-12 19:31:37'),
(34, 2, 35, 'Phở Đệ Nhất Gà', 'pho-de-nhat-ga', 'Phở ăn liền vị gà.', 9000.00, 8000.00, 90, 'active', '2026-09-12 18:40:40', '2026-09-12 19:31:37'),
(35, 3, 17, 'Bánh Cosy Marie 300g', 'banh-cosy-marie-300g', 'Bánh quy Marie Cosy.', 32000.00, 29000.00, 40, 'active', '2026-09-12 18:40:40', '2026-09-12 19:31:37'),
(36, 3, 15, 'ChocoPie Original 6 bánh', 'chocopie-original-6-banh', 'Bánh ChocoPie truyền thống.', 35000.00, 32000.00, 45, 'active', '2026-09-12 18:40:40', '2026-09-12 19:31:37'),
(37, 3, 37, 'Pocky Chocolate 47g', 'pocky-chocolate-47g', 'Bánh que Pocky chocolate.', 22000.00, 20000.00, 36, 'active', '2026-09-12 18:40:40', '2026-09-12 19:31:37'),
(38, 3, 22, 'KitKat 2 Fingers 17g', 'kitkat-2-fingers-17g', 'Thanh KitKat 2 Fingers.', 12000.00, 10000.00, 62, 'active', '2026-09-12 18:40:40', '2026-09-12 19:31:37'),
(39, 3, 26, 'Kẹo Mentos Mint 37g', 'mentos-mint-37g', 'Kẹo Mentos bạc hà.', 15000.00, 13000.00, 50, 'active', '2026-09-12 18:40:40', '2026-09-12 19:31:37'),
(40, 3, 11, 'Kẹo Alpenliebe Caramel 37g', 'alpenliebe-caramel-37g', 'Kẹo Alpenliebe caramel.', 12000.00, 10000.00, 75, 'active', '2026-09-12 18:40:40', '2026-09-12 19:31:37'),
(41, 3, 6, 'Oreo Mini Chocolate 67g', 'oreo-mini-chocolate-67g', 'Oreo mini chocolate.', 18000.00, 16000.00, 42, 'active', '2026-09-12 18:40:40', '2026-09-12 19:41:09'),
(42, 3, 6, 'Oreo Vanilla 133g', 'oreo-vanilla-133g', 'Oreo vanilla.', 28000.00, 25000.00, 42, 'active', '2026-09-12 18:40:40', '2026-09-12 19:31:37'),
(43, 12, 7, 'Lay\'s Vị Tảo Biển 50g', 'lays-tao-bien-50g', 'Khoai tây Lay\'s tảo biển.', 16000.00, 14000.00, 70, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(44, 12, 7, 'Lay\'s Phô Mai 52g', 'lays-pho-mai-52g', 'Khoai tây Lay\'s phô mai.', 16000.00, 14000.00, 65, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(45, 12, 33, 'Oishi Snack Bắp 44g', 'oishi-bap-44g', 'Snack bắp Oishi.', 10000.00, 9000.00, 88, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(46, 12, 33, 'Oishi Tôm Cay 46g', 'oishi-tom-cay-46g', 'Snack tôm cay.', 10000.00, 9000.00, 80, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(47, 12, 36, 'Poca Phô Mai 42g', 'poca-pho-mai-42g', 'Snack Poca phô mai.', 12000.00, 10000.00, 58, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(48, 12, 36, 'Poca Bò Nướng 42g', 'poca-bo-nuong-42g', 'Snack Poca bò nướng.', 12000.00, 10000.00, 57, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(49, 13, 44, 'Vinamilk 100% Có Đường 180ml', 'vinamilk-sua-tuoi-duong-180ml', 'Sữa tươi Vinamilk có đường.', 7000.00, 6500.00, 120, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(50, 13, 44, 'Vinamilk Không Đường 180ml', 'vinamilk-khong-duong-180ml', 'Sữa tươi Vinamilk không đường.', 7000.00, 6500.00, 115, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(51, 13, 43, 'TH True Milk Có Đường 180ml', 'th-true-milk-duong-180ml', 'Sữa tươi TH True Milk.', 8000.00, 7500.00, 96, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(52, 13, 43, 'TH True Milk Không Đường 180ml', 'th-true-milk-khong-duong-180ml', '', 8000.00, 7500.00, 93, 'inactive', '2026-09-12 18:40:40', '2026-09-12 19:02:41'),
(53, 13, 27, 'Milo Hộp 180ml', 'milo-hop-180ml', 'Nước uống lúa mạch Milo.', 9000.00, 8500.00, 85, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(54, 13, 19, 'Dutch Lady 180ml Có Đường', 'dutch-lady-180ml', 'Sữa tươi Dutch Lady.', 7000.00, 6500.00, 100, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(55, 14, 30, 'Nước Mắm Nam Ngư 500ml', 'nuoc-mam-nam-ngu-500ml', 'Nước mắm Nam Ngư.', 32000.00, 29000.00, 44, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(56, 14, 14, 'Nước Tương Chinsu 500ml', 'nuoc-tuong-chinsu-500ml', 'Nước tương Chinsu.', 18000.00, 16000.00, 55, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(57, 14, 14, 'Dầu Ăn Neptune 1L', 'dau-an-neptune-1l', 'Dầu ăn Neptune 1L.', 45000.00, 42000.00, 35, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(58, 14, 23, 'Hạt Nêm Knorr Thịt Thăn 170g', 'hat-nem-knorr-thit-than-170g', 'Hạt nêm Knorr.', 25000.00, 22000.00, 40, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(59, 14, 14, 'Bột Canh Chinsu 200g', 'bot-canh-chinsu-200g', 'Bột canh Chinsu.', 9000.00, 8000.00, 90, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(60, 14, 14, 'Tương Ớt Chinsu 250g', 'tuong-ot-chinsu-250g', 'Tương ớt Chinsu.', 13000.00, 11000.00, 70, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(61, 15, 42, 'Dầu Gội Sunsilk Óng Mượt 180ml', 'dau-goi-sunsilk-180ml', 'Dầu gội Sunsilk.', 39000.00, 35000.00, 38, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(62, 15, 21, 'Dầu Gội Head & Shoulders 170ml', 'dau-goi-head-shoulders-170ml', 'Dầu gội Head & Shoulders.', 62000.00, 55000.00, 31, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(63, 15, 18, 'Sữa Tắm Dove 250g', 'sua-tam-dove-250g', 'Sữa tắm Dove.', 65000.00, 59000.00, 27, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(64, 15, 25, 'Sữa Tắm Lifebuoy 250ml', 'sua-tam-lifebuoy-250ml', 'Sữa tắm Lifebuoy.', 45000.00, 42000.00, 44, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(65, 15, 34, 'Kem Đánh Răng P/S 150g', 'kem-danh-rang-ps-150g', 'Kem đánh răng P/S.', 36000.00, 32000.00, 50, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(66, 15, 16, 'Kem Đánh Răng Colgate 150g', 'kem-danh-rang-colgate-150g', 'Kem đánh răng Colgate.', 39000.00, 35000.00, 43, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(67, 4, 32, 'Nước Giặt OMO 800g', 'nuoc-giat-omo-800g', 'Nước giặt OMO.', 62000.00, 56000.00, 33, 'inactive', '2026-09-12 18:40:40', '2026-09-12 19:21:53'),
(68, 4, 41, 'Nước Rửa Chén Sunlight 750ml', 'nuoc-rua-chen-sunlight-750ml', 'Nước rửa chén Sunlight.', 32000.00, 29000.00, 52, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(69, 4, 8, 'Nước Xả Comfort 900ml', 'nuoc-xa-comfort-900ml', 'Nước xả Comfort.', 49000.00, 45000.00, 29, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(70, 4, 32, 'Bột Giặt OMO 800g', 'bot-giat-omo-800g', 'Bột giặt OMO.', 58000.00, 52000.00, 36, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(71, 4, 20, 'Nước Lau Sàn Gift 1L', 'nuoc-lau-san-gift-1l', 'Nước lau sàn Gift.', 38000.00, 34000.00, 25, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(72, 4, 20, 'Khăn Giấy Hộp Gift 180 tờ', 'khan-giay-gift-180-to', 'Khăn giấy Gift.', 27000.00, 24000.00, 45, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(73, 8, 13, 'Xúc Xích CP Phô Mai 175g', 'xuc-xich-cp-pho-mai-175g', 'Xúc xích CP phô mai.', 35000.00, 32000.00, 40, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(74, 8, 45, 'Xúc Xích Vissan 200g', 'xuc-xich-vissan-200g', 'Xúc xích Vissan.', 38000.00, 35000.00, 38, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40'),
(75, 8, 13, 'Cá Viên CP 200g', 'ca-vien-cp-200g', 'Cá viên CP.', 42000.00, 39000.00, 20, 'active', '2026-09-12 18:40:40', '2026-09-13 03:03:42'),
(76, 8, 45, 'Bò Viên Vissan 175g', 'bo-vien-vissan-175g', 'Bò viên Vissan.', 45000.00, 41000.00, 26, 'active', '2026-09-12 18:40:40', '2026-09-12 18:40:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `is_primary`, `created_at`) VALUES
(1, 1, 'uploads/products/coca-cola-330ml.png', 1, '2026-09-10 17:45:42'),
(2, 1, 'https://placehold.co/600x600/png?text=Coca-Cola+330ml+2', 0, '2026-09-10 17:45:42'),
(3, 2, 'uploads/products/pepsi-330ml.png', 1, '2026-09-10 17:45:42'),
(4, 2, 'https://placehold.co/600x600/png?text=Pepsi+330ml+2', 0, '2026-09-10 17:45:42'),
(5, 3, 'uploads/products/aquafina-500ml.png', 1, '2026-09-10 17:45:42'),
(6, 3, 'https://placehold.co/600x600/png?text=Aquafina+500ml+2', 0, '2026-09-10 17:45:42'),
(7, 4, 'uploads/products/aquafina-1-5l.png', 1, '2026-09-10 17:45:42'),
(8, 5, 'uploads/products/hao-hao-tom-chua-cay.png', 1, '2026-09-10 17:45:42'),
(9, 5, 'https://placehold.co/600x600/png?text=Hao+Hao+Tom+Chua+Cay+2', 0, '2026-09-10 17:45:42'),
(10, 6, 'uploads/products/hao-hao-sa-te-hanh.png', 1, '2026-09-10 17:45:42'),
(11, 7, 'uploads/products/omachi-suon-ham-ngu-qua.png', 1, '2026-09-10 17:45:42'),
(12, 8, 'uploads/products/oreo-original.png', 1, '2026-09-10 17:45:42'),
(13, 8, 'https://placehold.co/600x600/png?text=Oreo+Socola+2', 0, '2026-09-10 17:45:42'),
(14, 9, 'uploads/products/oreo-dau.png', 1, '2026-09-10 17:45:42'),
(15, 10, 'uploads/products/lays-classic.png', 1, '2026-09-10 17:45:42'),
(16, 11, 'uploads/products/lays-bbq.png', 1, '2026-09-10 17:45:42'),
(17, 12, 'uploads/products/comfort-huong-hoa.png', 1, '2026-09-10 17:45:42'),
(18, 13, 'uploads/products/comfort-1-8l.png', 1, '2026-09-10 17:45:42'),
(19, 14, 'uploads/products/hao-hao-ga.png', 1, '2026-09-10 17:45:42'),
(20, 15, 'uploads/products/omachi-xot-bo-ham.png', 1, '2026-09-10 17:45:42'),
(21, 17, 'uploads/products/pepsi-black-330ml.png', 1, '2026-09-12 19:10:35'),
(22, 18, 'uploads/products/coca-cola-zero-330ml.png', 1, '2026-09-12 19:10:35'),
(23, 19, 'uploads/products/sting-vang-330ml.png', 1, '2026-09-12 19:10:35'),
(24, 20, 'uploads/products/red-bull-250ml.png', 1, '2026-09-12 19:10:35'),
(25, 21, 'uploads/products/monster-energy-355ml.png', 1, '2026-09-12 19:10:35'),
(26, 22, 'uploads/products/7up-330ml.png', 1, '2026-09-12 19:10:35'),
(27, 23, 'uploads/products/mirinda-cam-330ml.png', 1, '2026-09-12 19:10:35'),
(28, 24, 'uploads/products/c2-tra-xanh-455ml.png', 1, '2026-09-12 19:10:35'),
(29, 25, 'uploads/products/revive-chanh-muoi-390ml.png', 1, '2026-09-12 19:10:35'),
(30, 26, 'uploads/products/number-1-330ml.png', 1, '2026-09-12 19:10:35'),
(31, 27, 'uploads/products/mi-hao-hao-tom-sa-te-75g.png', 1, '2026-09-12 19:10:35'),
(32, 28, 'uploads/products/mi-3-mien-tom-chua-cay-65g.png', 1, '2026-09-12 19:10:35'),
(33, 29, 'uploads/products/mi-3-mien-bo-ham-65g.png', 1, '2026-09-12 19:10:35'),
(34, 30, 'uploads/products/mi-kokomi-tom-chua-cay-65g.png', 1, '2026-09-12 19:10:35'),
(35, 31, 'uploads/products/mi-omachi-spaghetti-sot-ca-chua.png', 1, '2026-09-12 19:10:35'),
(36, 32, 'uploads/products/mi-omachi-tom-hum.png', 1, '2026-09-12 19:10:35'),
(37, 33, 'uploads/products/pho-de-nhat-bo-ham.png', 1, '2026-09-12 19:10:35'),
(38, 34, 'uploads/products/pho-de-nhat-ga.png', 1, '2026-09-12 19:10:35'),
(39, 35, 'uploads/products/banh-cosy-marie-300g.png', 1, '2026-09-12 19:10:35'),
(40, 36, 'uploads/products/chocopie-original-6-banh.png', 1, '2026-09-12 19:10:35'),
(41, 37, 'uploads/products/pocky-chocolate-47g.png', 1, '2026-09-12 19:10:35'),
(42, 38, 'uploads/products/kitkat-2-fingers-17g.png', 1, '2026-09-12 19:10:35'),
(43, 39, 'uploads/products/mentos-mint-37g.png', 1, '2026-09-12 19:10:35'),
(44, 40, 'uploads/products/alpenliebe-caramel-37g.png', 1, '2026-09-12 19:10:35'),
(45, 41, 'uploads/products/oreo-mini-chocolate-67g.png', 1, '2026-09-12 19:10:35'),
(46, 42, 'uploads/products/oreo-vanilla-133g.png', 1, '2026-09-12 19:10:35'),
(47, 43, 'uploads/products/lays-tao-bien-50g.png', 1, '2026-09-12 19:10:35'),
(48, 44, 'uploads/products/lays-pho-mai-52g.png', 1, '2026-09-12 19:10:35'),
(49, 45, 'uploads/products/oishi-bap-44g.png', 1, '2026-09-12 19:10:35'),
(50, 46, 'uploads/products/oishi-tom-cay-46g.png', 1, '2026-09-12 19:10:35'),
(51, 47, 'uploads/products/poca-pho-mai-42g.png', 1, '2026-09-12 19:10:35'),
(52, 48, 'uploads/products/poca-bo-nuong-42g.png', 1, '2026-09-12 19:10:35'),
(53, 49, 'uploads/products/vinamilk-sua-tuoi-duong-180ml.png', 1, '2026-09-12 19:10:35'),
(54, 50, 'uploads/products/vinamilk-khong-duong-180ml.png', 1, '2026-09-12 19:10:35'),
(55, 51, 'uploads/products/th-true-milk-duong-180ml.png', 1, '2026-09-12 19:10:35'),
(56, 52, 'uploads/products/th-true-milk-khong-duong-180ml.png', 1, '2026-09-12 19:10:35'),
(57, 53, 'uploads/products/milo-hop-180ml.png', 1, '2026-09-12 19:10:35'),
(58, 54, 'uploads/products/dutch-lady-180ml.png', 1, '2026-09-12 19:10:35'),
(59, 55, 'uploads/products/nuoc-mam-nam-ngu-500ml.png', 1, '2026-09-12 19:10:35'),
(60, 56, 'uploads/products/nuoc-tuong-chinsu-500ml.png', 1, '2026-09-12 19:10:35'),
(61, 57, 'uploads/products/dau-an-neptune-1l.png', 1, '2026-09-12 19:10:35'),
(62, 58, 'uploads/products/hat-nem-knorr-thit-than-170g.png', 1, '2026-09-12 19:10:35'),
(63, 59, 'uploads/products/bot-canh-chinsu-200g.png', 1, '2026-09-12 19:10:35'),
(64, 60, 'uploads/products/tuong-ot-chinsu-250g.png', 1, '2026-09-12 19:10:35'),
(65, 61, 'uploads/products/dau-goi-sunsilk-180ml.png', 1, '2026-09-12 19:10:35'),
(66, 62, 'uploads/products/dau-goi-head-shoulders-170ml.png', 1, '2026-09-12 19:10:35'),
(67, 63, 'uploads/products/sua-tam-dove-250g.png', 1, '2026-09-12 19:10:35'),
(68, 64, 'uploads/products/sua-tam-lifebuoy-250ml.png', 1, '2026-09-12 19:10:35'),
(69, 65, 'uploads/products/kem-danh-rang-ps-150g.png', 1, '2026-09-12 19:10:35'),
(70, 66, 'uploads/products/kem-danh-rang-colgate-150g.png', 1, '2026-09-12 19:10:35'),
(71, 67, 'uploads/products/nuoc-giat-omo-800g.png', 1, '2026-09-12 19:10:35'),
(72, 68, 'uploads/products/nuoc-rua-chen-sunlight-750ml.png', 1, '2026-09-12 19:10:35'),
(73, 69, 'uploads/products/nuoc-xa-comfort-900ml.png', 1, '2026-09-12 19:10:35'),
(74, 70, 'uploads/products/bot-giat-omo-800g.png', 1, '2026-09-12 19:10:35'),
(75, 71, 'uploads/products/nuoc-lau-san-gift-1l.png', 1, '2026-09-12 19:10:35'),
(76, 72, 'uploads/products/khan-giay-gift-180-to.png', 1, '2026-09-12 19:10:35'),
(77, 73, 'uploads/products/xuc-xich-cp-pho-mai-175g.png', 1, '2026-09-12 19:10:35'),
(78, 74, 'uploads/products/xuc-xich-vissan-200g.png', 1, '2026-09-12 19:10:35'),
(79, 75, 'uploads/products/ca-vien-cp-200g.png', 1, '2026-09-12 19:10:35'),
(80, 76, 'uploads/products/bo-vien-vissan-175g.png', 1, '2026-09-12 19:10:35');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` text DEFAULT NULL,
  `status` enum('pending','approved','hidden') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'SUPER_ADMIN', 'Quản trị viên cao nhất', '2026-08-26 18:03:05'),
(2, 'ADMIN', 'Quản trị viên', '2026-08-26 18:03:05'),
(3, 'STAFF', 'Nhân viên', '2026-08-26 18:03:05'),
(4, 'CUSTOMER', 'Khách hàng', '2026-08-26 18:03:05');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` enum('active','locked') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `password`, `phone`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Nguyễn Quốc Tuấn', 'test@gmail.com', '$2y$10$Ggrm/9C87HjCcIA2Vyq0p.2oxwQgHu4OpMijfjIatRdqIAf5FeX2.', '0901234567', 'active', '2026-09-10 17:31:44', '2026-09-10 18:41:42'),
(2, 4, 'Ngân Phan', 'nganthanh@gmail.com', '$2y$10$0bTP9zURrOcm/86UAZt1f.33kcykSduZOGY6TMuTMpXMA/DnzkGri', '0933332610', 'active', '2026-09-11 04:31:45', '2026-09-11 04:31:45'),
(3, 4, 'Bảo Hân', 'baohan123@gmail.com', '$2y$10$cBzxvIdM4uSAbFbRI48t/ulqw6sE5XYM6isNu.fVTk4axXff5yi7u', '0944083135', 'active', '2026-09-12 16:55:22', '2026-09-12 16:55:22'),
(4, 4, 'Nguyễn Minh Anh', 'demo.customer01@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000001', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(5, 4, 'Trần Gia Hân', 'demo.customer02@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000002', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(6, 4, 'Lê Hoàng Nam', 'demo.customer03@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000003', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(7, 4, 'Phạm Khánh Linh', 'demo.customer04@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000004', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(8, 4, 'Võ Quốc Bảo', 'demo.customer05@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000005', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(9, 4, 'Đặng Ngọc Mai', 'demo.customer06@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000006', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(10, 4, 'Bùi Thanh Tùng', 'demo.customer07@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000007', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(11, 4, 'Phan Thảo Vy', 'demo.customer08@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000008', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(12, 4, 'Huỳnh Đức Minh', 'demo.customer09@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000009', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(13, 4, 'Ngô Bảo Trân', 'demo.customer10@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000010', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(14, 4, 'Đỗ Nhật Huy', 'demo.customer11@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000011', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(15, 4, 'Trương Mỹ Duyên', 'demo.customer12@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000012', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(16, 4, 'Lý Minh Khang', 'demo.customer13@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000013', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(17, 4, 'Dương Quỳnh Anh', 'demo.customer14@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000014', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(18, 4, 'Mai Tuấn Kiệt', 'demo.customer15@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000015', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(19, 4, 'Cao Ngọc Hương', 'demo.customer16@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000016', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(20, 4, 'Vũ Anh Khoa', 'demo.customer17@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000017', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(21, 4, 'Nguyễn Hà My', 'demo.customer18@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000018', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(22, 4, 'Trần Quốc Khải', 'demo.customer19@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000019', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(23, 4, 'Lê Phương Nhi', 'demo.customer20@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000020', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(24, 4, 'Phạm Minh Quân', 'demo.customer21@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000021', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(25, 4, 'Võ Thu Hà', 'demo.customer22@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000022', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(26, 4, 'Đặng Thành Đạt', 'demo.customer23@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000023', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(27, 4, 'Bùi Ngọc Lan', 'demo.customer24@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000024', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(28, 4, 'Phan Gia Bảo', 'demo.customer25@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000025', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(29, 4, 'Huỳnh Khánh Vy', 'demo.customer26@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000026', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(30, 4, 'Ngô Minh Đức', 'demo.customer27@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000027', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(31, 4, 'Đỗ Thanh Thảo', 'demo.customer28@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000028', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(32, 4, 'Trương Hoài Nam', 'demo.customer29@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000029', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(33, 4, 'Lý Bảo Ngọc', 'demo.customer30@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000030', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(34, 4, 'Dương Minh Triết', 'demo.customer31@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000031', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(35, 4, 'Mai Khánh An', 'demo.customer32@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000032', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(36, 4, 'Cao Hoàng Long', 'demo.customer33@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000033', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(37, 4, 'Vũ Ngọc Ánh', 'demo.customer34@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000034', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(38, 4, 'Nguyễn Thiên Phúc', 'demo.customer35@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000035', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(39, 4, 'Trần Minh Châu', 'demo.customer36@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000036', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(40, 4, 'Lê Đức Anh', 'demo.customer37@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000037', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(41, 4, 'Phạm Ngọc Yến', 'demo.customer38@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000038', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(42, 4, 'Võ Minh Nhật', 'demo.customer39@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000039', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(43, 4, 'Đặng Quỳnh Như', 'demo.customer40@nhaminhmart.local', '$2y$12$qv0U.ccp0zjF.THj/hsgJulPNZYo9Ufm45tZaA3dr/3Gt3tNIm.3C', '0901000040', 'active', '2026-09-12 17:42:39', '2026-09-12 17:42:39'),
(44, 4, 'Tín Trung', 'trungtin@gmail.com', '$2y$10$ugaNQHcH7pBrvEXVsk2Z8O.HvJVdEmLsA.QE7pmSE5iWHthfJgbxy', '0849333333', 'active', '2026-09-12 19:39:54', '2026-09-12 19:39:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vouchers`
--

CREATE TABLE `vouchers` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `discount_type` enum('percent','fixed') NOT NULL,
  `discount_value` decimal(12,2) NOT NULL,
  `min_order_value` decimal(12,2) DEFAULT 0.00,
  `max_discount` decimal(12,2) DEFAULT NULL,
  `quantity` int(10) UNSIGNED DEFAULT 0,
  `used_quantity` int(10) UNSIGNED DEFAULT 0,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `vouchers`
--

INSERT INTO `vouchers` (`id`, `code`, `description`, `discount_type`, `discount_value`, `min_order_value`, `max_discount`, `quantity`, `used_quantity`, `start_date`, `end_date`, `status`, `created_at`) VALUES
(1, 'GIAM10', '', 'percent', 10.00, 0.00, NULL, 100, 3, '2026-09-11 00:10:00', '2026-10-11 00:10:00', 'active', '2026-09-11 04:30:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wishlists`
--

CREATE TABLE `wishlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `wishlists`
--

INSERT INTO `wishlists` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(1, 3, 42, '2026-09-13 03:47:00'),
(2, 3, 41, '2026-09-13 03:47:02'),
(3, 3, 40, '2026-09-13 03:47:03');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_addresses_user` (`user_id`);

--
-- Chỉ mục cho bảng `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Chỉ mục cho bảng `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_product` (`cart_id`,`product_id`),
  ADD KEY `fk_cart_items_product` (`product_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_categories_parent` (`parent_id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notifications_user` (`user_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_items_order` (`order_id`),
  ADD KEY `fk_order_items_product` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_products_category` (`category_id`),
  ADD KEY `fk_products_brand` (`brand_id`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_images_product` (`product_id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reviews_product` (`product_id`),
  ADD KEY `fk_reviews_user` (`user_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- Chỉ mục cho bảng `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_wishlist_user_product` (`user_id`,`product_id`),
  ADD KEY `idx_wishlist_user` (`user_id`),
  ADD KEY `idx_wishlist_product` (`product_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT cho bảng `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `fk_addresses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `fk_carts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_items_cart` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cart_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

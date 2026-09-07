-- =========================================================
-- MiniMart / SwiftMart - Full schema + rich seed data
-- database/schema.sql (v2 - du lieu day du de demo)
-- Engine: InnoDB | Charset: utf8mb4
-- =========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Xoa toan bo bang cu (neu co) theo dung thu tu de tranh loi khoa ngoai
DROP TABLE IF EXISTS `contact_messages`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `banners`;
DROP TABLE IF EXISTS `wishlists`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `vouchers`;
DROP TABLE IF EXISTS `cart_items`;
DROP TABLE IF EXISTS `carts`;
DROP TABLE IF EXISTS `product_images`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `brands`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `addresses`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `roles`;

-- =========================================================
-- 1. ROLES & USERS
-- =========================================================

CREATE TABLE `roles` (
  `id`   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `role_id`    INT UNSIGNED NOT NULL DEFAULT 4,
  `name`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `phone`      VARCHAR(20)  DEFAULT NULL,
  `avatar`     VARCHAR(255) DEFAULT NULL,
  `status`     TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_users_role` (`role_id`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `addresses` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`      INT UNSIGNED NOT NULL,
  `receiver_name` VARCHAR(100) NOT NULL,
  `phone`        VARCHAR(20) NOT NULL,
  `address_line` VARCHAR(255) NOT NULL,
  `city`         VARCHAR(100) DEFAULT NULL,
  `is_default`   TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_addresses_user` (`user_id`),
  CONSTRAINT `fk_addresses_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- 2. CATEGORIES & BRANDS
-- =========================================================

CREATE TABLE `categories` (
  `id`        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `parent_id` INT UNSIGNED DEFAULT NULL,
  `name`      VARCHAR(100) NOT NULL,
  `slug`      VARCHAR(120) NOT NULL UNIQUE,
  `image`     VARCHAR(255) DEFAULT NULL,
  `status`    TINYINT(1) NOT NULL DEFAULT 1,
  KEY `idx_categories_parent` (`parent_id`),
  CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `brands` (
  `id`   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `logo` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- 3. PRODUCTS
-- =========================================================

CREATE TABLE `products` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NOT NULL,
  `brand_id`    INT UNSIGNED DEFAULT NULL,
  `name`        VARCHAR(200) NOT NULL,
  `slug`        VARCHAR(220) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `price`       DECIMAL(12,2) NOT NULL DEFAULT 0,
  `sale_price`  DECIMAL(12,2) DEFAULT NULL,
  `stock`       INT NOT NULL DEFAULT 0,
  `sold_count`  INT NOT NULL DEFAULT 0,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `status`      TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_products_category` (`category_id`),
  KEY `idx_products_brand` (`brand_id`),
  FULLTEXT KEY `ft_products_name` (`name`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_products_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `product_images` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `image_url`  VARCHAR(255) NOT NULL,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  KEY `idx_product_images_product` (`product_id`),
  CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- 4. CART
-- =========================================================

CREATE TABLE `carts` (
  `id`      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL UNIQUE,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_carts_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cart_items` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `cart_id`    INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity`   INT UNSIGNED NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_cart_product` (`cart_id`, `product_id`),
  CONSTRAINT `fk_cart_items_cart` FOREIGN KEY (`cart_id`) REFERENCES `carts`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_items_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- 5. VOUCHERS
-- =========================================================

CREATE TABLE `vouchers` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code`           VARCHAR(50) NOT NULL UNIQUE,
  `discount_type`  ENUM('percent','fixed') NOT NULL DEFAULT 'percent',
  `discount_value` DECIMAL(12,2) NOT NULL,
  `min_order`      DECIMAL(12,2) NOT NULL DEFAULT 0,
  `usage_limit`    INT DEFAULT NULL,
  `used_count`     INT NOT NULL DEFAULT 0,
  `expiry_date`    DATE DEFAULT NULL,
  `status`         TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- 6. ORDERS
-- =========================================================

CREATE TABLE `orders` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`        INT UNSIGNED NOT NULL,
  `voucher_id`     INT UNSIGNED DEFAULT NULL,
  `receiver_name`  VARCHAR(100) NOT NULL,
  `phone`          VARCHAR(20) NOT NULL,
  `address`        VARCHAR(255) NOT NULL,
  `subtotal`       DECIMAL(12,2) NOT NULL DEFAULT 0,
  `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `total`          DECIMAL(12,2) NOT NULL DEFAULT 0,
  `payment_method` ENUM('cod','bank_transfer','vnpay','momo') NOT NULL DEFAULT 'cod',
  `status`         ENUM('pending','processing','shipping','completed','cancelled') NOT NULL DEFAULT 'pending',
  `note`           VARCHAR(255) DEFAULT NULL,
  `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_orders_user` (`user_id`),
  KEY `idx_orders_status` (`status`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_orders_voucher` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `order_items` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id`   INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `quantity`   INT UNSIGNED NOT NULL DEFAULT 1,
  `price`      DECIMAL(12,2) NOT NULL,
  KEY `idx_order_items_order` (`order_id`),
  KEY `idx_order_items_product` (`product_id`),
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- 7. REVIEWS & WISHLIST
-- =========================================================

CREATE TABLE `reviews` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `user_id`    INT UNSIGNED NOT NULL,
  `rating`     TINYINT UNSIGNED NOT NULL,
  `comment`    TEXT DEFAULT NULL,
  `status`     TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_reviews_product` (`product_id`),
  KEY `idx_reviews_user` (`user_id`),
  CONSTRAINT `chk_reviews_rating` CHECK (`rating` BETWEEN 1 AND 5),
  CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `wishlists` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_wishlist_user_product` (`user_id`, `product_id`),
  CONSTRAINT `fk_wishlists_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlists_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- 8. BANNERS & POSTS (CMS)
-- =========================================================

CREATE TABLE `banners` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title`      VARCHAR(150) NOT NULL,
  `image_url`  VARCHAR(255) NOT NULL,
  `link_url`   VARCHAR(255) DEFAULT NULL,
  `position`   VARCHAR(50) DEFAULT 'home_slider',
  `sort_order` INT NOT NULL DEFAULT 0,
  `status`     TINYINT(1) NOT NULL DEFAULT 1,
  `start_date` DATE DEFAULT NULL,
  `end_date`   DATE DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `posts` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `author_id`  INT UNSIGNED DEFAULT NULL,
  `title`      VARCHAR(200) NOT NULL,
  `slug`       VARCHAR(220) NOT NULL UNIQUE,
  `thumbnail`  VARCHAR(255) DEFAULT NULL,
  `content`    LONGTEXT DEFAULT NULL,
  `status`     TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_posts_author` (`author_id`),
  CONSTRAINT `fk_posts_author` FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- 9. NOTIFICATIONS
-- =========================================================

CREATE TABLE `notifications` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT UNSIGNED NOT NULL,
  `title`      VARCHAR(200) NOT NULL,
  `message`    VARCHAR(255) DEFAULT NULL,
  `is_read`    TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_notifications_user` (`user_id`),
  CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- 10. CONTACT MESSAGES
-- =========================================================

CREATE TABLE `contact_messages` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT UNSIGNED DEFAULT NULL,
  `name`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL,
  `subject`    VARCHAR(200) DEFAULT NULL,
  `message`    TEXT NOT NULL,
  `status`     ENUM('new','read','replied') NOT NULL DEFAULT 'new',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_contact_user` (`user_id`),
  CONSTRAINT `fk_contact_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================
-- SEED DATA
-- =========================================================

INSERT INTO `roles` (`id`, `name`) VALUES
  (1, 'Super Admin'), (2, 'Admin'), (3, 'Staff'), (4, 'Customer');

-- Mat khau cho tat ca tai khoan mau la: 123456
INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `password`, `phone`, `status`) VALUES
  (1, 1, 'Quản trị viên', 'admin@store.com', '$2b$10$4y/A.Mw95e7iTVca9RqJ5.hT86L80ZiRjb7gZASoWtQL3QHU/5iGi', '0900000001', 1),
  (2, 4, 'Nguyễn Văn Khách', 'customer@store.com', '$2b$10$4y/A.Mw95e7iTVca9RqJ5.hT86L80ZiRjb7gZASoWtQL3QHU/5iGi', '0900000002', 1),
  (3, 4, 'Trần Thị Mai', 'mai.tran@example.com', '$2b$10$4y/A.Mw95e7iTVca9RqJ5.hT86L80ZiRjb7gZASoWtQL3QHU/5iGi', '0900000003', 1),
  (4, 3, 'Lê Văn Nhân Viên', 'staff@store.com', '$2b$10$4y/A.Mw95e7iTVca9RqJ5.hT86L80ZiRjb7gZASoWtQL3QHU/5iGi', '0900000004', 1);

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `image`, `status`) VALUES
  (1, NULL, 'Đồ ăn vặt', 'do-an-vat', 'https://picsum.photos/seed/cat-snack/500/400', 1),
  (2, NULL, 'Nước giải khát', 'nuoc-giai-khat', 'https://picsum.photos/seed/cat-drink/500/400', 1),
  (3, NULL, 'Hóa mỹ phẩm', 'hoa-my-pham', 'https://picsum.photos/seed/cat-beauty/500/400', 1),
  (4, NULL, 'Đồ dùng gia đình', 'do-dung-gia-dinh', 'https://picsum.photos/seed/cat-household/500/400', 1),
  (5, NULL, 'Đồ tươi sống', 'do-tuoi-song', 'https://picsum.photos/seed/cat-fresh/500/400', 1),
  (6, NULL, 'Bánh mì và bánh ngọt', 'banh-mi-banh-ngot', 'https://picsum.photos/seed/cat-bakery/500/400', 1),
  (7, 1, 'Bánh kẹo', 'banh-keo', NULL, 1),
  (8, 1, 'Snack mặn', 'snack-man', NULL, 1),
  (9, 2, 'Nước ngọt', 'nuoc-ngot', NULL, 1),
  (10, 2, 'Nước ép và trà', 'nuoc-ep-va-tra', NULL, 1),
  (11, 3, 'Chăm sóc cá nhân', 'cham-soc-ca-nhan', NULL, 1),
  (12, 3, 'Chăm sóc tóc', 'cham-soc-toc', NULL, 1),
  (13, 4, 'Dụng cụ nhà bếp', 'dung-cu-nha-bep', NULL, 1),
  (14, 4, 'Vệ sinh nhà cửa', 've-sinh-nha-cua', NULL, 1),
  (15, 5, 'Rau củ quả', 'rau-cu-qua', NULL, 1),
  (16, 5, 'Trái cây', 'trai-cay', NULL, 1),
  (17, 6, 'Bánh mì', 'banh-mi', NULL, 1),
  (18, 6, 'Bánh ngọt', 'banh-ngot', NULL, 1);

INSERT INTO `brands` (`id`, `name`) VALUES
  (1, 'Coca-Cola'),
  (2, 'Orion'),
  (3, 'P/S'),
  (4, 'Vinamilk'),
  (5, 'Dove'),
  (6, 'Sunlight'),
  (7, 'Kinh Đô'),
  (8, 'Trung Nguyên'),
  (9, 'TH true MILK'),
  (10, 'Lay''s'),
  (11, 'Nabati'),
  (12, 'Comfort');

INSERT INTO `products`
  (`id`, `category_id`, `brand_id`, `name`, `slug`, `description`, `price`, `sale_price`, `stock`, `sold_count`, `is_featured`, `status`) VALUES
  (1, 9, 1, 'Coca-Cola lon 330ml', 'coca-cola-lon-330ml', 'Nước ngọt Coca-Cola lon 330ml, vị truyền thống sảng khoái.', 10000, 9000, 200, 340, 1, 1),
  (2, 9, 1, 'Coca-Cola chai 1.5L', 'coca-cola-chai-15l', 'Chai lớn tiện lợi cho cả gia đình, dùng trong các buổi tiệc.', 25000, NULL, 120, 90, 0, 1),
  (3, 9, 1, 'Sprite lon 330ml', 'sprite-lon-330ml', 'Nước ngọt có gas vị chanh mát lạnh.', 10000, NULL, 150, 120, 0, 1),
  (4, 7, 2, 'Bánh Custas hộp 12 gói', 'banh-custas-hop-12-goi', 'Bánh bông lan nhân kem Custas, hộp 12 gói tiện lợi mang đi.', 45000, NULL, 100, 210, 1, 1),
  (5, 7, 2, 'Bánh Chocopie vị socola', 'banh-chocopie-vi-socola', 'Bánh bông lan phủ socola mềm mịn, nhân marshmallow thơm ngon.', 22000, 19000, 90, 150, 0, 1),
  (6, 7, 11, 'Kẹo que Yan Yan', 'keo-que-yan-yan', 'Que bánh giòn chấm socola, phù hợp làm đồ ăn vặt.', 18000, NULL, 130, 80, 0, 1),
  (7, 8, 10, 'Snack khoai tây Lay''s vị phô mai', 'snack-khoai-tay-lay-s-vi-pho-mai', 'Khoai tây chiên giòn tan vị phô mai đậm đà.', 15000, NULL, 160, 260, 1, 1),
  (8, 8, 2, 'Snack Oishi vị tôm cay', 'snack-oishi-vi-tom-cay', 'Snack tôm cay giòn rụm, gói nhỏ tiện lợi.', 8000, NULL, 200, 300, 0, 1),
  (9, 11, 3, 'Kem đánh răng P/S bảo vệ 123', 'kem-danh-rang-p-s-bao-ve-123', 'Kem đánh răng P/S bảo vệ 123, ngừa sâu răng hiệu quả.', 25000, NULL, 150, 95, 0, 1),
  (10, 11, 5, 'Sữa tắm Dove dưỡng ẩm', 'sua-tam-dove-duong-am', 'Sữa tắm Dove chiết xuất 1/4 kem dưỡng ẩm, chai 500ml.', 65000, 55000, 80, 60, 1, 1),
  (11, 12, 5, 'Dầu gội Dove phục hồi hư tổn', 'dau-goi-dove-phuc-hoi-hu-ton', 'Dầu gội phục hồi tóc hư tổn, chai 650ml.', 78000, NULL, 70, 45, 0, 1),
  (12, 14, 6, 'Nước rửa chén Sunlight hương chanh', 'nuoc-rua-chen-sunlight-huong-chanh', 'Nước rửa chén Sunlight hương chanh, chai 750ml.', 32000, NULL, 140, 110, 0, 1),
  (13, 14, 12, 'Nước xả vải Comfort hương nước hoa', 'nuoc-xa-vai-comfort-huong-nuoc-hoa', 'Nước xả vải Comfort lưu hương lâu phai, túi 3.6L.', 89000, 79000, 60, 70, 1, 1),
  (14, 13, 6, 'Bộ dao kéo inox 6 món', 'bo-dao-keo-inox-6-mon', 'Bộ dao kéo inox cao cấp 6 món dùng trong bếp.', 120000, NULL, 40, 20, 0, 1),
  (15, 13, 6, 'Hộp đựng thực phẩm 3 món', 'hop-dung-thuc-pham-3-mon', 'Bộ hộp nhựa đựng thực phẩm an toàn, có nắp kín.', 95000, NULL, 55, 30, 0, 1),
  (16, 9, 4, 'Sữa tươi Vinamilk 1 lít', 'sua-tuoi-vinamilk-1-lit', 'Sữa tươi tiệt trùng nguyên chất Vinamilk, hộp 1 lít.', 32000, 29000, 80, 500, 1, 1),
  (17, 9, 4, 'Sữa chua Vinamilk có đường lốc 4', 'sua-chua-vinamilk-co-duong-loc-4', 'Sữa chua có đường Vinamilk, lốc 4 hộp 100g.', 28000, NULL, 100, 220, 0, 1),
  (18, 9, 9, 'Sữa tươi TH true MILK 900ml', 'sua-tuoi-th-true-milk-900ml', 'Sữa tươi tiệt trùng TH true MILK không đường.', 34000, NULL, 90, 180, 0, 1),
  (19, 10, 8, 'Cà phê sữa đá Trung Nguyên lon', 'ca-phe-sua-da-trung-nguyen-lon', 'Cà phê sữa đá đóng lon, tiện lợi mang theo.', 12000, NULL, 180, 260, 0, 1),
  (20, 10, 8, 'Trà xanh không độ C2', 'tra-xanh-khong-do-c2', 'Trà xanh vị chanh giải nhiệt, chai 455ml.', 9000, NULL, 220, 310, 0, 1),
  (21, 16, NULL, 'Táo xanh Envy (1kg)', 'tao-xanh-envy-1kg', 'Táo xanh Envy nhập khẩu, giòn ngọt.', 65000, NULL, 60, 40, 0, 1),
  (22, 16, NULL, 'Chuối già (1 nải)', 'chuoi-gia-1-nai', 'Chuối già chín tự nhiên, ngọt và thơm.', 35000, NULL, 45, 55, 0, 1),
  (23, 15, NULL, 'Cà chua (1kg)', 'ca-chua-1kg', 'Cà chua tươi, chín đỏ, giàu vitamin C.', 22000, NULL, 80, 70, 0, 1),
  (24, 15, NULL, 'Rau cải ngọt (500g)', 'rau-cai-ngot-500g', 'Rau cải ngọt hữu cơ, tươi mới hái trong ngày.', 12000, NULL, 100, 90, 0, 1),
  (25, 17, 7, 'Bánh mì sandwich nguyên cám', 'banh-mi-sandwich-nguyen-cam', 'Bánh mì sandwich nguyên cám giàu chất xơ.', 28000, NULL, 50, 65, 0, 1),
  (26, 17, 7, 'Bánh mì que giòn', 'banh-mi-que-gion', 'Bánh mì que giòn ruột đặc, gói 5 chiếc.', 15000, NULL, 70, 85, 0, 1),
  (27, 18, 7, 'Bánh croissant bơ Pháp', 'banh-croissant-bo-phap', 'Bánh croissant bơ thơm, lớp vỏ giòn xốp.', 18000, 15000, 40, 55, 1, 1),
  (28, 18, NULL, 'Bánh sinh nhật mini socola', 'banh-sinh-nhat-mini-socola', 'Bánh sinh nhật mini vị socola, size 1 người ăn.', 55000, NULL, 25, 18, 0, 1);

INSERT INTO `product_images` (`product_id`, `image_url`, `is_primary`, `sort_order`) VALUES
  (1, 'https://picsum.photos/seed/coca-cola-lon-330ml-1/600/600', 1, 0),
  (1, 'https://picsum.photos/seed/coca-cola-lon-330ml-2/600/600', 0, 1),
  (2, 'https://picsum.photos/seed/coca-cola-chai-15l-1/600/600', 1, 0),
  (2, 'https://picsum.photos/seed/coca-cola-chai-15l-2/600/600', 0, 1),
  (3, 'https://picsum.photos/seed/sprite-lon-330ml-1/600/600', 1, 0),
  (3, 'https://picsum.photos/seed/sprite-lon-330ml-2/600/600', 0, 1),
  (4, 'https://picsum.photos/seed/banh-custas-hop-12-goi-1/600/600', 1, 0),
  (4, 'https://picsum.photos/seed/banh-custas-hop-12-goi-2/600/600', 0, 1),
  (5, 'https://picsum.photos/seed/banh-chocopie-vi-socola-1/600/600', 1, 0),
  (5, 'https://picsum.photos/seed/banh-chocopie-vi-socola-2/600/600', 0, 1),
  (6, 'https://picsum.photos/seed/keo-que-yan-yan-1/600/600', 1, 0),
  (6, 'https://picsum.photos/seed/keo-que-yan-yan-2/600/600', 0, 1),
  (7, 'https://picsum.photos/seed/snack-khoai-tay-lay-s-vi-pho-mai-1/600/600', 1, 0),
  (7, 'https://picsum.photos/seed/snack-khoai-tay-lay-s-vi-pho-mai-2/600/600', 0, 1),
  (8, 'https://picsum.photos/seed/snack-oishi-vi-tom-cay-1/600/600', 1, 0),
  (8, 'https://picsum.photos/seed/snack-oishi-vi-tom-cay-2/600/600', 0, 1),
  (9, 'https://picsum.photos/seed/kem-danh-rang-p-s-bao-ve-123-1/600/600', 1, 0),
  (9, 'https://picsum.photos/seed/kem-danh-rang-p-s-bao-ve-123-2/600/600', 0, 1),
  (10, 'https://picsum.photos/seed/sua-tam-dove-duong-am-1/600/600', 1, 0),
  (10, 'https://picsum.photos/seed/sua-tam-dove-duong-am-2/600/600', 0, 1),
  (11, 'https://picsum.photos/seed/dau-goi-dove-phuc-hoi-hu-ton-1/600/600', 1, 0),
  (11, 'https://picsum.photos/seed/dau-goi-dove-phuc-hoi-hu-ton-2/600/600', 0, 1),
  (12, 'https://picsum.photos/seed/nuoc-rua-chen-sunlight-huong-chanh-1/600/600', 1, 0),
  (12, 'https://picsum.photos/seed/nuoc-rua-chen-sunlight-huong-chanh-2/600/600', 0, 1),
  (13, 'https://picsum.photos/seed/nuoc-xa-vai-comfort-huong-nuoc-hoa-1/600/600', 1, 0),
  (13, 'https://picsum.photos/seed/nuoc-xa-vai-comfort-huong-nuoc-hoa-2/600/600', 0, 1),
  (14, 'https://picsum.photos/seed/bo-dao-keo-inox-6-mon-1/600/600', 1, 0),
  (14, 'https://picsum.photos/seed/bo-dao-keo-inox-6-mon-2/600/600', 0, 1),
  (15, 'https://picsum.photos/seed/hop-dung-thuc-pham-3-mon-1/600/600', 1, 0),
  (15, 'https://picsum.photos/seed/hop-dung-thuc-pham-3-mon-2/600/600', 0, 1),
  (16, 'https://picsum.photos/seed/sua-tuoi-vinamilk-1-lit-1/600/600', 1, 0),
  (16, 'https://picsum.photos/seed/sua-tuoi-vinamilk-1-lit-2/600/600', 0, 1),
  (17, 'https://picsum.photos/seed/sua-chua-vinamilk-co-duong-loc-4-1/600/600', 1, 0),
  (17, 'https://picsum.photos/seed/sua-chua-vinamilk-co-duong-loc-4-2/600/600', 0, 1),
  (18, 'https://picsum.photos/seed/sua-tuoi-th-true-milk-900ml-1/600/600', 1, 0),
  (18, 'https://picsum.photos/seed/sua-tuoi-th-true-milk-900ml-2/600/600', 0, 1),
  (19, 'https://picsum.photos/seed/ca-phe-sua-da-trung-nguyen-lon-1/600/600', 1, 0),
  (19, 'https://picsum.photos/seed/ca-phe-sua-da-trung-nguyen-lon-2/600/600', 0, 1),
  (20, 'https://picsum.photos/seed/tra-xanh-khong-do-c2-1/600/600', 1, 0),
  (20, 'https://picsum.photos/seed/tra-xanh-khong-do-c2-2/600/600', 0, 1),
  (21, 'https://picsum.photos/seed/tao-xanh-envy-1kg-1/600/600', 1, 0),
  (21, 'https://picsum.photos/seed/tao-xanh-envy-1kg-2/600/600', 0, 1),
  (22, 'https://picsum.photos/seed/chuoi-gia-1-nai-1/600/600', 1, 0),
  (22, 'https://picsum.photos/seed/chuoi-gia-1-nai-2/600/600', 0, 1),
  (23, 'https://picsum.photos/seed/ca-chua-1kg-1/600/600', 1, 0),
  (23, 'https://picsum.photos/seed/ca-chua-1kg-2/600/600', 0, 1),
  (24, 'https://picsum.photos/seed/rau-cai-ngot-500g-1/600/600', 1, 0),
  (24, 'https://picsum.photos/seed/rau-cai-ngot-500g-2/600/600', 0, 1),
  (25, 'https://picsum.photos/seed/banh-mi-sandwich-nguyen-cam-1/600/600', 1, 0),
  (25, 'https://picsum.photos/seed/banh-mi-sandwich-nguyen-cam-2/600/600', 0, 1),
  (26, 'https://picsum.photos/seed/banh-mi-que-gion-1/600/600', 1, 0),
  (26, 'https://picsum.photos/seed/banh-mi-que-gion-2/600/600', 0, 1),
  (27, 'https://picsum.photos/seed/banh-croissant-bo-phap-1/600/600', 1, 0),
  (27, 'https://picsum.photos/seed/banh-croissant-bo-phap-2/600/600', 0, 1),
  (28, 'https://picsum.photos/seed/banh-sinh-nhat-mini-socola-1/600/600', 1, 0),
  (28, 'https://picsum.photos/seed/banh-sinh-nhat-mini-socola-2/600/600', 0, 1);

INSERT INTO `reviews` (`product_id`, `user_id`, `rating`, `comment`, `status`) VALUES
  (1, 2, 5, 'Sản phẩm chất lượng, giá hợp lý, sẽ mua lại.', 1),
  (4, 3, 4, 'Đóng gói cẩn thận, giao hàng nhanh.', 1),
  (7, 2, 5, 'Rất ưng ý, đúng như mô tả.', 1),
  (10, 3, 3, 'Tạm ổn, giá hơi cao so với siêu thị.', 1),
  (13, 2, 4, 'Vị ngon, phù hợp cho cả gia đình.', 1),
  (16, 3, 5, 'Sản phẩm chất lượng, giá hợp lý, sẽ mua lại.', 1),
  (20, 2, 4, 'Đóng gói cẩn thận, giao hàng nhanh.', 1),
  (27, 3, 5, 'Rất ưng ý, đúng như mô tả.', 1);

INSERT INTO `vouchers` (`id`, `code`, `discount_type`, `discount_value`, `min_order`, `usage_limit`, `expiry_date`, `status`) VALUES
  (1, 'WELCOME10', 'percent', 10, 50000, 100, '2026-12-31', 1),
  (2, 'FREESHIP', 'fixed', 15000, 0, NULL, '2026-12-31', 1),
  (3, 'SALE50K', 'fixed', 50000, 300000, 50, '2026-12-31', 1);

INSERT INTO `banners` (`id`, `title`, `image_url`, `link_url`, `position`, `sort_order`, `status`) VALUES
  (1, 'Flash Sale cuối tuần', 'https://picsum.photos/seed/banner-flash/1200/400', '/public/category.php?deals=1', 'home_slider', 1, 1),
  (2, 'Ưu đãi đồ tươi sống', 'https://picsum.photos/seed/banner-fresh/1200/400', '/public/category.php?category=do-tuoi-song', 'home_slider', 2, 1);

INSERT INTO `notifications` (`user_id`, `title`, `message`, `is_read`) VALUES
  (2, 'Flash Sale bắt đầu!', 'Giảm đến 20% cho hàng trăm sản phẩm, chỉ trong hôm nay.', 0),
  (2, 'Đơn hàng #1023 đang giao', 'Đơn hàng của bạn đang trên đường giao, dự kiến đến trong 2 giờ tới.', 0),
  (2, 'Chào mừng bạn đến SwiftMart', 'Cảm ơn bạn đã đăng ký tài khoản. Nhập mã WELCOME10 để được giảm 10%.', 1),
  (3, 'Ưu đãi riêng cho bạn', 'Mã FREESHIP miễn phí vận chuyển cho đơn hàng tiếp theo.', 0);

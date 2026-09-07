-- ==============================================================================
-- SQL PATCH & SEED DATA - THÀNH VIÊN 3: GIỎ HÀNG, VOUCHER, ĐẶT HÀNG & LỊCH SỬ
-- Hỗ trợ đồ án Web Cửa Hàng Tiện Lợi (PHP thuần - MVC - PDO - MySQL)
-- ==============================================================================

USE `convenience_store`;

-- 1. ĐIỀU CHỈNH BẢNG ORDERS ĐỂ HỖ TRỢ KHÁCH VÃNG LAI VÀ ĐẦY ĐỦ CÁC CỘT YÊU CẦU
-- Cho phép user_id là NULL khi khách mua hàng không cần đăng nhập tài khoản
ALTER TABLE `orders` MODIFY `user_id` int(10) UNSIGNED NULL DEFAULT NULL;

-- Đảm bảo có các cột bổ sung (nếu chưa có) để tương thích linh hoạt
ALTER TABLE `orders` 
    ADD COLUMN IF NOT EXISTS `customer_name` varchar(100) NULL AFTER `order_code`,
    ADD COLUMN IF NOT EXISTS `customer_email` varchar(150) NULL AFTER `customer_name`,
    ADD COLUMN IF NOT EXISTS `customer_phone` varchar(20) NULL AFTER `customer_email`,
    ADD COLUMN IF NOT EXISTS `total_amount` decimal(12,2) NULL AFTER `shipping_address`,
    ADD COLUMN IF NOT EXISTS `discount_amount` decimal(12,2) NULL DEFAULT 0.00 AFTER `total_amount`,
    ADD COLUMN IF NOT EXISTS `final_amount` decimal(12,2) NULL AFTER `discount_amount`,
    ADD COLUMN IF NOT EXISTS `order_status` enum('pending','confirmed','shipping','completed','cancelled') DEFAULT 'pending' AFTER `payment_status`;

-- 2. DỮ LIỆU MẪU CHO BẢNG VOUCHERS (MÃ GIẢM GIÁ)
INSERT INTO `vouchers` (`id`, `code`, `description`, `discount_type`, `discount_value`, `min_order_value`, `max_discount`, `quantity`, `used_quantity`, `start_date`, `end_date`, `status`) VALUES
(1, 'CHAOBANMOI', 'Giảm 10% cho đơn từ 50.000đ', 'percent', 10.00, 50000.00, 30000.00, 100, 0, NOW() - INTERVAL 1 DAY, NOW() + INTERVAL 30 DAY, 'active'),
(2, 'GIAM20K', 'Giảm trực tiếp 20.000đ cho đơn từ 100.000đ', 'fixed', 20000.00, 100000.00, 20000.00, 50, 0, NOW() - INTERVAL 1 DAY, NOW() + INTERVAL 30 DAY, 'active'),
(3, 'FREESHIP', 'Giảm 15.000đ phí vận chuyển cho đơn từ 80.000đ', 'fixed', 15000.00, 80000.00, 15000.00, 200, 0, NOW() - INTERVAL 1 DAY, NOW() + INTERVAL 60 DAY, 'active'),
(4, 'HETHAN', 'Mã đã hết hạn sử dụng để test', 'fixed', 10000.00, 0.00, 10000.00, 10, 0, NOW() - INTERVAL 10 DAY, NOW() - INTERVAL 1 DAY, 'active'),
(5, 'HETLUOT', 'Mã đã hết lượt dùng để test', 'fixed', 50000.00, 0.00, 50000.00, 0, 10, NOW() - INTERVAL 1 DAY, NOW() + INTERVAL 30 DAY, 'active')
ON DUPLICATE KEY UPDATE 
    `discount_value` = VALUES(`discount_value`),
    `quantity` = VALUES(`quantity`),
    `status` = VALUES(`status`);

-- 3. DỮ LIỆU SẢN PHẨM MẪU (PRODUCTS) ĐỂ TEST GIỎ HÀNG & TỒN KHO
-- Đảm bảo có category_id = 1 và brand_id = 1 để không vi phạm khoá ngoại
INSERT IGNORE INTO `categories` (`id`, `name`, `description`, `status`) VALUES
(1, 'Thực phẩm tiện lợi', 'Mì gói, bánh mì, đồ ăn nhanh', 'active'),
(2, 'Đồ uống giải khát', 'Nước ngọt, trà, cà phê', 'active'),
(3, 'Bánh kẹo & Snack', 'Snack khoai tây, kẹo cao su, sô-cô-la', 'active');

INSERT IGNORE INTO `brands` (`id`, `name`, `description`, `status`) VALUES
(1, 'Acecook', 'Thương hiệu mì ăn liền hàng đầu', 'active'),
(2, 'Coca Cola', 'Thương hiệu nước giải khát toàn cầu', 'active'),
(3, 'Vinamilk', 'Thương hiệu sữa tươi Việt Nam', 'active'),
(4, 'Orion', 'Bánh kẹo và Snack', 'active');

INSERT INTO `products` (`id`, `category_id`, `brand_id`, `name`, `slug`, `description`, `price`, `sale_price`, `stock`, `status`) VALUES
(1, 1, 1, 'Mì Hảo Hảo Tôm Chua Cay 75g', 'mi-hao-hao-tom-chua-cay-75g', 'Mì gói quốc dân vị tôm chua cay đậm đà, sợi mì dai ngon.', 5000.00, 4500.00, 150, 'active'),
(2, 2, 2, 'Nước Ngọt Coca Cola Chai 390ml', 'nuoc-nghot-coca-cola-chai-390ml', 'Nước ngọt có ga sảng khoái mát lạnh, thích hợp uống giải nhiệt.', 10000.00, 9000.00, 80, 'active'),
(3, 3, 4, 'Snack Khoai Tây OStar Vị Tự Nhiên 50g', 'snack-khoai-tay-ostar-50g', 'Khoai tây tươi thái lát chiên giòn rụm thơm ngon.', 16000.00, 14000.00, 45, 'active'),
(4, 2, 3, 'Sữa Tươi Tiệt Trùng Vinamilk 100% Có Đường 220ml', 'sua-tuoi-vinamilk-co-duong-220ml', 'Bổ sung canxi và khoáng chất tốt cho sức khỏe hàng ngày.', 12000.00, 11000.00, 60, 'active'),
(5, 1, 1, 'Bánh Mì Sandwich Phô Mai Bơ Sữa', 'banh-mi-sandwich-pho-mai', 'Bánh mì sandwich mềm mịn kẹp kem bơ béo ngậy.', 22000.00, 20000.00, 20, 'active'),
(6, 2, 2, 'Trà Xanh Không Độ Chai 455ml', 'tra-xanh-khong-do-455ml', 'Chiết xuất từ lá trà xanh Thái Nguyên thanh mát giải nhiệt.', 11000.00, NULL, 5, 'active')
ON DUPLICATE KEY UPDATE 
    `price` = VALUES(`price`),
    `sale_price` = VALUES(`sale_price`),
    `stock` = VALUES(`stock`),
    `status` = VALUES(`status`);

-- 4. ẢNH SẢN PHẨM MẪU (PRODUCT_IMAGES)
INSERT INTO `product_images` (`product_id`, `image_url`, `is_primary`) VALUES
(1, 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=500&auto=format&fit=crop&q=60', 1),
(2, 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?w=500&auto=format&fit=crop&q=60', 1),
(3, 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?w=500&auto=format&fit=crop&q=60', 1),
(4, 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=500&auto=format&fit=crop&q=60', 1),
(5, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=500&auto=format&fit=crop&q=60', 1),
(6, 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&auto=format&fit=crop&q=60', 1)
ON DUPLICATE KEY UPDATE `is_primary` = VALUES(`is_primary`);

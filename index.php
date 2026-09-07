<?php
/**
 * Front Controller (Router điều hướng hệ thống)
 * Mô hình: MVC PHP thuần
 * Phụ trách: Module Thành Viên 3 - Client (Giỏ hàng, Voucher, Thanh toán, Đơn hàng)
 */

// Nạp cấu hình hệ thống & kết nối CSDL
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Nạp các Controller & Model cần thiết
require_once __DIR__ . '/controllers/CartController.php';
require_once __DIR__ . '/models/ProductModel.php';

// Khởi tạo Controller
$cartController = new CartController();

// Nhận hành động từ Query String (?act=...)
$action = isset($_GET['act']) ? trim($_GET['act']) : 'home';

switch ($action) {
    // -------------------------------------------------------------
    // 1. TRANG MUA SẮM SẢN PHẨM (HOME)
    // -------------------------------------------------------------
    case 'home':
    case 'products':
        $productModel = new ProductModel();
        try {
            $products = $productModel->getAllActive();
        } catch (Exception $e) {
            $products = [];
            setFlash('warning', 'Lưu ý kết nối CSDL: ' . $e->getMessage() . '. Hãy chắc chắn bạn đã bật MySQL trong XAMPP và import database.');
        }
        require __DIR__ . '/views/products/index.php';
        break;

    // -------------------------------------------------------------
    // 2. NGHIỆP VỤ GIỎ HÀNG (CART)
    // -------------------------------------------------------------
    case 'cart':
        // Xem giỏ hàng
        $cartController->index();
        break;

    case 'add-to-cart':
        // Thêm sản phẩm vào giỏ (kiểm tra tồn kho stock)
        $cartController->addToCart();
        break;

    case 'update-cart':
        // Cập nhật số lượng (<= 0 tự xóa, kiểm tra stock)
        $cartController->updateCart();
        break;

    case 'delete-cart':
        // Xóa một sản phẩm
        $cartController->deleteItem();
        break;

    case 'clear-cart':
        // Xóa sạch giỏ hàng
        $cartController->clearCart();
        break;

    // -------------------------------------------------------------
    // 3. NGHIỆP VỤ VOUCHER (MÃ GIẢM GIÁ)
    // -------------------------------------------------------------
    case 'apply-voucher':
        // Áp dụng voucher (kiểm tra hạn dùng, lượt dùng, giá trị đơn tối thiểu)
        $cartController->applyVoucher();
        break;

    case 'remove-voucher':
        // Gỡ bỏ voucher đang áp dụng
        $cartController->removeVoucher();
        break;

    // -------------------------------------------------------------
    // 4. NGHIỆP VỤ THANH TOÁN (CHECKOUT & ĐẶT HÀNG)
    // -------------------------------------------------------------
    case 'checkout':
        // Hiển thị giao diện thanh toán 2 cột
        $cartController->checkout();
        break;

    case 'process-checkout':
        // Xử lý submit đặt hàng với PDO Transaction
        $cartController->processCheckout();
        break;

    case 'order-success':
        // Trang hoàn tất đặt hàng (Thank you page)
        $cartController->orderSuccess();
        break;

    // -------------------------------------------------------------
    // 5. LỊCH SỬ ĐƠN HÀNG, THEO DÕI VÀ HỦY ĐƠN
    // -------------------------------------------------------------
    case 'orders':
        // Danh sách lịch sử đơn hàng
        $cartController->orderHistory();
        break;

    case 'order-detail':
        // Xem chi tiết đơn hàng & timeline trạng thái
        $cartController->orderDetail();
        break;

    case 'cancel-order':
        // Khách hàng hủy đơn pending (tự động hoàn kho & voucher)
        $cartController->cancelOrder();
        break;

    // -------------------------------------------------------------
    // MẶC ĐỊNH: 404 NOT FOUND
    // -------------------------------------------------------------
    default:
        http_response_code(404);
        echo "<div style='text-align:center; padding: 50px; font-family: sans-serif;'>
                <h2>404 - Trang không tồn tại!</h2>
                <p>Hành động yêu cầu không hợp lệ.</p>
                <a href='" . BASE_URL . "'>Về trang chủ</a>
              </div>";
        break;
}

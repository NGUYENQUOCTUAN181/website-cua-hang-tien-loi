<?php
/**
 * Cấu hình chung cho toàn bộ website
 * Module: Thành Viên 3 - Client: Giỏ hàng, Voucher & Thanh toán
 */

// Bắt đầu phiên làm việc Session nếu chưa khởi động
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Thiết lập múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Định nghĩa URL gốc của hệ thống (điều chỉnh phù hợp với thư mục máy chủ web của bạn)
define('BASE_URL', 'index.php');
define('SITE_NAME', 'Cửa Hàng Tiện Lợi');

/**
 * Hàm định dạng số tiền sang định dạng tiền tệ Việt Nam (VNĐ)
 * Ví dụ: 50000 -> 50.000 ₫
 *
 * @param float|int $amount
 * @return string
 */
function formatPrice($amount) {
    return number_format((float)$amount, 0, ',', '.') . ' ₫';
}

/**
 * Lưu thông báo nhanh vào Session (Flash Message)
 *
 * @param string $type success | danger | warning | info
 * @param string $message
 * @return void
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Lấy và xóa thông báo nhanh khỏi Session
 *
 * @return array|null
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Chuyển hướng trang kèm URL
 *
 * @param string $url
 * @return void
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}

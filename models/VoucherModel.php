<?php
/**
 * Model: Quản lý Khuyến Mãi / Mã Giảm Giá (VoucherModel)
 * Module: Thành Viên 3 - Client
 */

require_once __DIR__ . '/../config/database.php';

class VoucherModel {
    private $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    /**
     * Tìm kiếm và thẩm định một mã voucher dựa trên tổng tiền giỏ hàng
     *
     * @param string $code Mã voucher người dùng nhập
     * @param float $cartTotal Tổng tiền tạm tính của giỏ hàng
     * @return array [ 'success' => bool, 'message' => string, 'voucher' => array|null, 'discount_amount' => float ]
     */
    public function validateVoucher($code, $cartTotal) {
        $code = strtoupper(trim($code));

        if (empty($code)) {
            return [
                'success' => false,
                'message' => 'Vui lòng nhập mã giảm giá.',
                'voucher' => null,
                'discount_amount' => 0
            ];
        }

        // Truy vấn voucher từ CSDL: hỗ trợ cả 2 chuẩn tên cột (start_date/end_date hoặc expires_at, status hoặc is_active)
        $sql = "SELECT * FROM vouchers WHERE UPPER(code) = :code LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':code' => $code]);
        $voucher = $stmt->fetch();

        // 1. Kiểm tra tồn tại
        if (!$voucher) {
            return [
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại hoặc đã bị xóa.',
                'voucher' => null,
                'discount_amount' => 0
            ];
        }

        // 2. Kiểm tra trạng thái hoạt động (active / is_active)
        $isActive = true;
        if (isset($voucher['status']) && $voucher['status'] !== 'active') {
            $isActive = false;
        }
        if (isset($voucher['is_active']) && (int)$voucher['is_active'] !== 1) {
            $isActive = false;
        }

        if (!$isActive) {
            return [
                'success' => false,
                'message' => 'Mã giảm giá này hiện đang bị tạm khóa.',
                'voucher' => null,
                'discount_amount' => 0
            ];
        }

        // 3. Kiểm tra số lượng lượt dùng còn lại (quantity > 0)
        if ((int)$voucher['quantity'] <= 0) {
            return [
                'success' => false,
                'message' => 'Mã giảm giá này đã hết lượt sử dụng.',
                'voucher' => null,
                'discount_amount' => 0
            ];
        }

        // 4. Kiểm tra thời hạn hiệu lực (start_date, end_date hoặc expires_at)
        $now = date('Y-m-d H:i:s');
        if (!empty($voucher['start_date']) && $now < $voucher['start_date']) {
            return [
                'success' => false,
                'message' => 'Chương trình ưu đãi cho mã này chưa bắt đầu.',
                'voucher' => null,
                'discount_amount' => 0
            ];
        }

        $endDate = $voucher['end_date'] ?? $voucher['expires_at'] ?? null;
        if (!empty($endDate) && $now > $endDate) {
            return [
                'success' => false,
                'message' => 'Mã giảm giá đã hết hạn sử dụng.',
                'voucher' => null,
                'discount_amount' => 0
            ];
        }

        // 5. Kiểm tra giá trị đơn hàng tối thiểu (min_order_value)
        $minOrder = (float)($voucher['min_order_value'] ?? 0);
        if ($cartTotal < $minOrder) {
            return [
                'success' => false,
                'message' => 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($minOrder, 0, ',', '.') . ' ₫ để sử dụng mã này.',
                'voucher' => null,
                'discount_amount' => 0
            ];
        }

        // 6. Tính số tiền giảm giá hợp lệ
        $discountAmount = $this->calculateDiscount($voucher, $cartTotal);

        return [
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'voucher' => $voucher,
            'discount_amount' => $discountAmount
        ];
    }

    /**
     * Tính toán số tiền được giảm giá
     *
     * @param array $voucher
     * @param float $cartTotal
     * @return float
     */
    public function calculateDiscount($voucher, $cartTotal) {
        $discountValue = (float)$voucher['discount_value'];
        $discountType = strtolower($voucher['discount_type']);
        $calculatedDiscount = 0;

        if ($discountType === 'percent') {
            // Giảm theo phần trăm (ví dụ 10% của 200.000đ = 20.000đ)
            $calculatedDiscount = ($cartTotal * $discountValue) / 100;

            // Nếu có mức giảm tối đa (max_discount)
            if (!empty($voucher['max_discount']) && (float)$voucher['max_discount'] > 0) {
                $maxDiscount = (float)$voucher['max_discount'];
                if ($calculatedDiscount > $maxDiscount) {
                    $calculatedDiscount = $maxDiscount;
                }
            }
        } else {
            // Giảm cố định một số tiền (fixed)
            $calculatedDiscount = $discountValue;
        }

        // Số tiền giảm không được vượt quá tổng tiền của giỏ hàng
        if ($calculatedDiscount > $cartTotal) {
            $calculatedDiscount = $cartTotal;
        }

        return round($calculatedDiscount, 2);
    }
}

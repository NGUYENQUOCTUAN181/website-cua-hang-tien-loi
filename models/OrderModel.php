<?php
/**
 * Model: Quản lý Đơn Hàng & Xử Lý Đặt Hàng (OrderModel)
 * Module: Thành Viên 3 - Client
 * Áp dụng PDO Transaction để đảm bảo tính toàn vẹn dữ liệu và an toàn kho hàng.
 */

require_once __DIR__ . '/../config/database.php';

class OrderModel {
    private $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    /**
     * Tạo đơn hàng mới với PDO Transaction khép kín:
     * 1. Query lại DB kiểm tra giá và tồn kho từng món.
     * 2. Thêm mới bản ghi vào bảng orders.
     * 3. Duyệt giỏ hàng thêm từng dòng vào order_items (giá chốt tại thời điểm mua).
     * 4. Trừ số lượng tồn kho (stock) trong bảng products.
     * 5. Trừ số lượt sử dụng voucher (nếu có áp dụng).
     * 6. Commit nếu thành công; Rollback và ném Exception nếu có bất kỳ lỗi nào.
     *
     * @param array $customerData Thông tin khách hàng (name, phone, email, address, payment_method, note, user_id)
     * @param array $cartItems Danh sách sản phẩm trong giỏ [productId => item]
     * @param array|null $voucherInfo Thông tin voucher nếu có
     * @return array [ 'order_id' => int, 'order_code' => string ]
     * @throws Exception Khi xảy ra lỗi hoặc hết hàng
     */
    public function createOrderWithTransaction($customerData, $cartItems, $voucherInfo = null) {
        if (empty($cartItems)) {
            throw new Exception("Giỏ hàng đang trống, không thể tiến hành đặt hàng.");
        }

        // BẮT ĐẦU PDO TRANSACTION
        $this->db->beginTransaction();

        try {
            $subtotal = 0;
            $verifiedItems = [];

            // =====================================================================
            // BƯỚC 1: XÁC MINH LẠI GIÁ VÀ TỒN KHO TỪ BẢNG PRODUCTS (CHỐNG SỬA GIÁ F12)
            // =====================================================================
            // Sử dụng FOR UPDATE để khoá hàng đợi (Pessimistic Locking), tránh Race Condition
            $checkProductStmt = $this->db->prepare("
                SELECT id, name, price, sale_price, stock, status 
                FROM products 
                WHERE id = :id 
                FOR UPDATE
            ");

            foreach ($cartItems as $productId => $cartItem) {
                $checkProductStmt->execute([':id' => (int)$productId]);
                $dbProduct = $checkProductStmt->fetch();

                if (!$dbProduct || $dbProduct['status'] !== 'active') {
                    throw new Exception("Sản phẩm '{$cartItem['name']}' không còn tồn tại hoặc đã ngừng kinh doanh.");
                }

                $buyQty = (int)$cartItem['quantity'];
                $availableStock = (int)$dbProduct['stock'];

                // Kiểm tra tồn kho thời gian thực
                if ($buyQty > $availableStock) {
                    throw new Exception("Sản phẩm '{$dbProduct['name']}' chỉ còn {$availableStock} món trong kho (bạn đặt {$buyQty}). Vui lòng cập nhật lại giỏ hàng.");
                }

                // Tính giá bán thực tế từ Database (KHÔNG tin tưởng giá từ session hay form client)
                $effectivePrice = (float)$dbProduct['price'];
                if (!empty($dbProduct['sale_price']) && (float)$dbProduct['sale_price'] > 0 && (float)$dbProduct['sale_price'] < $effectivePrice) {
                    $effectivePrice = (float)$dbProduct['sale_price'];
                }

                $itemTotal = $effectivePrice * $buyQty;
                $subtotal += $itemTotal;

                $verifiedItems[] = [
                    'product_id'   => $dbProduct['id'],
                    'product_name' => $dbProduct['name'],
                    'price'        => $effectivePrice,
                    'quantity'     => $buyQty,
                    'total_price'  => $itemTotal
                ];
            }

            // =====================================================================
            // BƯỚC 2: TÍNH TOÁN LẠI TIỀN GIẢM VOUCHER (NẾU CÓ) TẠI THỜI ĐIỂM ĐẶT HÀNG
            // =====================================================================
            $discountAmount = 0;
            $voucherCode = null;

            if (!empty($voucherInfo) && !empty($voucherInfo['code'])) {
                $voucherStmt = $this->db->prepare("
                    SELECT * FROM vouchers 
                    WHERE UPPER(code) = :code 
                    FOR UPDATE
                ");
                $voucherStmt->execute([':code' => strtoupper($voucherInfo['code'])]);
                $dbVoucher = $voucherStmt->fetch();

                if ($dbVoucher) {
                    $now = date('Y-m-d H:i:s');
                    $endDate = $dbVoucher['end_date'] ?? $dbVoucher['expires_at'] ?? null;
                    $isActive = ($dbVoucher['status'] ?? 'active') === 'active' && ($dbVoucher['is_active'] ?? 1) == 1;

                    if ($isActive && (int)$dbVoucher['quantity'] > 0 && (empty($endDate) || $now <= $endDate)) {
                        if ($subtotal >= (float)($dbVoucher['min_order_value'] ?? 0)) {
                            $voucherCode = $dbVoucher['code'];
                            $val = (float)$dbVoucher['discount_value'];

                            if (strtolower($dbVoucher['discount_type']) === 'percent') {
                                $discountAmount = ($subtotal * $val) / 100;
                                if (!empty($dbVoucher['max_discount']) && (float)$dbVoucher['max_discount'] > 0) {
                                    $discountAmount = min($discountAmount, (float)$dbVoucher['max_discount']);
                                }
                            } else {
                                $discountAmount = $val;
                            }
                            $discountAmount = min($discountAmount, $subtotal);
                        }
                    }
                }
            }

            $finalAmount = max(0, $subtotal - $discountAmount);

            // =====================================================================
            // BƯỚC 3: TẠO BẢN GHI ĐƠN HÀNG TRONG BẢNG ORDERS
            // =====================================================================
            $orderCode = 'DH' . date('ymd') . strtoupper(substr(uniqid(), -5));
            $userId = !empty($customerData['user_id']) ? (int)$customerData['user_id'] : null;

            // Xác định cấu trúc cột của bảng orders trong DB
            // Đổ dữ liệu vào cả tên cột mới và tên cột cũ để tương thích 100%
            $sqlOrder = "
                INSERT INTO orders (
                    user_id, order_code, 
                    receiver_name, customer_name,
                    receiver_phone, customer_phone,
                    customer_email,
                    shipping_address,
                    subtotal, total_amount,
                    discount, discount_amount,
                    shipping_fee,
                    total, final_amount,
                    voucher_code, payment_method, payment_status,
                    status, order_status,
                    note, created_at
                ) VALUES (
                    :user_id, :order_code,
                    :receiver_name, :customer_name,
                    :receiver_phone, :customer_phone,
                    :customer_email,
                    :shipping_address,
                    :subtotal, :total_amount,
                    :discount, :discount_amount,
                    0.00,
                    :total, :final_amount,
                    :voucher_code, :payment_method, :payment_status,
                    'pending', 'pending',
                    :note, NOW()
                )
            ";

            // Chuẩn bị fallback nếu bảng trong MySQL chưa có một số cột
            // Chúng ta kiểm tra linh hoạt bằng cách thực hiện câu INSERT an toàn
            try {
                $orderStmt = $this->db->prepare($sqlOrder);
                $orderStmt->execute([
                    ':user_id'          => $userId,
                    ':order_code'       => $orderCode,
                    ':receiver_name'    => $customerData['name'],
                    ':customer_name'    => $customerData['name'],
                    ':receiver_phone'   => $customerData['phone'],
                    ':customer_phone'   => $customerData['phone'],
                    ':customer_email'   => $customerData['email'] ?? null,
                    ':shipping_address' => $customerData['address'],
                    ':subtotal'         => $subtotal,
                    ':total_amount'     => $subtotal,
                    ':discount'         => $discountAmount,
                    ':discount_amount'  => $discountAmount,
                    ':total'            => $finalAmount,
                    ':final_amount'     => $finalAmount,
                    ':voucher_code'     => $voucherCode,
                    ':payment_method'   => strtoupper($customerData['payment_method']),
                    ':payment_status'   => 'unpaid',
                    ':note'             => $customerData['note'] ?? ''
                ]);
            } catch (PDOException $e) {
                // Fallback nếu database dùng đúng schema gốc trong convenience_store.sql
                $sqlOrderFallback = "
                    INSERT INTO orders (
                        user_id, order_code, receiver_name, receiver_phone, 
                        shipping_address, subtotal, discount, shipping_fee, total, 
                        voucher_code, payment_method, payment_status, status, note, created_at
                    ) VALUES (
                        :user_id, :order_code, :receiver_name, :receiver_phone,
                        :shipping_address, :subtotal, :discount, 0.00, :total,
                        :voucher_code, :payment_method, 'unpaid', 'pending', :note, NOW()
                    )
                ";
                $orderStmt = $this->db->prepare($sqlOrderFallback);
                $orderStmt->execute([
                    ':user_id'          => $userId ?? 1, // Fallback nếu user_id có NOT NULL constraint
                    ':order_code'       => $orderCode,
                    ':receiver_name'    => $customerData['name'],
                    ':receiver_phone'   => $customerData['phone'],
                    ':shipping_address' => $customerData['address'],
                    ':subtotal'         => $subtotal,
                    ':discount'         => $discountAmount,
                    ':total'            => $finalAmount,
                    ':voucher_code'     => $voucherCode,
                    ':payment_method'   => strtoupper($customerData['payment_method']) === 'BANKING' ? 'BANK_TRANSFER' : 'COD',
                    ':note'             => $customerData['note'] ?? ''
                ]);
            }

            $orderId = (int)$this->db->lastInsertId();

            // =====================================================================
            // BƯỚC 4: LƯU TỪNG DÒNG SẢN PHẨM VÀO ORDER_ITEMS
            // =====================================================================
            $sqlItem = "
                INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal)
                VALUES (:order_id, :product_id, :product_name, :price, :quantity, :subtotal)
            ";
            $itemStmt = $this->db->prepare($sqlItem);

            // =====================================================================
            // BƯỚC 5: TRỪ TỒN KHO TRONG BẢNG PRODUCTS
            // =====================================================================
            $updateStockStmt = $this->db->prepare("
                UPDATE products 
                SET stock = stock - :qty 
                WHERE id = :id AND stock >= :qty
            ");

            foreach ($verifiedItems as $item) {
                // Chèn order_item
                $itemStmt->execute([
                    ':order_id'     => $orderId,
                    ':product_id'   => $item['product_id'],
                    ':product_name' => $item['product_name'],
                    ':price'        => $item['price'],
                    ':quantity'     => $item['quantity'],
                    ':subtotal'     => $item['total_price']
                ]);

                // Trừ tồn kho
                $updateStockStmt->execute([
                    ':qty' => $item['quantity'],
                    ':id'  => $item['product_id']
                ]);

                if ($updateStockStmt->rowCount() === 0) {
                    throw new Exception("Không thể cập nhật số lượng tồn kho cho món '{$item['product_name']}'. Vui lòng thử lại!");
                }
            }

            // =====================================================================
            // BƯỚC 6: TRỪ LƯỢT DÙNG VOUCHER (NẾU CÓ)
            // =====================================================================
            if ($voucherCode) {
                $updateVoucherStmt = $this->db->prepare("
                    UPDATE vouchers 
                    SET quantity = quantity - 1,
                        used_quantity = COALESCE(used_quantity, 0) + 1
                    WHERE code = :code AND quantity > 0
                ");
                $updateVoucherStmt->execute([':code' => $voucherCode]);
            }

            // =====================================================================
            // BƯỚC 7: COMMIT TOÀN BỘ TRANSACTION KHI TẤT CẢ ĐÃ THÀNH CÔNG
            // =====================================================================
            $this->db->commit();

            return [
                'order_id'   => $orderId,
                'order_code' => $orderCode,
                'subtotal'   => $subtotal,
                'discount'   => $discountAmount,
                'total'      => $finalAmount
            ];

        } catch (Exception $e) {
            // NẾU CÓ LỖI: ROLLBACK TOÀN BỘ THAO TÁC, KHÔNG GÂY SAI LỆCH DỮ LIỆU
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw new Exception("Đặt hàng thất bại: " . $e->getMessage());
        }
    }

    /**
     * Lấy thông tin đơn hàng theo ID
     *
     * @param int $orderId
     * @return array|false
     */
    public function getOrderById($orderId) {
        $sql = "SELECT * FROM orders WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => (int)$orderId]);
        return $stmt->fetch();
    }

    /**
     * Lấy thông tin đơn hàng theo Mã Đơn Hàng (order_code)
     *
     * @param string $orderCode
     * @return array|false
     */
    public function getOrderByCode($orderCode) {
        $sql = "SELECT * FROM orders WHERE order_code = :code LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':code' => trim($orderCode)]);
        return $stmt->fetch();
    }

    /**
     * Lấy danh sách sản phẩm chi tiết của một đơn hàng
     *
     * @param int $orderId
     * @return array
     */
    public function getOrderItems($orderId) {
        $sql = "
            SELECT oi.*, 
                   COALESCE(pi.image_url, 'https://placehold.co/100x100?text=Item') AS image
            FROM order_items oi
            LEFT JOIN product_images pi ON oi.product_id = pi.product_id AND pi.is_primary = 1
            WHERE oi.order_id = :order_id
            ORDER BY oi.id ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => (int)$orderId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy lịch sử danh sách đơn hàng của khách hàng (theo user_id hoặc số điện thoại / email)
     *
     * @param int|null $userId
     * @param string|null $phoneOrEmail
     * @return array
     */
    public function getOrdersHistory($userId = null, $phoneOrEmail = null) {
        $conditions = [];
        $params = [];

        if (!empty($userId)) {
            $conditions[] = "user_id = :user_id";
            $params[':user_id'] = (int)$userId;
        }

        if (!empty($phoneOrEmail)) {
            $phoneOrEmail = trim($phoneOrEmail);
            $conditions[] = "(receiver_phone = :keyword OR id = :keyword_id)";
            $params[':keyword'] = $phoneOrEmail;
            $params[':keyword_id'] = is_numeric($phoneOrEmail) ? (int)$phoneOrEmail : 0;
        }

        if (empty($conditions)) {
            // Nếu chưa đăng nhập và không tra cứu, lấy các đơn gần nhất trong phiên
            return [];
        }

        $sql = "SELECT * FROM orders WHERE " . implode(' OR ', $conditions) . " ORDER BY id DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // Fallback nếu có lỗi
            return [];
        }
    }

    /**
     * Khách hàng hủy đơn hàng (chỉ cho phép khi trạng thái đơn là 'pending')
     * Tự động hoàn lại số lượng tồn kho (stock) và hoàn lại lượt voucher
     *
     * @param int $orderId
     * @param string $cancelReason
     * @return bool
     * @throws Exception
     */
    public function cancelOrder($orderId, $cancelReason = 'Khách hàng yêu cầu hủy') {
        $this->db->beginTransaction();

        try {
            // Lấy thông tin đơn hàng và khóa hàng để kiểm tra
            $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id FOR UPDATE");
            $stmt->execute([':id' => (int)$orderId]);
            $order = $stmt->fetch();

            if (!$order) {
                throw new Exception("Đơn hàng không tồn tại.");
            }

            $currentStatus = $order['status'] ?? $order['order_status'] ?? '';
            if ($currentStatus !== 'pending') {
                throw new Exception("Đơn hàng đã được xác nhận hoặc đang vận chuyển, không thể tự hủy. Vui lòng liên hệ Hotline cửa hàng!");
            }

            // 1. Cập nhật trạng thái đơn thành 'cancelled' (có fallback linh hoạt)
            try {
                $updateSql = "
                    UPDATE orders 
                    SET status = 'cancelled', 
                        order_status = 'cancelled',
                        note = CONCAT(COALESCE(note, ''), ' | Lý do hủy: ', :reason)
                    WHERE id = :id
                ";
                $updateStmt = $this->db->prepare($updateSql);
                $updateStmt->execute([
                    ':reason' => $cancelReason,
                    ':id'     => (int)$orderId
                ]);
            } catch (PDOException $ex) {
                $updateSql = "
                    UPDATE orders 
                    SET status = 'cancelled',
                        note = CONCAT(COALESCE(note, ''), ' | Lý do hủy: ', :reason)
                    WHERE id = :id
                ";
                $updateStmt = $this->db->prepare($updateSql);
                $updateStmt->execute([
                    ':reason' => $cancelReason,
                    ':id'     => (int)$orderId
                ]);
            }

            // 2. Hoàn lại số lượng tồn kho cho các món trong đơn hàng
            $items = $this->getOrderItems($orderId);
            $restoreStockStmt = $this->db->prepare("UPDATE products SET stock = stock + :qty WHERE id = :id");

            foreach ($items as $item) {
                $restoreStockStmt->execute([
                    ':qty' => (int)$item['quantity'],
                    ':id'  => (int)$item['product_id']
                ]);
            }

            // 3. Hoàn lại lượt dùng voucher (nếu đơn có dùng voucher)
            if (!empty($order['voucher_code'])) {
                $restoreVoucherStmt = $this->db->prepare("
                    UPDATE vouchers 
                    SET quantity = quantity + 1,
                        used_quantity = GREATEST(0, COALESCE(used_quantity, 1) - 1)
                    WHERE code = :code
                ");
                $restoreVoucherStmt->execute([':code' => $order['voucher_code']]);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw new Exception("Hủy đơn thất bại: " . $e->getMessage());
        }
    }
}

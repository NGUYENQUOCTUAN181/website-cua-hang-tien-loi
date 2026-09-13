<?php

require_once __DIR__ . '/Voucher.php';

class Order
{
    private PDO $pdo;
    private Voucher $voucherModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->voucherModel = new Voucher($pdo);
    }

    /**
     * Sinh mã đơn hàng duy nhất
     */
    private function generateOrderCode(): string
    {
        do {

            $orderCode =
                'CS-' .
                date('Ymd-His') .
                '-' .
                strtoupper(
                    bin2hex(random_bytes(3))
                );

            $sql = "SELECT id
                    FROM orders
                    WHERE order_code = :order_code
                    LIMIT 1";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'order_code' => $orderCode
            ]);

            $exists = $stmt->fetchColumn();

        } while ($exists);

        return $orderCode;
    }

    /**
     * Tạo đơn hàng từ giỏ hàng
     */
    public function createFromCart(
        int $userId,
        string $receiverName,
        string $receiverPhone,
        string $shippingAddress,
        string $paymentMethod,
        string $note = '',
        ?string $voucherCode = null
    ): array {

        if (!in_array(
            $paymentMethod,
            ['COD', 'BANK_TRANSFER'],
            true
        )) {
            throw new InvalidArgumentException(
                'Phương thức thanh toán không hợp lệ.'
            );
        }

        $this->pdo->beginTransaction();

        try {

            /*
             * ==================================================
             * 1. LẤY GIỎ HÀNG
             * ==================================================
             */

            $sql = "SELECT id
                    FROM carts
                    WHERE user_id = :user_id
                    LIMIT 1
                    FOR UPDATE";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'user_id' => $userId
            ]);

            $cartId = $stmt->fetchColumn();

            if (!$cartId) {
                throw new RuntimeException(
                    'Giỏ hàng không tồn tại.'
                );
            }


            /*
             * ==================================================
             * 2. LẤY ITEMS
             * ==================================================
             */

            $sql = "SELECT
                        ci.id AS cart_item_id,
                        ci.product_id,
                        ci.quantity,

                        p.name,
                        p.price,
                        p.sale_price,
                        p.stock,
                        p.status

                    FROM cart_items ci

                    INNER JOIN products p
                        ON p.id = ci.product_id

                    WHERE ci.cart_id = :cart_id

                    FOR UPDATE";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'cart_id' => $cartId
            ]);

            $items = $stmt->fetchAll();

            if (empty($items)) {
                throw new RuntimeException(
                    'Giỏ hàng đang trống.'
                );
            }


            /*
             * ==================================================
             * 3. KIỂM TRA STOCK + TÍNH SUBTOTAL
             * ==================================================
             */

            $subtotal = 0;

            foreach ($items as $item) {

                if (
                    $item['status'] !== 'active'
                ) {
                    throw new RuntimeException(
                        'Sản phẩm "' .
                        $item['name'] .
                        '" không còn kinh doanh.'
                    );
                }

                $quantity =
                    (int) $item['quantity'];

                $stock =
                    (int) $item['stock'];

                if ($quantity > $stock) {
                    throw new RuntimeException(
                        'Sản phẩm "' .
                        $item['name'] .
                        '" không đủ tồn kho.'
                    );
                }

                $unitPrice =
                    $item['sale_price'] !== null
                    ? (float) $item['sale_price']
                    : (float) $item['price'];

                $subtotal +=
                    $unitPrice * $quantity;
            }


            /*
             * ==================================================
             * 4. XỬ LÝ VOUCHER
             * ==================================================
             */

            $discount = 0;
            $voucherDbCode = null;

            if (
                $voucherCode !== null &&
                trim($voucherCode) !== ''
            ) {

                $voucherCode =
                    strtoupper(
                        trim($voucherCode)
                    );

                $voucher =
                    $this->voucherModel
                        ->findValidForUpdate(
                            $voucherCode
                        );

                if (!$voucher) {
                    throw new RuntimeException(
                        'Voucher không tồn tại, đã hết lượt hoặc đã hết hạn.'
                    );
                }

                $discount =
                    $this->voucherModel
                        ->calculateDiscount(
                            $voucher,
                            $subtotal
                        );

                $voucherDbCode =
                    $voucher['code'];

                /*
                 * Tăng số lần sử dụng
                 */
                $sql = "UPDATE vouchers
                        SET used_quantity =
                            used_quantity + 1
                        WHERE id = :id
                        AND used_quantity < quantity";

                $stmt = $this->pdo->prepare($sql);

                $stmt->execute([
                    'id' => (int) $voucher['id']
                ]);

                if ($stmt->rowCount() !== 1) {
                    throw new RuntimeException(
                        'Voucher vừa được sử dụng hết lượt.'
                    );
                }
            }


            /*
             * ==================================================
             * 5. PHÍ VẬN CHUYỂN
             * ==================================================
             */

            $shippingFee = 0;


            /*
             * ==================================================
             * 6. TOTAL
             * ==================================================
             */

            $total =
                $subtotal
                - $discount
                + $shippingFee;


            /*
             * ==================================================
             * 7. TẠO ORDER
             * ==================================================
             */

            $orderCode =
                $this->generateOrderCode();

            $sql = "INSERT INTO orders (
                        user_id,
                        order_code,
                        receiver_name,
                        receiver_phone,
                        shipping_address,
                        subtotal,
                        discount,
                        shipping_fee,
                        total,
                        voucher_code,
                        payment_method,
                        payment_status,
                        status,
                        note,
                        created_at,
                        updated_at
                    ) VALUES (
                        :user_id,
                        :order_code,
                        :receiver_name,
                        :receiver_phone,
                        :shipping_address,
                        :subtotal,
                        :discount,
                        :shipping_fee,
                        :total,
                        :voucher_code,
                        :payment_method,
                        'unpaid',
                        'pending',
                        :note,
                        NOW(),
                        NOW()
                    )";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'user_id' =>
                    $userId,

                'order_code' =>
                    $orderCode,

                'receiver_name' =>
                    $receiverName,

                'receiver_phone' =>
                    $receiverPhone,

                'shipping_address' =>
                    $shippingAddress,

                'subtotal' =>
                    $subtotal,

                'discount' =>
                    $discount,

                'shipping_fee' =>
                    $shippingFee,

                'total' =>
                    $total,

                'voucher_code' =>
                    $voucherDbCode,

                'payment_method' =>
                    $paymentMethod,

                'note' =>
                    $note
            ]);

            $orderId =
                (int) $this->pdo->lastInsertId();


            /*
             * ==================================================
             * 8. ORDER ITEMS + TRỪ STOCK
             * ==================================================
             */

            foreach ($items as $item) {

                $productId =
                    (int) $item['product_id'];

                $quantity =
                    (int) $item['quantity'];

                $unitPrice =
                    $item['sale_price'] !== null
                    ? (float) $item['sale_price']
                    : (float) $item['price'];

                $itemSubtotal =
                    $unitPrice * $quantity;


                /*
                 * ORDER ITEM
                 */

                $sql = "INSERT INTO order_items (
                            order_id,
                            product_id,
                            product_name,
                            price,
                            quantity,
                            subtotal
                        ) VALUES (
                            :order_id,
                            :product_id,
                            :product_name,
                            :price,
                            :quantity,
                            :subtotal
                        )";

                $stmt =
                    $this->pdo->prepare($sql);

                $stmt->execute([
                    'order_id' =>
                        $orderId,

                    'product_id' =>
                        $productId,

                    'product_name' =>
                        $item['name'],

                    'price' =>
                        $unitPrice,

                    'quantity' =>
                        $quantity,

                    'subtotal' =>
                        $itemSubtotal
                ]);


                /*
                 * TRỪ STOCK
                 */

                $sql = "UPDATE products
                        SET
                            stock =
                                stock - :quantity,
                            updated_at =
                                NOW()
                        WHERE id = :product_id
                        AND stock >= :quantity";

                $stmt =
                    $this->pdo->prepare($sql);

                $stmt->execute([
                    'quantity' =>
                        $quantity,

                    'product_id' =>
                        $productId
                ]);

                if ($stmt->rowCount() !== 1) {
                    throw new RuntimeException(
                        'Không thể cập nhật tồn kho cho sản phẩm "' .
                        $item['name'] .
                        '".'
                    );
                }
            }


            /*
             * ==================================================
             * 9. XÓA CART
             * ==================================================
             */

            $sql = "DELETE FROM cart_items
                    WHERE cart_id = :cart_id";

            $stmt =
                $this->pdo->prepare($sql);

            $stmt->execute([
                'cart_id' =>
                    $cartId
            ]);


            /*
             * ==================================================
             * 10. COMMIT
             * ==================================================
             */

            $this->pdo->commit();


            return [
                'success' =>
                    true,

                'order_id' =>
                    $orderId,

                'order_code' =>
                    $orderCode,

                'subtotal' =>
                    $subtotal,

                'discount' =>
                    $discount,

                'shipping_fee' =>
                    $shippingFee,

                'total' =>
                    $total,

                'voucher_code' =>
                    $voucherDbCode
            ];

        } catch (Throwable $e) {

            if (
                $this->pdo->inTransaction()
            ) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }


    /*
     * ========================================================
     * CÁC HÀM ORDER HISTORY CŨ
     * ========================================================
     */

    public function getByUser(
        int $userId
    ): array {

        $sql = "SELECT
                    id,
                    order_code,
                    receiver_name,
                    receiver_phone,
                    shipping_address,
                    subtotal,
                    discount,
                    shipping_fee,
                    total,
                    voucher_code,
                    payment_method,
                    payment_status,
                    status,
                    note,
                    created_at,
                    updated_at
                FROM orders
                WHERE user_id = :user_id
                ORDER BY created_at DESC";

        $stmt =
            $this->pdo->prepare($sql);

        $stmt->execute([
            'user_id' =>
                $userId
        ]);

        return $stmt->fetchAll();
    }


    public function findByIdForUser(
        int $orderId,
        int $userId
    ): ?array {

        $sql = "SELECT
                    id,
                    user_id,
                    order_code,
                    receiver_name,
                    receiver_phone,
                    shipping_address,
                    subtotal,
                    discount,
                    shipping_fee,
                    total,
                    voucher_code,
                    payment_method,
                    payment_status,
                    status,
                    note,
                    created_at,
                    updated_at
                FROM orders
                WHERE id = :order_id
                AND user_id = :user_id
                LIMIT 1";

        $stmt =
            $this->pdo->prepare($sql);

        $stmt->execute([
            'order_id' =>
                $orderId,

            'user_id' =>
                $userId
        ]);

        $order =
            $stmt->fetch();

        return $order ?: null;
    }


    public function getItems(
        int $orderId
    ): array {

        $sql = "SELECT
                    oi.id,
                    oi.order_id,
                    oi.product_id,
                    oi.product_name,
                    oi.price,
                    oi.quantity,
                    oi.subtotal,
                    (
                        SELECT pi.image_url
                        FROM product_images pi
                        WHERE pi.product_id = oi.product_id
                        ORDER BY
                            pi.is_primary DESC,
                            pi.id ASC
                        LIMIT 1
                    ) AS image_url
                FROM order_items oi
                WHERE oi.order_id = :order_id
                ORDER BY oi.id ASC";

        $stmt =
            $this->pdo->prepare($sql);

        $stmt->execute([
            'order_id' =>
                $orderId
        ]);

        return $stmt->fetchAll();
    }


    public function cancelOrder(
        int $orderId,
        int $userId
    ): bool {

        $this->pdo->beginTransaction();

        try {

            $sql = "SELECT
                        id,
                        status
                    FROM orders
                    WHERE id = :order_id
                    AND user_id = :user_id
                    LIMIT 1
                    FOR UPDATE";

            $stmt =
                $this->pdo->prepare($sql);

            $stmt->execute([
                'order_id' =>
                    $orderId,

                'user_id' =>
                    $userId
            ]);

            $order =
                $stmt->fetch();

            if (!$order) {
                throw new RuntimeException(
                    'Không tìm thấy đơn hàng.'
                );
            }

            if (
                $order['status'] !== 'pending'
            ) {
                throw new RuntimeException(
                    'Chỉ có thể hủy đơn hàng đang chờ xác nhận.'
                );
            }

            $sql = "SELECT
                        product_id,
                        quantity
                    FROM order_items
                    WHERE order_id = :order_id
                    FOR UPDATE";

            $stmt =
                $this->pdo->prepare($sql);

            $stmt->execute([
                'order_id' =>
                    $orderId
            ]);

            $items =
                $stmt->fetchAll();

            foreach ($items as $item) {

                $sql = "UPDATE products
                        SET
                            stock =
                                stock + :quantity,
                            updated_at =
                                NOW()
                        WHERE id = :product_id";

                $stmt =
                    $this->pdo->prepare($sql);

                $stmt->execute([
                    'quantity' =>
                        (int) $item['quantity'],

                    'product_id' =>
                        (int) $item['product_id']
                ]);
            }

            $sql = "UPDATE orders
                    SET
                        status = 'cancelled',
                        updated_at = NOW()
                    WHERE id = :order_id
                    AND user_id = :user_id
                    AND status = 'pending'";

            $stmt =
                $this->pdo->prepare($sql);

            $stmt->execute([
                'order_id' =>
                    $orderId,

                'user_id' =>
                    $userId
            ]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException(
                    'Không thể hủy đơn hàng.'
                );
            }

            /*
             * Nếu đơn có voucher,
             * hoàn lại một lượt sử dụng.
             */
            $sql = "SELECT voucher_code
                    FROM orders
                    WHERE id = :order_id
                    LIMIT 1";

            $stmt =
                $this->pdo->prepare($sql);

            $stmt->execute([
                'order_id' =>
                    $orderId
            ]);

            $voucherCode =
                $stmt->fetchColumn();

            if ($voucherCode) {

                $sql = "UPDATE vouchers
                        SET used_quantity =
                            GREATEST(
                                used_quantity - 1,
                                0
                            )
                        WHERE code = :code";

                $stmt =
                    $this->pdo->prepare($sql);

                $stmt->execute([
                    'code' =>
                        $voucherCode
                ]);
            }

            $this->pdo->commit();

            return true;

        } catch (Throwable $e) {

            if (
                $this->pdo->inTransaction()
            ) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}
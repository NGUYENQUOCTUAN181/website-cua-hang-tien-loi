<?php

class AdminOrder
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy danh sách đơn hàng
     */
    public function getAll(
        string $keyword = '',
        string $status = ''
    ): array {
        $sql = "SELECT
                    o.*,
                    u.name AS user_name,
                    u.email AS user_email
                FROM orders o
                INNER JOIN users u
                    ON u.id = o.user_id
                WHERE 1 = 1";

        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (
                        o.order_code LIKE :keyword
                        OR o.receiver_name LIKE :keyword
                        OR o.receiver_phone LIKE :keyword
                        OR u.name LIKE :keyword
                        OR u.email LIKE :keyword
                    )";

            $params['keyword'] = '%' . $keyword . '%';
        }

        if (
            $status !== '' &&
            in_array(
                $status,
                [
                    'pending',
                    'confirmed',
                    'shipping',
                    'completed',
                    'cancelled'
                ],
                true
            )
        ) {
            $sql .= " AND o.status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY o.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Lấy chi tiết đơn
     */
    public function findById(int $orderId): ?array
    {
        $sql = "SELECT
                    o.*,
                    u.name AS user_name,
                    u.email AS user_email
                FROM orders o
                INNER JOIN users u
                    ON u.id = o.user_id
                WHERE o.id = :order_id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'order_id' => $orderId
        ]);

        $order = $stmt->fetch();

        return $order ?: null;
    }

    /**
     * Lấy sản phẩm trong đơn
     */
    public function getItems(int $orderId): array
    {
        $sql = "SELECT
                    oi.*
                FROM order_items oi
                WHERE oi.order_id = :order_id
                ORDER BY oi.id ASC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'order_id' => $orderId
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Cập nhật trạng thái đơn
     */
    public function updateStatus(
        int $orderId,
        string $newStatus
    ): bool {
        $allowedStatuses = [
            'pending',
            'confirmed',
            'shipping',
            'completed',
            'cancelled'
        ];

        if (!in_array(
            $newStatus,
            $allowedStatuses,
            true
        )) {
            throw new InvalidArgumentException(
                'Trạng thái không hợp lệ.'
            );
        }

        $this->pdo->beginTransaction();

        try {

            $sql = "SELECT
                        id,
                        status
                    FROM orders
                    WHERE id = :order_id
                    LIMIT 1
                    FOR UPDATE";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'order_id' => $orderId
            ]);

            $order = $stmt->fetch();

            if (!$order) {
                throw new RuntimeException(
                    'Không tìm thấy đơn hàng.'
                );
            }

            $currentStatus = $order['status'];

            /*
             * Không cho thay đổi đơn đã completed
             * hoặc đã cancelled.
             */
            if (
                in_array(
                    $currentStatus,
                    ['completed', 'cancelled'],
                    true
                )
            ) {
                throw new RuntimeException(
                    'Đơn hàng đã kết thúc, không thể thay đổi trạng thái.'
                );
            }

            /*
             * Không cho chuyển cancelled sang trạng thái khác.
             */
            if ($newStatus === 'cancelled') {

                /*
                 * Hoàn stock khi Admin hủy đơn.
                 */
                $sql = "SELECT
                            product_id,
                            quantity
                        FROM order_items
                        WHERE order_id = :order_id
                        FOR UPDATE";

                $stmt = $this->pdo->prepare($sql);

                $stmt->execute([
                    'order_id' => $orderId
                ]);

                $items = $stmt->fetchAll();

                foreach ($items as $item) {

                    $sql = "UPDATE products
                            SET stock = stock + :quantity,
                                updated_at = NOW()
                            WHERE id = :product_id";

                    $stmt = $this->pdo->prepare($sql);

                    $stmt->execute([
                        'quantity' => (int) $item['quantity'],
                        'product_id' => (int) $item['product_id']
                    ]);
                }
            }

            /*
             * Cập nhật trạng thái.
             */
            $sql = "UPDATE orders
                    SET status = :status,
                        updated_at = NOW()
                    WHERE id = :order_id";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'status' => $newStatus,
                'order_id' => $orderId
            ]);

            $this->pdo->commit();

            return true;

        } catch (Throwable $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}
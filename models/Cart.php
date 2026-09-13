<?php

class Cart
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy cart của user, nếu chưa có thì tạo mới
     */
    public function getOrCreateCart(int $userId): int
    {
        $sql = "SELECT id
                FROM carts
                WHERE user_id = :user_id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId
        ]);

        $cartId = $stmt->fetchColumn();

        if ($cartId) {
            return (int) $cartId;
        }

        $sql = "INSERT INTO carts (
                    user_id,
                    created_at,
                    updated_at
                ) VALUES (
                    :user_id,
                    NOW(),
                    NOW()
                )";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'user_id' => $userId
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Thêm sản phẩm vào giỏ
     * Nếu đã tồn tại thì tăng quantity
     */
    public function addItem(
        int $userId,
        int $productId,
        int $quantity
    ): bool {
        if ($quantity < 1) {
            return false;
        }

        $productSql = "SELECT id, stock, status
                       FROM products
                       WHERE id = :id
                       LIMIT 1";

        $stmt = $this->pdo->prepare($productSql);
        $stmt->execute([
            'id' => $productId
        ]);

        $product = $stmt->fetch();

        if (!$product || $product['status'] !== 'active') {
            return false;
        }

        $stock = (int) $product['stock'];

        if ($stock < $quantity) {
            return false;
        }

        $cartId = $this->getOrCreateCart($userId);

        $itemSql = "SELECT id, quantity
                    FROM cart_items
                    WHERE cart_id = :cart_id
                    AND product_id = :product_id
                    LIMIT 1";

        $stmt = $this->pdo->prepare($itemSql);

        $stmt->execute([
            'cart_id' => $cartId,
            'product_id' => $productId
        ]);

        $item = $stmt->fetch();

        if ($item) {
            $newQuantity = (int) $item['quantity'] + $quantity;

            if ($newQuantity > $stock) {
                return false;
            }

            $sql = "UPDATE cart_items
                    SET quantity = :quantity,
                        updated_at = NOW()
                    WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([
                'quantity' => $newQuantity,
                'id' => $item['id']
            ]);
        }

        $sql = "INSERT INTO cart_items (
                    cart_id,
                    product_id,
                    quantity,
                    created_at,
                    updated_at
                ) VALUES (
                    :cart_id,
                    :product_id,
                    :quantity,
                    NOW(),
                    NOW()
                )";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'cart_id' => $cartId,
            'product_id' => $productId,
            'quantity' => $quantity
        ]);
    }

    /**
     * Lấy toàn bộ sản phẩm trong giỏ
     */
    public function getItems(int $userId): array
    {
        $sql = "SELECT
                    ci.id AS cart_item_id,
                    ci.cart_id,
                    ci.product_id,
                    ci.quantity,

                    p.name,
                    p.price,
                    p.sale_price,
                    p.stock,
                    p.status,

                    c.name AS category_name,
                    b.name AS brand_name,

                    (
                        SELECT pi.image_url
                        FROM product_images pi
                        WHERE pi.product_id = p.id
                        ORDER BY pi.is_primary DESC, pi.id ASC
                        LIMIT 1
                    ) AS image_url

                FROM carts cart

                INNER JOIN cart_items ci
                    ON ci.cart_id = cart.id

                INNER JOIN products p
                    ON p.id = ci.product_id

                LEFT JOIN categories c
                    ON c.id = p.category_id

                LEFT JOIN brands b
                    ON b.id = p.brand_id

                WHERE cart.user_id = :user_id
                ORDER BY ci.id DESC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'user_id' => $userId
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Cập nhật số lượng
     */
    public function updateItem(
        int $userId,
        int $cartItemId,
        int $quantity
    ): bool {
        if ($quantity < 1) {
            return false;
        }

        $sql = "SELECT
                    ci.id,
                    p.stock
                FROM cart_items ci
                INNER JOIN carts c
                    ON c.id = ci.cart_id
                INNER JOIN products p
                    ON p.id = ci.product_id
                WHERE ci.id = :cart_item_id
                AND c.user_id = :user_id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'cart_item_id' => $cartItemId,
            'user_id' => $userId
        ]);

        $item = $stmt->fetch();

        if (!$item) {
            return false;
        }

        if ($quantity > (int) $item['stock']) {
            return false;
        }

        $sql = "UPDATE cart_items ci
                INNER JOIN carts c
                    ON c.id = ci.cart_id
                SET ci.quantity = :quantity,
                    ci.updated_at = NOW()
                WHERE ci.id = :cart_item_id
                AND c.user_id = :user_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'quantity' => $quantity,
            'cart_item_id' => $cartItemId,
            'user_id' => $userId
        ]);
    }

    /**
     * Xóa sản phẩm khỏi giỏ
     */
    public function removeItem(
        int $userId,
        int $cartItemId
    ): bool {
        $sql = "DELETE ci
                FROM cart_items ci
                INNER JOIN carts c
                    ON c.id = ci.cart_id
                WHERE ci.id = :cart_item_id
                AND c.user_id = :user_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'cart_item_id' => $cartItemId,
            'user_id' => $userId
        ]);
    }

    /**
     * Đếm tổng số lượng sản phẩm trong giỏ
     */
    public function getItemCount(int $userId): int
    {
        $sql = "SELECT COALESCE(SUM(ci.quantity), 0)
                FROM carts c
                INNER JOIN cart_items ci
                    ON ci.cart_id = c.id
                WHERE c.user_id = :user_id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'user_id' => $userId
        ]);

        return (int) $stmt->fetchColumn();
    }
}
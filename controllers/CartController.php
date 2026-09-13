<?php

require_once __DIR__ . '/../models/Cart.php';

class CartController
{
    private Cart $cartModel;

    public function __construct(PDO $pdo)
    {
        $this->cartModel = new Cart($pdo);
    }

    /**
     * Thêm sản phẩm vào giỏ
     */
    public function add(
        int $userId,
        int $productId,
        int $quantity
    ): bool {
        return $this->cartModel->addItem(
            $userId,
            $productId,
            $quantity
        );
    }

    /**
     * Lấy sản phẩm trong giỏ
     */
    public function getItems(int $userId): array
    {
        return $this->cartModel->getItems($userId);
    }

    /**
     * Cập nhật số lượng
     */
    public function update(
        int $userId,
        int $cartItemId,
        int $quantity
    ): bool {
        return $this->cartModel->updateItem(
            $userId,
            $cartItemId,
            $quantity
        );
    }

    /**
     * Xóa sản phẩm
     */
    public function remove(
        int $userId,
        int $cartItemId
    ): bool {
        return $this->cartModel->removeItem(
            $userId,
            $cartItemId
        );
    }

    /**
     * Đếm tổng số lượng sản phẩm
     */
    public function count(int $userId): int
    {
        return $this->cartModel->getItemCount($userId);
    }
}
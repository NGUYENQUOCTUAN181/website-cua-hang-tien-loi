<?php

require_once __DIR__ . '/../models/Order.php';

class OrderController
{
    private Order $orderModel;

    public function __construct(PDO $pdo)
    {
        $this->orderModel = new Order($pdo);
    }

    public function createFromCart(
        int $userId,
        string $receiverName,
        string $receiverPhone,
        string $shippingAddress,
        string $paymentMethod,
        string $note = '',
        ?string $voucherCode = null
    ): array {
        return $this->orderModel->createFromCart(
            $userId,
            $receiverName,
            $receiverPhone,
            $shippingAddress,
            $paymentMethod,
            $note,
            $voucherCode
        );
    }

    public function getByUser(int $userId): array
    {
        return $this->orderModel->getByUser($userId);
    }

    public function findByIdForUser(
        int $orderId,
        int $userId
    ): ?array {
        return $this->orderModel->findByIdForUser(
            $orderId,
            $userId
        );
    }

    public function getItems(int $orderId): array
    {
        return $this->orderModel->getItems($orderId);
    }
    public function cancel(
    int $orderId,
    int $userId
): bool {
    return $this->orderModel->cancelOrder(
        $orderId,
        $userId
    );
}
}
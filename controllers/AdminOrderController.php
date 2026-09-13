<?php

require_once __DIR__ . '/../models/AdminOrder.php';

class AdminOrderController
{
    private AdminOrder $orderModel;

    public function __construct(PDO $pdo)
    {
        $this->orderModel = new AdminOrder($pdo);
    }

    public function getAll(
        string $keyword = '',
        string $status = ''
    ): array {
        return $this->orderModel->getAll(
            $keyword,
            $status
        );
    }

    public function findById(int $orderId): ?array
    {
        return $this->orderModel->findById($orderId);
    }

    public function getItems(int $orderId): array
    {
        return $this->orderModel->getItems($orderId);
    }

    public function updateStatus(
        int $orderId,
        string $status
    ): bool {
        return $this->orderModel->updateStatus(
            $orderId,
            $status
        );
    }
}
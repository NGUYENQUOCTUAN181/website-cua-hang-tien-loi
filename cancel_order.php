<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/OrderController.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user']['id'];

$orderId = (int) ($_POST['order_id'] ?? 0);

if ($orderId <= 0) {
    header('Location: orders.php?error=invalid_order');
    exit;
}

try {

    $orderController = new OrderController($pdo);

    $orderController->cancel(
        $orderId,
        $userId
    );

    header(
        'Location: order_detail.php?id=' .
        $orderId .
        '&cancelled=1'
    );

    exit;

} catch (Throwable $e) {

    header(
        'Location: order_detail.php?id=' .
        $orderId .
        '&error=' .
        urlencode($e->getMessage())
    );

    exit;
}
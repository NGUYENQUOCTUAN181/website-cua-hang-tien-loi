<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';
require_once __DIR__ . '/../controllers/AdminOrderController.php';

$action = $_POST['action'] ?? '';

if ($action !== 'update_status') {
    header('Location: orders.php');
    exit;
}

$orderId = (int) ($_POST['order_id'] ?? 0);
$status = $_POST['status'] ?? '';

if ($orderId <= 0) {
    die('ID đơn hàng không hợp lệ.');
}

try {

    $orderController = new AdminOrderController($pdo);

    $orderController->updateStatus(
        $orderId,
        $status
    );

    header(
        'Location: order_detail.php?id=' .
        $orderId .
        '&success=1'
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
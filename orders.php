<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/OrderController.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user']['id'];

$orderController = new OrderController($pdo);

$orders = $orderController->getByUser($userId);

function formatOrderPrice(float $price): string
{
    return number_format($price, 0, ',', '.') . '₫';
}

function getStatusText(string $status): string
{
    return match ($status) {
        'pending'   => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'shipping'  => 'Đang giao hàng',
        'completed' => 'Đã giao hàng',
        default     => $status
    };
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Đơn hàng của tôi - <?= SITE_NAME ?></title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #222;
        }

        .header {
            background: #ff6b00;
            color: white;
            padding: 18px 0;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: auto;
        }

        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            text-decoration: none;
            font-size: 24px;
            font-weight: bold;
        }

        .back {
            color: white;
            text-decoration: none;
        }

        .box {
            background: white;
            margin: 35px 0;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, .05);
        }

        .box h1 {
            margin-top: 0;
        }

        .order-card {
            border: 1px solid #eee;
            border-radius: 10px;
            margin-top: 18px;
            padding: 20px;
        }

        .order-top {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: center;
            margin-bottom: 15px;
        }

        .order-code {
            font-weight: bold;
            font-size: 18px;
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            background: #fff3e0;
            color: #e65100;
            font-size: 14px;
            font-weight: bold;
        }

        .order-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 18px;
        }

        .info-label {
            color: #777;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .info-value {
            font-weight: bold;
        }

        .detail-btn {
            display: inline-block;
            padding: 10px 16px;
            background: #ff6b00;
            color: white;
            text-decoration: none;
            border-radius: 7px;
            font-weight: bold;
        }

        .empty {
            text-align: center;
            padding: 50px 20px;
        }

        .shop-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 12px 20px;
            background: #ff6b00;
            color: white;
            text-decoration: none;
            border-radius: 7px;
        }

        @media (max-width: 700px) {

            .order-info {
                grid-template-columns: 1fr;
            }

            .order-top {
                flex-direction: column;
                align-items: flex-start;
            }
        }

    </style>

</head>

<body>

<header class="header">

    <div class="container header-inner">

        <a
            href="index.php"
            class="logo"
        >
            <?= SITE_NAME ?>
        </a>

        <a
            href="products.php"
            class="back"
        >
            ← Mua hàng
        </a>

    </div>

</header>

<main class="container">

    <div class="box">

        <h1>
            📦 Đơn hàng của tôi
        </h1>

        <?php if (empty($orders)): ?>

            <div class="empty">

                <h2>
                    Bạn chưa có đơn hàng nào
                </h2>

                <p>
                    Hãy chọn sản phẩm và bắt đầu mua sắm nhé.
                </p>

                <a
                    href="products.php"
                    class="shop-btn"
                >
                    🛍️ Xem sản phẩm
                </a>

            </div>

        <?php else: ?>

            <?php foreach ($orders as $order): ?>

                <div class="order-card">

                    <div class="order-top">

                        <div>

                            <div class="order-code">
                                <?= htmlspecialchars(
                                    $order['order_code'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                            <div class="info-label">
                                <?= htmlspecialchars(
                                    $order['created_at'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                        </div>

                        <div class="status">

                            <?= htmlspecialchars(
                                getStatusText($order['status']),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>

                    </div>

                    <div class="order-info">

                        <div>

                            <div class="info-label">
                                Người nhận
                            </div>

                            <div class="info-value">
                                <?= htmlspecialchars(
                                    $order['receiver_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                        </div>

                        <div>

                            <div class="info-label">
                                Thanh toán
                            </div>

                            <div class="info-value">
                                <?= $order['payment_method'] === 'COD'
                                    ? 'COD'
                                    : 'Chuyển khoản' ?>
                            </div>

                        </div>

                        <div>

                            <div class="info-label">
                                Tổng tiền
                            </div>

                            <div
                                class="info-value"
                                style="color:#e53935;"
                            >
                                <?= formatOrderPrice(
                                    (float) $order['total']
                                ) ?>
                            </div>

                        </div>

                    </div>

                    <a
                        href="order_detail.php?id=<?= (int) $order['id'] ?>"
                        class="detail-btn"
                    >
                        Xem chi tiết →
                    </a>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</main>

</body>
</html>
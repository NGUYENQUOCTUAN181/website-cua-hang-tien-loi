<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/OrderController.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user']['id'];

$orderId = (int) ($_GET['id'] ?? 0);

if ($orderId <= 0) {
    header('Location: orders.php');
    exit;
}

$orderController = new OrderController($pdo);

$order = $orderController->findByIdForUser(
    $orderId,
    $userId
);

if (!$order) {
    http_response_code(404);
    die('Không tìm thấy đơn hàng.');
}

$items = $orderController->getItems($orderId);

function formatOrderDetailPrice(float $price): string
{
    return number_format($price, 0, ',', '.') . '₫';
}

function statusClass(string $status): string
{
    return match ($status) {
        'pending' => 'active-pending',
        'confirmed' => 'active-confirmed',
        'shipping' => 'active-shipping',
        'completed' => 'active-completed',
        default => ''
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

    <title>
        Chi tiết đơn hàng -
        <?= htmlspecialchars(
            $order['order_code'],
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </title>

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
            border-radius: 12px;
            margin: 30px 0;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, .05);
        }

        .order-code {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .date {
            color: #777;
        }

        /* TRACKING */

        .tracking {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            margin: 35px 0 20px;
            position: relative;
        }

        .tracking::before {
            content: "";
            position: absolute;
            top: 20px;
            left: 12%;
            right: 12%;
            height: 4px;
            background: #ddd;
            z-index: 0;
        }

        .step {
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .circle {
            width: 42px;
            height: 42px;
            margin: 0 auto 10px;
            border-radius: 50%;
            background: #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
        }

        .step.active .circle {
            background: #ff6b00;
            color: white;
        }

        .step.done .circle {
            background: #2e7d32;
            color: white;
        }

        .step-title {
            font-size: 14px;
            font-weight: bold;
        }
        <?php if ($order['status'] === 'cancelled'): ?>

    <div
        style="
            margin-top: 20px;
            padding: 15px;
            background: #ffebee;
            color: #c62828;
            border-radius: 8px;
            font-weight: bold;
        "
    >
        ❌ Đơn hàng đã bị hủy
    </div>

<?php endif; ?>

        /* INFORMATION */

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .info-box {
            background: #fafafa;
            padding: 18px;
            border-radius: 8px;
        }

        .info-box h3 {
            margin-top: 0;
        }

        .label {
            color: #777;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .value {
            font-weight: bold;
            margin-bottom: 12px;
        }

        /* ITEMS */

        .item {
            display: grid;
            grid-template-columns: 80px 1fr 100px 120px;
            gap: 15px;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid #eee;
        }

        .item-image {
            width: 75px;
            height: 75px;
            object-fit: cover;
            border-radius: 8px;
            background: #eee;
        }

        .item-name {
            font-weight: bold;
        }

        .item-quantity {
            color: #777;
            margin-top: 5px;
        }

        .item-price {
            color: #e53935;
            font-weight: bold;
        }

        .summary {
            margin-top: 25px;
            margin-left: auto;
            max-width: 400px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            padding-top: 15px;
            margin-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 20px;
            font-weight: bold;
        }

        .summary-total span:last-child {
            color: #e53935;
            font-size: 27px;
        }

        @media (max-width: 700px) {

            .info-grid {
                grid-template-columns: 1fr;
            }

            .tracking {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .tracking::before {
                display: none;
            }

            .item {
                grid-template-columns: 70px 1fr;
            }

            .item-price {
                grid-column: 2;
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
            href="orders.php"
            class="back"
        >
            ← Đơn hàng của tôi
        </a>

    </div>

</header>

<main class="container">

    <!-- HEADER ĐƠN -->

    <div class="box">

        <div class="order-code">

            <?= htmlspecialchars(
                $order['order_code'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </div>

        <div class="date">

            Ngày đặt:

            <?= htmlspecialchars(
                $order['created_at'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </div>


        <!-- TRACKING -->

        <div class="tracking">

            <div class="step
                <?= $order['status'] === 'pending'
                    ? 'active'
                    : (
                        in_array(
                            $order['status'],
                            ['confirmed', 'shipping', 'completed'],
                            true
                        )
                            ? 'done'
                            : ''
                    ) ?>">

                <div class="circle">
                    1
                </div>

                <div class="step-title">
                    Đã đặt hàng
                </div>

            </div>


            <div class="step
                <?= in_array(
                    $order['status'],
                    ['confirmed', 'shipping', 'completed'],
                    true
                ) ? (
                    $order['status'] === 'confirmed'
                        ? 'active'
                        : 'done'
                ) : '' ?>">

                <div class="circle">
                    2
                </div>

                <div class="step-title">
                    Đã xác nhận
                </div>

            </div>


            <div class="step
                <?= in_array(
                    $order['status'],
                    ['shipping', 'completed'],
                    true
                ) ? (
                    $order['status'] === 'shipping'
                        ? 'active'
                        : 'done'
                ) : '' ?>">

                <div class="circle">
                    3
                </div>

                <div class="step-title">
                    Đang giao
                </div>

            </div>


            <div class="step
                <?= $order['status'] === 'completed'
                    ? 'done'
                    : '' ?>">

                <div class="circle">
                    4
                </div>

                <div class="step-title">
                    Đã giao
                </div>

            </div>

        </div>

    </div>


    <!-- THÔNG TIN -->

    <div class="box">

        <h2>
            📦 Thông tin giao hàng
        </h2>

        <div class="info-grid">

            <div class="info-box">

                <h3>
                    Người nhận
                </h3>

                <div class="label">
                    Họ tên
                </div>

                <div class="value">
                    <?= htmlspecialchars(
                        $order['receiver_name'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>

                <div class="label">
                    Số điện thoại
                </div>

                <div class="value">
                    <?= htmlspecialchars(
                        $order['receiver_phone'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>

            </div>


            <div class="info-box">

                <h3>
                    Giao hàng
                </h3>

                <div class="label">
                    Địa chỉ
                </div>

                <div class="value">
                    <?= nl2br(
                        htmlspecialchars(
                            $order['shipping_address'],
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ) ?>
                </div>

                <div class="label">
                    Phương thức thanh toán
                </div>

                <div class="value">

                    <?= $order['payment_method'] === 'COD'
                        ? 'Thanh toán khi nhận hàng (COD)'
                        : 'Chuyển khoản ngân hàng' ?>

                </div>

            </div>

        </div>

    </div>


    <!-- SẢN PHẨM -->

    <div class="box">

        <h2>
            🛒 Sản phẩm
        </h2>


        <?php foreach ($items as $item): ?>

            <?php

            $imageUrl = !empty($item['image_url'])
                ? $item['image_url']
                : 'https://placehold.co/300x300/png?text=No+Image';

            ?>

            <div class="item">

                <img
                    src="<?= htmlspecialchars(
                        $imageUrl,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    alt="<?= htmlspecialchars(
                        $item['product_name'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    class="item-image"
                >


                <div>

                    <div class="item-name">

                        <?= htmlspecialchars(
                            $item['product_name'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                    <div class="item-quantity">

                        Số lượng:
                        <?= (int) $item['quantity'] ?>

                    </div>

                </div>


                <div>

                    <?= formatOrderDetailPrice(
                        (float) $item['price']
                    ) ?>

                </div>


                <div class="item-price">

                    <?= formatOrderDetailPrice(
                        (float) $item['subtotal']
                    ) ?>

                </div>

            </div>

        <?php endforeach; ?>


        <div class="summary">

            <div class="summary-row">

                <span>
                    Tạm tính
                </span>

                <strong>
                    <?= formatOrderDetailPrice(
                        (float) $order['subtotal']
                    ) ?>
                </strong>

            </div>


            <div class="summary-row">

                <span>
                    Giảm giá
                </span>

                <strong>
                    <?= formatOrderDetailPrice(
                        (float) $order['discount']
                    ) ?>
                </strong>

            </div>


            <div class="summary-row">

                <span>
                    Phí vận chuyển
                </span>

                <strong>

                    <?= (float) $order['shipping_fee'] > 0
                        ? formatOrderDetailPrice(
                            (float) $order['shipping_fee']
                        )
                        : 'Miễn phí' ?>

                </strong>

            </div>


            <div class="summary-total">

                <span>
                    Tổng cộng
                </span>

                <span>
                    <?= formatOrderDetailPrice(
                        (float) $order['total']
                    ) ?>
                </span>

            </div>

        </div>

    </div>

<?php if ($order['status'] === 'pending'): ?>

    <div class="box">

        <h2>
            ⚠️ Thao tác đơn hàng
        </h2>

        <form
            method="POST"
            action="cancel_order.php"
            onsubmit="return confirm(
                'Bạn có chắc chắn muốn hủy đơn hàng này không?'
            );"
        >

            <input
                type="hidden"
                name="order_id"
                value="<?= (int) $order['id'] ?>"
            >

            <button
                type="submit"
                style="
                    padding: 12px 20px;
                    border: none;
                    border-radius: 8px;
                    background: #e53935;
                    color: white;
                    font-weight: bold;
                    cursor: pointer;
                "
            >
                ❌ Hủy đơn hàng
            </button>

        </form>

    </div>

<?php endif; ?>
</main>

</body>

</html>
<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';
require_once __DIR__ . '/../controllers/AdminOrderController.php';

$orderId = (int) ($_GET['id'] ?? 0);

if ($orderId <= 0) {
    header('Location: orders.php');
    exit;
}

$orderController = new AdminOrderController($pdo);

$order = $orderController->findById($orderId);

if (!$order) {
    die('Không tìm thấy đơn hàng.');
}

$items = $orderController->getItems($orderId);

function adminOrderPrice(float $price): string
{
    return number_format(
        $price,
        0,
        ',',
        '.'
    ) . '₫';
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
            background: #222;
            color: white;
            padding: 18px 0;
        }

        .container {
            width: 92%;
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
            font-size: 23px;
            font-weight: bold;
        }

        .back {
            color: white;
            text-decoration: none;
        }

        .page {
            padding: 30px 0;
        }

        .box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .order-code {
            font-size: 23px;
            font-weight: bold;
        }

        .date {
            color: #777;
            margin-top: 5px;
        }

        .status-form {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .status-form select {
            padding: 11px;
            border: 1px solid #ddd;
            border-radius: 7px;
            flex: 1;
        }

        .status-form button {
            padding: 11px 18px;
            border: none;
            border-radius: 7px;
            background: #ff6b00;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .info {
            background: #fafafa;
            padding: 18px;
            border-radius: 8px;
        }

        .info h3 {
            margin-top: 0;
        }

        .label {
            color: #777;
            font-size: 13px;
        }

        .value {
            font-weight: bold;
            margin-bottom: 12px;
        }

        .item {
            display: grid;
            grid-template-columns: 1fr 100px 90px 120px;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            align-items: center;
        }

        .item-name {
            font-weight: bold;
        }

        .item-price {
            color: #e53935;
        }

        .summary {
            margin-left: auto;
            max-width: 380px;
            margin-top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #ddd;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 21px;
            font-weight: bold;
        }

        .summary-total span:last-child {
            color: #e53935;
        }

        @media (max-width: 700px) {

            .info-grid {
                grid-template-columns: 1fr;
            }

            .item {
                grid-template-columns: 1fr 1fr;
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
            🏪 Convenience Store Admin
        </a>

        <a
            href="orders.php"
            class="back"
        >
            ← Đơn hàng
        </a>

    </div>

</header>


<main class="container page">

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


        <?php if (
            !in_array(
                $order['status'],
                ['completed', 'cancelled'],
                true
            )
        ): ?>

            <form
                method="POST"
                action="order_action.php"
                class="status-form"
            >

                <input
                    type="hidden"
                    name="action"
                    value="update_status"
                >

                <input
                    type="hidden"
                    name="order_id"
                    value="<?= (int) $order['id'] ?>"
                >

                <select name="status">

                    <option
                        value="pending"
                        <?= $order['status'] === 'pending'
                            ? 'selected'
                            : '' ?>
                    >
                        Chờ xác nhận
                    </option>

                    <option
                        value="confirmed"
                        <?= $order['status'] === 'confirmed'
                            ? 'selected'
                            : '' ?>
                    >
                        Đã xác nhận
                    </option>

                    <option
                        value="shipping"
                        <?= $order['status'] === 'shipping'
                            ? 'selected'
                            : '' ?>
                    >
                        Đang giao
                    </option>

                    <option
                        value="completed"
                        <?= $order['status'] === 'completed'
                            ? 'selected'
                            : '' ?>
                    >
                        Đã giao
                    </option>

                    <option
                        value="cancelled"
                    >
                        Đã hủy
                    </option>

                </select>

                <button type="submit">
                    💾 Cập nhật
                </button>

            </form>

        <?php endif; ?>

    </div>


    <!-- THÔNG TIN -->

    <div class="box">

        <h2>
            👤 Thông tin đơn hàng
        </h2>

        <div class="info-grid">

            <div class="info">

                <h3>
                    Khách hàng
                </h3>

                <div class="label">
                    Tài khoản
                </div>

                <div class="value">

                    <?= htmlspecialchars(
                        $order['user_name'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>

                <div class="label">
                    Email
                </div>

                <div class="value">

                    <?= htmlspecialchars(
                        $order['user_email'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>

            </div>


            <div class="info">

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

            </div>

        </div>

    </div>


    <!-- SẢN PHẨM -->

    <div class="box">

        <h2>
            📦 Sản phẩm trong đơn
        </h2>


        <?php foreach ($items as $item): ?>

            <div class="item">

                <div>

                    <div class="item-name">

                        <?= htmlspecialchars(
                            $item['product_name'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                </div>


                <div>

                    x<?= (int) $item['quantity'] ?>

                </div>


                <div class="item-price">

                    <?= adminOrderPrice(
                        (float) $item['price']
                    ) ?>

                </div>


                <div class="item-price">

                    <?= adminOrderPrice(
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

                    <?= adminOrderPrice(
                        (float) $order['subtotal']
                    ) ?>

                </strong>

            </div>


            <div class="summary-row">

                <span>
                    Giảm giá
                </span>

                <strong>

                    <?= adminOrderPrice(
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
                        ? adminOrderPrice(
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

                    <?= adminOrderPrice(
                        (float) $order['total']
                    ) ?>

                </span>

            </div>

        </div>

    </div>

</main>

</body>

</html>
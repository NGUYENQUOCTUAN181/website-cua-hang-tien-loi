<?php

require_once __DIR__ . '/config/config.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$order = $_SESSION['order_success'] ?? null;

if (!$order) {
    header('Location: index.php');
    exit;
}

unset($_SESSION['order_success']);

function formatSuccessPrice(float $price): string
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
        Đặt hàng thành công - <?= SITE_NAME ?>
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;

            font-family: Arial, sans-serif;

            background: #f5f5f5;
        }


        .container {
            width: 90%;

            max-width: 700px;

            margin: auto;
        }


        .success-box {
            background: white;

            margin: 80px auto;

            padding: 45px;

            border-radius: 14px;

            text-align: center;

            box-shadow:
                0 5px 25px
                rgba(0, 0, 0, 0.08);
        }


        .icon {
            font-size: 70px;

            margin-bottom: 15px;
        }


        h1 {
            color: #2e7d32;

            margin-bottom: 10px;
        }


        .order-code {
            font-size: 22px;

            font-weight: bold;

            margin: 25px 0;

            padding: 15px;

            background: #f5f5f5;

            border-radius: 8px;
        }


        .total {
            color: #e53935;

            font-size: 27px;

            font-weight: bold;

            margin: 15px 0 25px;
        }


        .btn {
            display: inline-block;

            padding: 13px 22px;

            margin: 5px;

            border-radius: 8px;

            text-decoration: none;

            font-weight: bold;
        }


        .btn-primary {
            background: #ff6b00;

            color: white;
        }


        .btn-secondary {
            border: 1px solid #ff6b00;

            color: #ff6b00;

            background: white;
        }

    </style>

</head>


<body>


<div class="container">

    <div class="success-box">

        <div class="icon">
            🎉
        </div>


        <h1>
            Đặt hàng thành công!
        </h1>


        <p>
            Cảm ơn bạn đã mua hàng tại
            <strong><?= SITE_NAME ?></strong>.
        </p>


        <div class="order-code">

            Mã đơn hàng:

            <br>

            <?= htmlspecialchars(
                $order['order_code'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </div>


        <div>
            Tổng tiền
        </div>


        <div class="total">

            <?= formatSuccessPrice(
                (float) $order['total']
            ) ?>

        </div>


        <a
            href="products.php"
            class="btn btn-primary"
        >
            🛍️ Tiếp tục mua hàng
        </a>


        <a
            href="index.php"
            class="btn btn-secondary"
        >
            🏠 Về trang chủ
        </a>

    </div>

</div>


</body>

</html>
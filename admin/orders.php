<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';
require_once __DIR__ . '/../controllers/AdminOrderController.php';

$orderController = new AdminOrderController($pdo);

$keyword = trim($_GET['keyword'] ?? '');
$status = $_GET['status'] ?? '';

$orders = $orderController->getAll(
    $keyword,
    $status
);

function formatAdminOrderPrice(float $price): string
{
    return number_format(
        $price,
        0,
        ',',
        '.'
    ) . '₫';
}

function statusText(string $status): string
{
    return match ($status) {
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'shipping' => 'Đang giao',
        'completed' => 'Đã giao',
        'cancelled' => 'Đã hủy',
        default => $status
    };
}

function statusClass(string $status): string
{
    return match ($status) {
        'pending' => 'pending',
        'confirmed' => 'confirmed',
        'shipping' => 'shipping',
        'completed' => 'completed',
        'cancelled' => 'cancelled',
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

    <title>Quản lý đơn hàng - Admin</title>

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
            width: 94%;
            max-width: 1400px;
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

        .links {
            display: flex;
            gap: 15px;
        }

        .links a {
            color: white;
            text-decoration: none;
        }

        .page {
            padding: 30px 0;
        }

        .page-head h1 {
            margin-bottom: 8px;
        }

        .filter-box {
            background: white;
            padding: 18px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .filter-form {
            display: grid;
            grid-template-columns: 1fr 220px auto;
            gap: 10px;
        }

        input,
        select {
            width: 100%;
            padding: 11px;
            border: 1px solid #ddd;
            border-radius: 7px;
            font-size: 15px;
        }

        .filter-btn {
            border: none;
            background: #222;
            color: white;
            padding: 11px 18px;
            border-radius: 7px;
            cursor: pointer;
        }

        .table-box {
            background: white;
            border-radius: 10px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 1200px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px;
            border-bottom: 1px solid #eee;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #fafafa;
        }

        .order-code {
            font-weight: bold;
        }

        .small {
            color: #777;
            font-size: 13px;
            margin-top: 4px;
        }

        .total {
            color: #e53935;
            font-weight: bold;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .status.pending {
            background: #fff3e0;
            color: #e65100;
        }

        .status.confirmed {
            background: #e3f2fd;
            color: #1565c0;
        }

        .status.shipping {
            background: #ede7f6;
            color: #6a1b9a;
        }

        .status.completed {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status.cancelled {
            background: #ffebee;
            color: #c62828;
        }

        .detail-btn {
            display: inline-block;
            padding: 8px 12px;
            background: #ff6b00;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
        }

        .empty {
            padding: 50px;
            text-align: center;
        }

        .message {
            margin-bottom: 20px;
            padding: 13px 15px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 7px;
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

        <div class="links">

            <a href="index.php">
                Dashboard
            </a>

            <a href="products.php">
                Sản phẩm
            </a>

            <a href="../logout.php">
                Đăng xuất
            </a>

        </div>

    </div>

</header>

<main class="container page">

    <div class="page-head">

        <h1>
            🚚 Quản lý đơn hàng
        </h1>

        <p>
            Xem và cập nhật trạng thái đơn hàng.
        </p>

    </div>


    <?php if (isset($_GET['success'])): ?>

        <div class="message">
            ✅ Trạng thái đơn hàng đã được cập nhật.
        </div>

    <?php endif; ?>


    <div class="filter-box">

        <form
            method="GET"
            class="filter-form"
        >

            <input
                type="text"
                name="keyword"
                value="<?= htmlspecialchars(
                    $keyword,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                placeholder="Mã đơn, tên khách, email, SĐT..."
            >


            <select name="status">

                <option value="">
                    Tất cả trạng thái
                </option>

                <option
                    value="pending"
                    <?= $status === 'pending'
                        ? 'selected'
                        : '' ?>
                >
                    Chờ xác nhận
                </option>

                <option
                    value="confirmed"
                    <?= $status === 'confirmed'
                        ? 'selected'
                        : '' ?>
                >
                    Đã xác nhận
                </option>

                <option
                    value="shipping"
                    <?= $status === 'shipping'
                        ? 'selected'
                        : '' ?>
                >
                    Đang giao
                </option>

                <option
                    value="completed"
                    <?= $status === 'completed'
                        ? 'selected'
                        : '' ?>
                >
                    Đã giao
                </option>

                <option
                    value="cancelled"
                    <?= $status === 'cancelled'
                        ? 'selected'
                        : '' ?>
                >
                    Đã hủy
                </option>

            </select>


            <button
                type="submit"
                class="filter-btn"
            >
                🔍 Lọc
            </button>

        </form>

    </div>


    <div class="table-box">

        <?php if (empty($orders)): ?>

            <div class="empty">
                Không có đơn hàng.
            </div>

        <?php else: ?>

            <table>

                <thead>

                    <tr>

                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Người nhận</th>
                        <th>Thanh toán</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                        <th>Chi tiết</th>

                    </tr>

                </thead>

                <tbody>

                <?php foreach ($orders as $order): ?>

                    <tr>

                        <td>

                            <div class="order-code">

                                <?= htmlspecialchars(
                                    $order['order_code'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $order['user_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                            <div class="small">

                                <?= htmlspecialchars(
                                    $order['user_email'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $order['receiver_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                            <div class="small">

                                <?= htmlspecialchars(
                                    $order['receiver_phone'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        </td>


                        <td>

                            <?= $order['payment_method'] === 'COD'
                                ? '💵 COD'
                                : '🏦 Chuyển khoản' ?>

                        </td>


                        <td>

                            <span class="total">

                                <?= formatAdminOrderPrice(
                                    (float) $order['total']
                                ) ?>

                            </span>

                        </td>


                        <td>

                            <span
                                class="status <?= statusClass(
                                    $order['status']
                                ) ?>"
                            >

                                <?= statusText(
                                    $order['status']
                                ) ?>

                            </span>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $order['created_at'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <td>

                            <a
                                href="order_detail.php?id=<?= (int) $order['id'] ?>"
                                class="detail-btn"
                            >
                                Xem →
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php endif; ?>

    </div>

</main>

</body>

</html>
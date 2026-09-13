<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';
require_once __DIR__ . '/../models/AdminVoucher.php';

$voucherModel = new AdminVoucher($pdo);

$keyword = trim($_GET['keyword'] ?? '');
$status = $_GET['status'] ?? '';

$vouchers = $voucherModel->getAll(
    $keyword,
    $status
);

function formatVoucherValue(array $voucher): string
{
    if ($voucher['discount_type'] === 'percent') {
        return (float) $voucher['discount_value'] . '%';
    }

    return number_format(
        (float) $voucher['discount_value'],
        0,
        ',',
        '.'
    ) . '₫';
}

function voucherStatusText(
    string $status
): string {
    return $status === 'active'
        ? 'Đang hoạt động'
        : 'Ngừng hoạt động';
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

    <title>Quản lý Voucher - Admin</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
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

        .page-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-head h1 {
            margin: 0;
        }

        .add-btn {
            padding: 12px 18px;
            background: #ff6b00;
            color: white;
            text-decoration: none;
            border-radius: 7px;
            font-weight: bold;
        }

        .filter-box,
        .table-box {
            background: white;
            border-radius: 10px;
        }

        .filter-box {
            padding: 18px;
            margin-bottom: 20px;
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

        .filter-form button {
            border: none;
            background: #222;
            color: white;
            padding: 0 18px;
            border-radius: 7px;
            cursor: pointer;
        }

        .table-box {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 1250px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 13px;
            border-bottom: 1px solid #eee;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #fafafa;
        }

        .code {
            font-weight: bold;
            color: #e65100;
        }

        .small {
            color: #777;
            font-size: 13px;
            margin-top: 4px;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
        }

        .active {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .inactive {
            background: #eee;
            color: #777;
        }

        .actions {
            display: flex;
            gap: 7px;
        }

        .edit-btn,
        .delete-btn {
            padding: 8px 11px;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
        }

        .edit-btn {
            background: #fff3e0;
            color: #e65100;
        }

        .delete-btn {
            background: #ffebee;
            color: #c62828;
        }

        .message {
            padding: 13px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        @media (max-width: 750px) {
            .filter-form {
                grid-template-columns: 1fr;
            }

            .page-head,
            .header-inner {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }
    </style>

</head>

<body>

<header class="header">

    <div class="container header-inner">

        <a href="index.php" class="logo">
            🏪 Convenience Store Admin
        </a>

        <div class="links">
            <a href="index.php">Dashboard</a>
            <a href="orders.php">Đơn hàng</a>
            <a href="../logout.php">Đăng xuất</a>
        </div>

    </div>

</header>

<main class="container page">

    <div class="page-head">

        <div>
            <h1>🎟️ Quản lý Voucher</h1>
            <p>Tạo và quản lý mã giảm giá.</p>
        </div>

        <a
            href="voucher_form.php"
            class="add-btn"
        >
            ➕ Thêm voucher
        </a>

    </div>

    <?php if (isset($_GET['success'])): ?>

        <div class="message">
            ✅ Thao tác voucher thành công.
        </div>

    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>

        <div class="message">
            ✅ Voucher đã được ngừng hoạt động.
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
                placeholder="Mã voucher hoặc mô tả..."
            >

            <select name="status">

                <option value="">
                    Tất cả trạng thái
                </option>

                <option
                    value="active"
                    <?= $status === 'active'
                        ? 'selected'
                        : '' ?>
                >
                    Đang hoạt động
                </option>

                <option
                    value="inactive"
                    <?= $status === 'inactive'
                        ? 'selected'
                        : '' ?>
                >
                    Ngừng hoạt động
                </option>

            </select>

            <button type="submit">
                🔍 Lọc
            </button>

        </form>

    </div>

    <div class="table-box">

        <table>

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Mã</th>
                    <th>Giảm giá</th>
                    <th>Đơn tối thiểu</th>
                    <th>Giảm tối đa</th>
                    <th>Số lượng</th>
                    <th>Đã dùng</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>

            </thead>

            <tbody>

            <?php foreach ($vouchers as $voucher): ?>

                <tr>

                    <td>
                        #<?= (int) $voucher['id'] ?>
                    </td>

                    <td>

                        <div class="code">

                            <?= htmlspecialchars(
                                $voucher['code'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>

                        <div class="small">

                            <?= htmlspecialchars(
                                $voucher['description'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>

                    </td>

                    <td>

                        <?= formatVoucherValue($voucher) ?>

                    </td>

                    <td>

                        <?= number_format(
                            (float) $voucher['min_order_value'],
                            0,
                            ',',
                            '.'
                        ) ?>₫

                    </td>

                    <td>

                        <?= $voucher['max_discount'] !== null
                            ? number_format(
                                (float) $voucher['max_discount'],
                                0,
                                ',',
                                '.'
                            ) . '₫'
                            : 'Không giới hạn' ?>

                    </td>

                    <td>
                        <?= (int) $voucher['quantity'] ?>
                    </td>

                    <td>
                        <?= (int) $voucher['used_quantity'] ?>
                    </td>

                    <td>

                        <div>
                            <?= htmlspecialchars(
                                $voucher['start_date'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                        <div class="small">
                            →
                            <?= htmlspecialchars(
                                $voucher['end_date'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    </td>

                    <td>

                        <span
                            class="status <?= $voucher['status'] === 'active'
                                ? 'active'
                                : 'inactive' ?>"
                        >

                            <?= voucherStatusText(
                                $voucher['status']
                            ) ?>

                        </span>

                    </td>

                    <td>

                        <div class="actions">

                            <a
                                href="voucher_form.php?id=<?= (int) $voucher['id'] ?>"
                                class="edit-btn"
                            >
                                ✏️ Sửa
                            </a>

                            <?php if (
                                $voucher['status'] === 'active'
                            ): ?>

                                <form
                                    method="POST"
                                    action="voucher_action.php"
                                    onsubmit="return confirm(
                                        'Bạn có chắc muốn ngừng voucher này?'
                                    );"
                                >

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="delete"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $voucher['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="delete-btn"
                                    >
                                        🗑️ Ngừng
                                    </button>

                                </form>

                            <?php endif; ?>

                        </div>

                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</main>

</body>

</html>
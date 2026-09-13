<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';
require_once __DIR__ . '/../models/AdminVoucher.php';

$voucherModel = new AdminVoucher($pdo);

$id = (int) ($_GET['id'] ?? 0);

$voucher = null;

if ($id > 0) {

    $voucher = $voucherModel->findById($id);

    if (!$voucher) {
        header('Location: vouchers.php');
        exit;
    }
}

$isEdit = $voucher !== null;

function voucherDateValue(
    ?string $date
): string {
    if (!$date) {
        return '';
    }

    return date('Y-m-d\TH:i', strtotime($date));
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
        <?= $isEdit
            ? 'Sửa Voucher'
            : 'Thêm Voucher' ?>
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

        .header {
            background: #222;
            color: white;
            padding: 18px 0;
        }

        .container {
            width: 92%;
            max-width: 950px;
            margin: auto;
        }

        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo,
        .back {
            color: white;
            text-decoration: none;
        }

        .logo {
            font-size: 23px;
            font-weight: bold;
        }

        .page {
            padding: 35px 0;
        }

        .box {
            background: white;
            padding: 30px;
            border-radius: 12px;
        }

        h1 {
            margin-top: 0;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .full {
            grid-column: 1 / -1;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 7px;
            font-size: 15px;
        }

        textarea {
            min-height: 100px;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 7px;
            border: none;
            text-decoration: none;
            cursor: pointer;
            font-weight: bold;
        }

        .cancel {
            background: #eee;
            color: #333;
        }

        .save {
            background: #ff6b00;
            color: white;
        }

        @media (max-width: 700px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .full {
                grid-column: auto;
            }
        }

    </style>

</head>

<body>

<header class="header">

    <div class="container header-inner">

        <a
            href="vouchers.php"
            class="logo"
        >
            🎟️ Admin — Voucher
        </a>

        <a
            href="vouchers.php"
            class="back"
        >
            ← Danh sách
        </a>

    </div>

</header>

<main class="container page">

    <div class="box">

        <h1>
            <?= $isEdit
                ? '✏️ Sửa Voucher'
                : '➕ Thêm Voucher' ?>
        </h1>

        <form
            method="POST"
            action="voucher_action.php"
        >

            <input
                type="hidden"
                name="action"
                value="<?= $isEdit
                    ? 'update'
                    : 'create' ?>"
            >

            <?php if ($isEdit): ?>

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int) $voucher['id'] ?>"
                >

            <?php endif; ?>

            <div class="grid">

                <div class="form-group">

                    <label>
                        Mã voucher *
                    </label>

                    <input
                        type="text"
                        name="code"
                        value="<?= htmlspecialchars(
                            $voucher['code'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        placeholder="GIAM10"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Trạng thái
                    </label>

                    <select name="status">

                        <option
                            value="active"
                            <?= !$isEdit ||
                                $voucher['status'] === 'active'
                                ? 'selected'
                                : '' ?>
                        >
                            Hoạt động
                        </option>

                        <option
                            value="inactive"
                            <?= $isEdit &&
                                $voucher['status'] === 'inactive'
                                ? 'selected'
                                : '' ?>
                        >
                            Ngừng hoạt động
                        </option>

                    </select>

                </div>


                <div class="form-group full">

                    <label>
                        Mô tả
                    </label>

                    <textarea
                        name="description"
                    ><?= htmlspecialchars(
                        $voucher['description'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?></textarea>

                </div>


                <div class="form-group">

                    <label>
                        Loại giảm giá *
                    </label>

                    <select
                        name="discount_type"
                        id="discount_type"
                    >

                        <option
                            value="percent"
                            <?= !$isEdit ||
                                $voucher['discount_type'] === 'percent'
                                ? 'selected'
                                : '' ?>
                        >
                            Phần trăm (%)
                        </option>

                        <option
                            value="fixed"
                            <?= $isEdit &&
                                $voucher['discount_type'] === 'fixed'
                                ? 'selected'
                                : '' ?>
                        >
                            Số tiền cố định
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        Giá trị giảm *
                    </label>

                    <input
                        type="number"
                        name="discount_value"
                        min="0"
                        step="0.01"
                        value="<?= htmlspecialchars(
                            $voucher['discount_value'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Đơn tối thiểu
                    </label>

                    <input
                        type="number"
                        name="min_order_value"
                        min="0"
                        step="0.01"
                        value="<?= htmlspecialchars(
                            $voucher['min_order_value'] ?? '0',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Giảm tối đa
                    </label>

                    <input
                        type="number"
                        name="max_discount"
                        min="0"
                        step="0.01"
                        value="<?= htmlspecialchars(
                            $voucher['max_discount'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        placeholder="Để trống nếu không giới hạn"
                    >

                </div>


                <div class="form-group">

                    <label>
                        Số lượng voucher *
                    </label>

                    <input
                        type="number"
                        name="quantity"
                        min="1"
                        step="1"
                        value="<?= htmlspecialchars(
                            $voucher['quantity'] ?? '100',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Đã sử dụng
                    </label>

                    <input
                        type="number"
                        value="<?= (int) (
                            $voucher['used_quantity'] ?? 0
                        ) ?>"
                        disabled
                    >

                </div>


                <div class="form-group">

                    <label>
                        Bắt đầu *
                    </label>

                    <input
                        type="datetime-local"
                        name="start_date"
                        value="<?= voucherDateValue(
                            $voucher['start_date'] ?? null
                        ) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Kết thúc *
                    </label>

                    <input
                        type="datetime-local"
                        name="end_date"
                        value="<?= voucherDateValue(
                            $voucher['end_date'] ?? null
                        ) ?>"
                        required
                    >

                </div>

            </div>


            <div class="actions">

                <a
                    href="vouchers.php"
                    class="btn cancel"
                >
                    Hủy
                </a>

                <button
                    type="submit"
                    class="btn save"
                >
                    💾 Lưu Voucher
                </button>

            </div>

        </form>

    </div>

</main>

</body>

</html>
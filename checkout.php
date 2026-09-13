<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/CartController.php';
require_once __DIR__ . '/controllers/OrderController.php';
require_once __DIR__ . '/models/Voucher.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user']['id'];

$cartController = new CartController($pdo);
$voucherModel = new Voucher($pdo);
$orderController = new OrderController($pdo);

$items = $cartController->getItems($userId);

if (empty($items)) {
    header('Location: cart.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| HÀM FORMAT
|--------------------------------------------------------------------------
*/

function formatPrice(float $price): string
{
    return number_format(
        $price,
        0,
        ',',
        '.'
    ) . '₫';
}

/*
|--------------------------------------------------------------------------
| TÍNH TẠM TÍNH
|--------------------------------------------------------------------------
*/

$subtotal = 0;

foreach ($items as $item) {

    $unitPrice = $item['sale_price'] !== null
        ? (float) $item['sale_price']
        : (float) $item['price'];

    $subtotal +=
        $unitPrice * (int) $item['quantity'];
}

/*
|--------------------------------------------------------------------------
| DỮ LIỆU FORM
|--------------------------------------------------------------------------
*/

$receiverName = trim($_POST['receiver_name'] ?? '');

$receiverPhone = trim($_POST['receiver_phone'] ?? '');

$shippingAddress = trim(
    $_POST['shipping_address'] ?? ''
);

$paymentMethod = $_POST['payment_method'] ?? 'COD';

$note = trim($_POST['note'] ?? '');

/*
|--------------------------------------------------------------------------
| VOUCHER SESSION
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['checkout_voucher'])) {
    $_SESSION['checkout_voucher'] = null;
}

$voucherCode =
    $_SESSION['checkout_voucher']['code'] ?? null;

$discount =
    (float) (
        $_SESSION['checkout_voucher']['discount'] ?? 0
    );

$voucherMessage = '';

$voucherError = '';

$orderError = '';

/*
|--------------------------------------------------------------------------
| ÁP DỤNG VOUCHER
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    &&
    ($_POST['action'] ?? '') === 'apply_voucher'
) {

    $code = strtoupper(
        trim($_POST['voucher_code'] ?? '')
    );

    if ($code === '') {

        $voucherError =
            'Vui lòng nhập mã voucher.';

    } else {

        $voucher = $voucherModel->findValid($code);

        if (!$voucher) {

            $_SESSION['checkout_voucher'] = null;

            $voucherCode = null;
            $discount = 0;

            $voucherError =
                'Voucher không tồn tại, đã hết lượt hoặc đã hết hạn.';

        } else {

            try {

                $discount =
                    $voucherModel->calculateDiscount(
                        $voucher,
                        $subtotal
                    );

                $_SESSION['checkout_voucher'] = [
                    'code' => $voucher['code'],
                    'discount' => $discount
                ];

                $voucherCode =
                    $voucher['code'];

                $voucherMessage =
                    'Áp dụng voucher thành công!';

            } catch (Throwable $e) {

                $_SESSION['checkout_voucher'] = null;

                $voucherCode = null;
                $discount = 0;

                $voucherError =
                    $e->getMessage();
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| BỎ VOUCHER
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    &&
    ($_POST['action'] ?? '') === 'remove_voucher'
) {

    $_SESSION['checkout_voucher'] = null;

    $voucherCode = null;
    $discount = 0;

    $voucherMessage =
        'Đã bỏ voucher.';
}

/*
|--------------------------------------------------------------------------
| KIỂM TRA LẠI VOUCHER ĐANG LƯU
|--------------------------------------------------------------------------
*/

if (
    $voucherCode !== null
    &&
    ($_POST['action'] ?? '') !== 'apply_voucher'
    &&
    ($_POST['action'] ?? '') !== 'remove_voucher'
) {

    $voucher = $voucherModel->findValid($voucherCode);

    if ($voucher) {

        try {

            $discount =
                $voucherModel->calculateDiscount(
                    $voucher,
                    $subtotal
                );

            $_SESSION['checkout_voucher']['discount'] =
                $discount;

        } catch (Throwable $e) {

            $_SESSION['checkout_voucher'] = null;

            $voucherCode = null;
            $discount = 0;
        }

    } else {

        $_SESSION['checkout_voucher'] = null;

        $voucherCode = null;
        $discount = 0;
    }
}

/*
|--------------------------------------------------------------------------
| TỔNG TIỀN
|--------------------------------------------------------------------------
*/

$shippingFee = 0;

$total = max(
    0,
    $subtotal - $discount + $shippingFee
);

/*
|--------------------------------------------------------------------------
| XÁC NHẬN ĐẶT HÀNG
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    &&
    ($_POST['action'] ?? '') === 'place_order'
) {

    /*
     * Validate họ tên
     */
    if ($receiverName === '') {

        $orderError =
            'Vui lòng nhập họ tên người nhận.';

    } elseif (
        mb_strlen($receiverName) < 2
    ) {

        $orderError =
            'Họ tên người nhận phải có ít nhất 2 ký tự.';
    }

    /*
     * Validate số điện thoại
     */
    if ($orderError === '') {

        if ($receiverPhone === '') {

            $orderError =
                'Vui lòng nhập số điện thoại.';

        } elseif (
            !preg_match(
                '/^[0-9]{9,11}$/',
                $receiverPhone
            )
        ) {

            $orderError =
                'Số điện thoại phải gồm 9-11 chữ số.';
        }
    }

    /*
     * Validate địa chỉ
     */
    if ($orderError === '') {

        if ($shippingAddress === '') {

            $orderError =
                'Vui lòng nhập địa chỉ giao hàng.';

        } elseif (
            mb_strlen($shippingAddress) < 10
        ) {

            $orderError =
                'Địa chỉ giao hàng phải có ít nhất 10 ký tự.';
        }
    }

    /*
     * Validate payment
     */
    if ($orderError === '') {

        if (!in_array(
            $paymentMethod,
            ['COD', 'BANK_TRANSFER'],
            true
        )) {

            $orderError =
                'Phương thức thanh toán không hợp lệ.';
        }
    }

    /*
     * TẠO ORDER
     */
    if ($orderError === '') {

        try {

            /*
             * Lấy voucher mới nhất.
             * createFromCart() sẽ lock voucher
             * và kiểm tra lại trong transaction.
             */
            $finalVoucherCode = $voucherCode;

            $result =
                $orderController->createFromCart(
                    $userId,
                    $receiverName,
                    $receiverPhone,
                    $shippingAddress,
                    $paymentMethod,
                    $note,
                    $finalVoucherCode
                );

            /*
             * Xóa voucher session sau khi đặt thành công
             */
            $_SESSION['checkout_voucher'] = null;

            /*
             * Lưu kết quả đơn hàng
             */
            $_SESSION['order_success'] = $result;

            /*
             * Chuyển sang trang thành công
             */
            header(
                'Location: order_success.php'
            );

            exit;

        } catch (Throwable $e) {

            $orderError =
                $e->getMessage();
        }
    }
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
        Thanh toán - <?= htmlspecialchars(
            SITE_NAME,
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
            max-width: 1200px;
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

        .layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 25px;
            margin: 30px 0;
        }

        .box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow:
                0 5px 20px rgba(0, 0, 0, 0.05);
        }

        h2 {
            margin-top: 0;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 7px;
            font-size: 15px;
            font-family: Arial, sans-serif;
            outline: none;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: #ff6b00;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 13px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .payment-option input {
            width: auto;
        }

        .voucher-box {
            border: 1px solid #ffd1ad;
            background: #fffaf5;
        }

        .voucher-form {
            display: flex;
            gap: 10px;
        }

        .voucher-form input {
            flex: 1;
        }

        .voucher-btn {
            border: none;
            padding: 0 18px;
            background: #ff6b00;
            color: white;
            border-radius: 7px;
            font-weight: bold;
            cursor: pointer;
        }

        .voucher-btn:hover {
            background: #e85d00;
        }

        .success {
            padding: 12px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 7px;
            margin-bottom: 12px;
        }

        .error {
            padding: 12px;
            background: #ffebee;
            color: #c62828;
            border-radius: 7px;
            margin-bottom: 12px;
        }

        .applied {
            padding: 14px;
            background: #e8f5e9;
            border-radius: 7px;
            color: #2e7d32;
        }

        .remove-btn {
            margin-top: 10px;
            border: none;
            background: transparent;
            color: #c62828;
            cursor: pointer;
            font-weight: bold;
        }

        .order-item {
            display: flex;
            gap: 12px;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .item-info {
            flex: 1;
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
            white-space: nowrap;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
        }

        .discount {
            color: #2e7d32;
        }

        .total {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #ddd;
            padding-top: 18px;
            margin-top: 10px;
            font-size: 21px;
            font-weight: bold;
        }

        .total span:last-child {
            color: #e53935;
            font-size: 28px;
        }

        .order-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 8px;
            background: #ff6b00;
            color: white;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }

        .order-btn:hover {
            background: #e85d00;
        }

        .top-error {
            margin-top: 25px;
        }

        .bank-info {
            display: none;
            padding: 14px;
            margin-top: 10px;
            background: #f5f5f5;
            border-radius: 8px;
        }

        @media (max-width: 850px) {

            .layout {
                grid-template-columns: 1fr;
            }

            .voucher-form {
                flex-direction: column;
            }

            .voucher-btn {
                padding: 12px;
            }

            .header-inner {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
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
            <?= htmlspecialchars(
                SITE_NAME,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </a>

        <a
            href="cart.php"
            class="back"
        >
            ← Quay lại giỏ hàng
        </a>

    </div>

</header>

<?php if ($orderError !== ''): ?>

    <div class="container">

        <div class="error top-error">

            ❌

            <?= htmlspecialchars(
                $orderError,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </div>

    </div>

<?php endif; ?>


<main class="container">

    <div class="layout">

        <!-- ==================================================
             CỘT TRÁI
        ================================================== -->

        <section>

            <!-- THÔNG TIN GIAO HÀNG -->

            <div class="box">

                <h2>
                    📦 Thông tin giao hàng
                </h2>

                <!--
                    FORM ĐẶT HÀNG CHÍNH
                -->

                <form
                    method="POST"
                    action="checkout.php"
                    id="placeOrderForm"
                >

                    <input
                        type="hidden"
                        name="action"
                        value="place_order"
                    >

                    <div class="form-group">

                        <label for="receiver_name">
                            Họ tên người nhận *
                        </label>

                        <input
                            type="text"
                            id="receiver_name"
                            name="receiver_name"
                            value="<?= htmlspecialchars(
                                $receiverName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            placeholder="Nguyễn Văn A"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="receiver_phone">
                            Số điện thoại *
                        </label>

                        <input
                            type="text"
                            id="receiver_phone"
                            name="receiver_phone"
                            value="<?= htmlspecialchars(
                                $receiverPhone,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            placeholder="0901234567"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="shipping_address">
                            Địa chỉ giao hàng *
                        </label>

                        <textarea
                            id="shipping_address"
                            name="shipping_address"
                            placeholder="Số nhà, đường, phường/xã..."
                            required
                        ><?= htmlspecialchars(
                            $shippingAddress,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></textarea>

                    </div>


                    <div class="form-group">

                        <label>
                            Phương thức thanh toán *
                        </label>

                        <label class="payment-option">

                            <input
                                type="radio"
                                name="payment_method"
                                value="COD"
                                <?= $paymentMethod === 'COD'
                                    ? 'checked'
                                    : '' ?>
                            >

                            💵 Thanh toán khi nhận hàng (COD)

                        </label>


                        <label class="payment-option">

                            <input
                                type="radio"
                                name="payment_method"
                                value="BANK_TRANSFER"
                                <?= $paymentMethod === 'BANK_TRANSFER'
                                    ? 'checked'
                                    : '' ?>
                            >

                            🏦 Chuyển khoản ngân hàng

                        </label>

                        <div
                            id="bankInfo"
                            class="bank-info"
                        >

                            <strong>
                                Thông tin chuyển khoản
                            </strong>

                            <p style="margin-bottom: 0;">

                                Ngân hàng:
                                Vietcombank

                                <br>

                                STK:
                                0123456789

                                <br>

                                Chủ tài khoản:
                                CONVENIENCE STORE

                            </p>

                        </div>

                    </div>


                    <div class="form-group">

                        <label for="note">
                            Ghi chú
                        </label>

                        <textarea
                            id="note"
                            name="note"
                            placeholder="Ví dụ: Giao giờ hành chính..."
                        ><?= htmlspecialchars(
                            $note,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></textarea>

                    </div>

                </form>

            </div>


            <!-- VOUCHER -->

            <div class="box voucher-box">

                <h2>
                    🎟️ Mã giảm giá
                </h2>


                <?php if (
                    $voucherMessage !== ''
                ): ?>

                    <div class="success">

                        ✅

                        <?= htmlspecialchars(
                            $voucherMessage,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                <?php endif; ?>


                <?php if (
                    $voucherError !== ''
                ): ?>

                    <div class="error">

                        ❌

                        <?= htmlspecialchars(
                            $voucherError,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                <?php endif; ?>


                <?php if ($voucherCode): ?>

                    <div class="applied">

                        ✅ Đã áp dụng:

                        <strong>

                            <?= htmlspecialchars(
                                $voucherCode,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </strong>

                        <br>

                        Giảm:

                        <strong>

                            <?= formatPrice(
                                $discount
                            ) ?>

                        </strong>


                        <form
                            method="POST"
                            action="checkout.php"
                        >

                            <input
                                type="hidden"
                                name="action"
                                value="remove_voucher"
                            >

                            <button
                                type="submit"
                                class="remove-btn"
                            >
                                Bỏ voucher
                            </button>

                        </form>

                    </div>

                <?php else: ?>

                    <form
                        method="POST"
                        action="checkout.php"
                        class="voucher-form"
                    >

                        <input
                            type="hidden"
                            name="action"
                            value="apply_voucher"
                        >

                        <input
                            type="text"
                            name="voucher_code"
                            placeholder="Nhập mã voucher, ví dụ GIAM10"
                            required
                        >

                        <button
                            type="submit"
                            class="voucher-btn"
                        >
                            Áp dụng
                        </button>

                    </form>

                <?php endif; ?>

            </div>

        </section>


        <!-- ==================================================
             CỘT PHẢI
        ================================================== -->

        <aside class="box">

            <h2>
                🛒 Đơn hàng
            </h2>


            <?php foreach ($items as $item): ?>

                <?php

                $unitPrice =
                    $item['sale_price'] !== null
                    ? (float) $item['sale_price']
                    : (float) $item['price'];

                $itemTotal =
                    $unitPrice *
                    (int) $item['quantity'];

                ?>

                <div class="order-item">

                    <div class="item-info">

                        <div class="item-name">

                            <?= htmlspecialchars(
                                $item['name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>

                        <div class="item-quantity">

                            x<?= (int) $item['quantity'] ?>

                        </div>

                    </div>


                    <div class="item-price">

                        <?= formatPrice(
                            $itemTotal
                        ) ?>

                    </div>

                </div>

            <?php endforeach; ?>


            <div class="summary-row">

                <span>
                    Tạm tính
                </span>

                <strong>

                    <?= formatPrice(
                        $subtotal
                    ) ?>

                </strong>

            </div>


            <div class="summary-row discount">

                <span>
                    Giảm giá
                </span>

                <strong>

                    -<?= formatPrice(
                        $discount
                    ) ?>

                </strong>

            </div>


            <div class="summary-row">

                <span>
                    Phí vận chuyển
                </span>

                <strong>
                    Miễn phí
                </strong>

            </div>


            <div class="total">

                <span>
                    Tổng cộng
                </span>

                <span>

                    <?= formatPrice(
                        $total
                    ) ?>

                </span>

            </div>


            <!-- NÚT ĐẶT HÀNG -->

            <button
                type="submit"
                form="placeOrderForm"
                class="order-btn"
                onclick="
                    return confirm(
                        'Bạn có chắc chắn muốn đặt đơn hàng này không?'
                    );
                "
            >
                ✅ Xác nhận đặt hàng
            </button>

        </aside>

    </div>

</main>


<script>

    const paymentRadios =
        document.querySelectorAll(
            'input[name="payment_method"]'
        );

    const bankInfo =
        document.getElementById(
            'bankInfo'
        );

    function updatePaymentInfo() {

        const checked =
            document.querySelector(
                'input[name="payment_method"]:checked'
            );

        if (
            checked &&
            checked.value === 'BANK_TRANSFER'
        ) {

            bankInfo.style.display =
                'block';

        } else {

            bankInfo.style.display =
                'none';
        }
    }

    paymentRadios.forEach(
        function (radio) {

            radio.addEventListener(
                'change',
                updatePaymentInfo
            );

        }
    );

    updatePaymentInfo();

</script>

</body>

</html>
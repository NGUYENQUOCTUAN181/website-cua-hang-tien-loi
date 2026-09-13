<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/CartController.php';


// ============================================================
// KIỂM TRA ĐĂNG NHẬP
// ============================================================

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}


$userId = (int) $_SESSION['user']['id'];


// ============================================================
// KHỞI TẠO CART CONTROLLER
// ============================================================

$cartController = new CartController($pdo);


// ============================================================
// LẤY DANH SÁCH SẢN PHẨM TRONG GIỎ
// ============================================================

$items = $cartController->getItems($userId);


// ============================================================
// TÍNH TỔNG TIỀN
// ============================================================

$total = 0;

foreach ($items as $item) {

    $unitPrice = $item['sale_price'] !== null
        ? (float) $item['sale_price']
        : (float) $item['price'];

    $itemTotal = $unitPrice * (int) $item['quantity'];

    $total += $itemTotal;
}


// ============================================================
// FORMAT GIÁ
// ============================================================

function formatCartPrice(float $price): string
{
    return number_format($price, 0, ',', '.') . '₫';
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
        Giỏ hàng - <?= htmlspecialchars(
            SITE_NAME,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </title>


    <style>

        /* =====================================================
           RESET
        ===================================================== */

        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;

            font-family: Arial, sans-serif;

            background: #f5f5f5;

            color: #222;
        }


        /* =====================================================
           HEADER
        ===================================================== */

        .header {

            background: #ff6b00;

            color: white;

            padding: 18px 0;

            box-shadow:
                0 2px 8px rgba(0, 0, 0, 0.08);
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

            gap: 20px;
        }


        .logo {

            color: white;

            text-decoration: none;

            font-size: 24px;

            font-weight: bold;
        }


        .header-link {

            color: white;

            text-decoration: none;

            font-weight: 500;
        }


        .header-link:hover {

            text-decoration: underline;
        }


        /* =====================================================
           CART CONTAINER
        ===================================================== */

        .cart-box {

            background: white;

            margin: 35px 0;

            padding: 28px;

            border-radius: 14px;

            box-shadow:
                0 5px 25px rgba(0, 0, 0, 0.05);
        }


        .cart-title {

            margin: 0 0 25px;

            font-size: 30px;
        }


        /* =====================================================
           MESSAGE
        ===================================================== */

        .message {

            padding: 13px 15px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-size: 15px;
        }


        .message.success {

            background: #e8f5e9;

            color: #2e7d32;

            border: 1px solid #c8e6c9;
        }


        .message.error {

            background: #ffebee;

            color: #c62828;

            border: 1px solid #ffcdd2;
        }


        /* =====================================================
           CART HEADER
        ===================================================== */

        .cart-header {

            display: grid;

            grid-template-columns:
                100px
                minmax(200px, 1fr)
                140px
                180px
                150px
                90px;

            gap: 18px;

            padding: 0 0 12px;

            border-bottom: 2px solid #eee;

            font-weight: bold;

            color: #555;

            align-items: center;
        }


        /* =====================================================
           CART ITEM
        ===================================================== */

        .cart-item {

            display: grid;

            grid-template-columns:
                100px
                minmax(200px, 1fr)
                140px
                180px
                150px
                90px;

            gap: 18px;

            align-items: center;

            padding: 20px 0;

            border-bottom: 1px solid #eee;
        }


        .cart-image {

            width: 90px;

            height: 90px;

            object-fit: cover;

            border-radius: 10px;

            background: #f2f2f2;

            border: 1px solid #eee;
        }


        .product-name {

            font-size: 17px;

            font-weight: bold;

            line-height: 1.4;

            margin-bottom: 7px;
        }


        .brand {

            color: #777;

            font-size: 14px;

            margin-bottom: 5px;
        }


        .stock {

            color: #777;

            font-size: 13px;
        }


        .unit-price {

            color: #e53935;

            font-weight: bold;

            font-size: 16px;
        }


        /* =====================================================
           QUANTITY
        ===================================================== */

        .quantity-form {

            display: flex;

            align-items: center;

            gap: 8px;
        }


        .quantity-form input {

            width: 65px;

            height: 40px;

            text-align: center;

            border: 1px solid #ddd;

            border-radius: 6px;

            font-size: 16px;

            outline: none;
        }


        .quantity-form input:focus {

            border-color: #ff6b00;
        }


        .update-btn {

            height: 40px;

            padding: 0 12px;

            border: 1px solid #ff6b00;

            background: white;

            color: #ff6b00;

            border-radius: 6px;

            cursor: pointer;

            font-weight: 600;
        }


        .update-btn:hover {

            background: #ff6b00;

            color: white;
        }


        /* =====================================================
           ITEM TOTAL
        ===================================================== */

        .item-total {

            color: #e53935;

            font-size: 17px;

            font-weight: bold;
        }


        /* =====================================================
           REMOVE
        ===================================================== */

        .remove-form {

            margin-top: 8px;
        }


        .remove-btn {

            border: none;

            background: transparent;

            color: #e53935;

            cursor: pointer;

            padding: 0;

            font-size: 14px;
        }


        .remove-btn:hover {

            text-decoration: underline;
        }


        /* =====================================================
           SUMMARY
        ===================================================== */

        .cart-summary {

            display: flex;

            justify-content: flex-end;

            margin-top: 30px;
        }


        .summary-box {

            width: 380px;

            max-width: 100%;
        }


        .summary-row {

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding: 8px 0;

            font-size: 16px;
        }


        .summary-total {

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding: 18px 0 20px;

            border-top: 1px solid #ddd;

            margin-top: 10px;

            font-size: 20px;

            font-weight: bold;
        }


        .summary-total-price {

            color: #e53935;

            font-size: 28px;
        }


        /* =====================================================
           BUTTONS
        ===================================================== */

        .action-buttons {

            display: flex;

            justify-content: flex-end;

            gap: 12px;
        }


        .continue-btn {

            display: inline-block;

            padding: 14px 20px;

            background: white;

            color: #ff6b00;

            border: 1px solid #ff6b00;

            text-decoration: none;

            border-radius: 8px;

            font-weight: bold;
        }


        .continue-btn:hover {

            background: #fff3e0;
        }


        .checkout-btn {

            display: inline-block;

            padding: 14px 22px;

            background: #ff6b00;

            color: white;

            text-decoration: none;

            border-radius: 8px;

            font-weight: bold;
        }


        .checkout-btn:hover {

            background: #e85d00;
        }


        /* =====================================================
           EMPTY CART
        ===================================================== */

        .empty {

            text-align: center;

            padding: 60px 20px;
        }


        .empty-icon {

            font-size: 60px;

            margin-bottom: 15px;
        }


        .empty h2 {

            margin: 0 0 10px;

            font-size: 26px;
        }


        .empty p {

            color: #777;

            margin-bottom: 25px;
        }


        .shop-btn {

            display: inline-block;

            padding: 13px 22px;

            background: #ff6b00;

            color: white;

            text-decoration: none;

            border-radius: 8px;

            font-weight: bold;
        }


        .shop-btn:hover {

            background: #e85d00;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 1050px) {

            .cart-header {

                display: none;
            }


            .cart-item {

                grid-template-columns:
                    100px
                    1fr
                    140px;

                gap: 15px;
            }


            .unit-price {

                grid-column: 2;
            }


            .quantity-form {

                grid-column: 2;
            }


            .item-total {

                grid-column: 3;

                grid-row: 2;
            }
        }


        @media (max-width: 650px) {

            .container {

                width: 94%;
            }


            .header-inner {

                flex-direction: column;

                align-items: flex-start;
            }


            .cart-box {

                padding: 18px;

                margin: 20px 0;
            }


            .cart-title {

                font-size: 26px;
            }


            .cart-item {

                grid-template-columns: 80px 1fr;

                gap: 12px;
            }


            .cart-image {

                width: 75px;

                height: 75px;
            }


            .unit-price,
            .quantity-form,
            .item-total {

                grid-column: 2;
            }


            .item-total {

                grid-row: auto;
            }


            .action-buttons {

                flex-direction: column;
            }


            .continue-btn,
            .checkout-btn {

                width: 100%;

                text-align: center;
            }
        }

    </style>

</head>


<body>


<!-- =========================================================
     HEADER
========================================================= -->

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
            href="products.php"
            class="header-link"
        >
            ← Tiếp tục mua hàng
        </a>

    </div>

</header>


<!-- =========================================================
     MAIN
========================================================= -->

<main class="container">

    <div class="cart-box">


        <h1 class="cart-title">

            🛒 Giỏ hàng

        </h1>


        <!-- =================================================
             SUCCESS MESSAGES
        ================================================== -->

        <?php if (isset($_GET['added'])): ?>

            <div class="message success">

                ✅ Đã thêm sản phẩm vào giỏ hàng!

            </div>

        <?php endif; ?>


        <?php if (isset($_GET['updated'])): ?>

            <div class="message success">

                ✅ Đã cập nhật số lượng sản phẩm!

            </div>

        <?php endif; ?>


        <?php if (isset($_GET['removed'])): ?>

            <div class="message success">

                ✅ Đã xóa sản phẩm khỏi giỏ hàng!

            </div>

        <?php endif; ?>


        <?php if (isset($_GET['error'])): ?>

            <div class="message error">

                ❌ Không thể thực hiện thao tác.
                Vui lòng kiểm tra lại số lượng hoặc tồn kho.

            </div>

        <?php endif; ?>


        <!-- =================================================
             EMPTY CART
        ================================================== -->

        <?php if (empty($items)): ?>


            <div class="empty">

                <div class="empty-icon">
                    🛒
                </div>


                <h2>
                    Giỏ hàng đang trống
                </h2>


                <p>
                    Hãy chọn một sản phẩm để bắt đầu mua sắm.
                </p>


                <a
                    href="products.php"
                    class="shop-btn"
                >
                    🛍️ Xem sản phẩm
                </a>

            </div>


        <?php else: ?>


            <!-- =================================================
                 CART HEADER
            ================================================== -->

            <div class="cart-header">

                <div>
                    Ảnh
                </div>

                <div>
                    Sản phẩm
                </div>

                <div>
                    Đơn giá
                </div>

                <div>
                    Số lượng
                </div>

                <div>
                    Thành tiền
                </div>

                <div>
                    Xóa
                </div>

            </div>


            <!-- =================================================
                 CART ITEMS
            ================================================== -->

            <?php foreach ($items as $item): ?>

                <?php

                $unitPrice = $item['sale_price'] !== null
                    ? (float) $item['sale_price']
                    : (float) $item['price'];

                $itemTotal =
                    $unitPrice *
                    (int) $item['quantity'];

                $imageUrl = !empty($item['image_url'])
                    ? $item['image_url']
                    : 'https://placehold.co/300x300/png?text=No+Image';

                ?>


                <div class="cart-item">


                    <!-- IMAGE -->

                    <div>

                        <img
                            src="<?= htmlspecialchars(
                                $imageUrl,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            alt="<?= htmlspecialchars(
                                $item['name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="cart-image"
                        >

                    </div>


                    <!-- PRODUCT -->

                    <div>

                        <div class="product-name">

                            <?= htmlspecialchars(
                                $item['name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>


                        <div class="brand">

                            Thương hiệu:

                            <?= htmlspecialchars(
                                $item['brand_name']
                                    ?? 'Không có',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>


                        <div class="stock">

                            Còn <?= (int) $item['stock'] ?> sản phẩm

                        </div>

                    </div>


                    <!-- UNIT PRICE -->

                    <div class="unit-price">

                        <?= formatCartPrice(
                            $unitPrice
                        ) ?>

                    </div>


                    <!-- QUANTITY -->

                    <form
                        method="POST"
                        action="cart_action.php"
                        class="quantity-form"
                    >

                        <input
                            type="hidden"
                            name="action"
                            value="update"
                        >


                        <input
                            type="hidden"
                            name="cart_item_id"
                            value="<?= (int) $item['cart_item_id'] ?>"
                        >


                        <input
                            type="number"
                            name="quantity"
                            value="<?= (int) $item['quantity'] ?>"
                            min="1"
                            max="<?= (int) $item['stock'] ?>"
                            required
                        >


                        <button
                            type="submit"
                            class="update-btn"
                        >
                            Cập nhật
                        </button>

                    </form>


                    <!-- ITEM TOTAL -->

                    <div class="item-total">

                        <?= formatCartPrice(
                            $itemTotal
                        ) ?>

                    </div>


                    <!-- REMOVE -->

                    <form
                        method="POST"
                        action="cart_action.php"
                        class="remove-form"
                        onsubmit="return confirm(
                            'Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?'
                        );"
                    >

                        <input
                            type="hidden"
                            name="action"
                            value="remove"
                        >


                        <input
                            type="hidden"
                            name="cart_item_id"
                            value="<?= (int) $item['cart_item_id'] ?>"
                        >


                        <button
                            type="submit"
                            class="remove-btn"
                        >
                            🗑️ Xóa
                        </button>

                    </form>


                </div>


            <?php endforeach; ?>


            <!-- =================================================
                 SUMMARY
            ================================================== -->

            <div class="cart-summary">

                <div class="summary-box">


                    <div class="summary-row">

                        <span>
                            Số mặt hàng
                        </span>

                        <strong>
                            <?= count($items) ?>
                        </strong>

                    </div>


                    <div class="summary-row">

                        <span>
                            Tổng số lượng
                        </span>

                        <strong>
                            <?= $cartController->count($userId) ?>
                        </strong>

                    </div>


                    <div class="summary-total">

                        <span>
                            Tổng tiền
                        </span>

                        <span class="summary-total-price">

                            <?= formatCartPrice($total) ?>

                        </span>

                    </div>


                    <div class="action-buttons">

                        <a
                            href="products.php"
                            class="continue-btn"
                        >
                            ← Tiếp tục mua hàng
                        </a>


                        <a
                            href="checkout.php"
                            class="checkout-btn"
                        >
                            Tiến hành đặt hàng →
                        </a>

                    </div>


                </div>

            </div>


        <?php endif; ?>


    </div>

</main>


</body>

</html>
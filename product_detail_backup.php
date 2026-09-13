<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/ProductController.php';

$productController = new ProductController($pdo);

$productId = (int) ($_GET['id'] ?? 0);

if ($productId <= 0) {
    header('Location: products.php');
    exit;
}

$product = $productController->detail($productId);

if (!$product) {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Không tìm thấy sản phẩm - <?= SITE_NAME ?></title>

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

            .container {
                width: 90%;
                max-width: 1200px;
                margin: auto;
            }

            .error-box {
                background: white;
                margin: 80px auto;
                max-width: 600px;
                padding: 40px;
                text-align: center;
                border-radius: 12px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            }

            .error-box h1 {
                margin-top: 0;
            }

            .back-btn {
                display: inline-block;
                margin-top: 15px;
                padding: 12px 20px;
                background: #ff6b00;
                color: white;
                text-decoration: none;
                border-radius: 7px;
            }
        </style>
    </head>

    <body>

        <div class="container">

            <div class="error-box">

                <h1>Không tìm thấy sản phẩm 😢</h1>

                <p>Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.</p>

                <a href="products.php" class="back-btn">
                    ← Quay lại sản phẩm
                </a>

            </div>

        </div>

    </body>
    </html>
    <?php
    exit;
}


/*
|--------------------------------------------------------------------------
| XỬ LÝ HÌNH ẢNH
|--------------------------------------------------------------------------
*/

$images = $product['images'] ?? [];

$mainImage = null;

/*
 * Ưu tiên ảnh có is_primary = 1
 */
foreach ($images as $image) {

    if ((int) $image['is_primary'] === 1) {

        $mainImage = $image['image_url'];

        break;
    }
}

/*
 * Nếu không có ảnh primary thì lấy ảnh đầu tiên
 */
if (!$mainImage && !empty($images)) {

    $mainImage = $images[0]['image_url'];
}

/*
 * Nếu không có ảnh thì dùng placeholder
 */
if (!$mainImage) {

    $mainImage =
        'https://placehold.co/600x600/png?text=' .
        urlencode($product['name']);
}


/*
|--------------------------------------------------------------------------
| GIÁ HIỂN THỊ
|--------------------------------------------------------------------------
*/

$displayPrice = $product['sale_price'] !== null
    ? (float) $product['sale_price']
    : (float) $product['price'];


/*
|--------------------------------------------------------------------------
| FORMAT GIÁ
|--------------------------------------------------------------------------
*/

function formatDetailPrice(float $price): string
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

        <?= htmlspecialchars(
            $product['name'],
            ENT_QUOTES,
            'UTF-8'
        ) ?>

        -

        <?= SITE_NAME ?>

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


        .back {

            color: white;

            text-decoration: none;

            font-size: 16px;

            font-weight: 500;
        }


        .back:hover {

            text-decoration: underline;
        }


        /*
        |--------------------------------------------------------------------------
        | PRODUCT DETAIL
        |--------------------------------------------------------------------------
        */

        .product-detail {

            margin: 40px 0;

            background: white;

            border-radius: 14px;

            padding: 30px;

            display: grid;

            grid-template-columns:
                minmax(0, 1fr)
                minmax(0, 1fr);

            gap: 50px;

            box-shadow:
                0 5px 25px rgba(0, 0, 0, 0.05);
        }


        /*
        |--------------------------------------------------------------------------
        | GALLERY
        |--------------------------------------------------------------------------
        */

        .gallery {

            text-align: center;
        }


        .main-image {

            width: 100%;

            max-width: 520px;

            height: 500px;

            object-fit: contain;

            display: block;

            margin: auto;

            background: #f2f2f2;

            border-radius: 12px;

            border: 1px solid #eee;
        }


        .thumbnails {

            display: flex;

            justify-content: center;

            gap: 12px;

            margin-top: 18px;

            flex-wrap: wrap;
        }


        .thumbnail {

            width: 82px;

            height: 82px;

            object-fit: cover;

            background: #f5f5f5;

            border: 2px solid #ddd;

            border-radius: 8px;

            cursor: pointer;

            transition: 0.2s;
        }


        .thumbnail:hover {

            border-color: #ff6b00;

            transform: translateY(-2px);
        }


        .thumbnail.active {

            border-color: #ff6b00;

            box-shadow:
                0 0 0 2px rgba(255, 107, 0, 0.15);
        }


        /*
        |--------------------------------------------------------------------------
        | INFORMATION
        |--------------------------------------------------------------------------
        */

        .information {

            padding: 10px 0;
        }


        .category {

            color: #777;

            font-size: 14px;

            margin-bottom: 12px;
        }


        .product-name {

            font-size: 34px;

            line-height: 1.25;

            margin: 0 0 16px;
        }


        .brand {

            margin-bottom: 20px;

            color: #666;

            font-size: 16px;
        }


        .price {

            color: #e53935;

            font-size: 32px;

            font-weight: bold;

            margin-bottom: 5px;
        }


        .old-price {

            color: #999;

            text-decoration: line-through;

            font-size: 18px;

            font-weight: normal;

            margin-left: 10px;
        }


        .sale-badge {

            display: inline-block;

            margin-left: 10px;

            padding: 4px 8px;

            background: #e53935;

            color: white;

            font-size: 12px;

            border-radius: 4px;

            vertical-align: middle;
        }


        .stock {

            margin: 18px 0;

            font-size: 16px;
        }


        .stock.available {

            color: #2e7d32;
        }


        .stock.empty {

            color: #d32f2f;
        }


        /*
        |--------------------------------------------------------------------------
        | DESCRIPTION
        |--------------------------------------------------------------------------
        */

        .description {

            margin-top: 25px;

            padding-top: 20px;

            border-top: 1px solid #eee;

            line-height: 1.7;
        }


        .description h3 {

            margin-top: 0;

            margin-bottom: 12px;
        }


        .description p {

            margin: 0;

            color: #444;
        }


        /*
        |--------------------------------------------------------------------------
        | QUANTITY
        |--------------------------------------------------------------------------
        */

        .quantity-area {

            margin-top: 28px;
        }


        .quantity-title {

            font-weight: bold;

            margin-bottom: 10px;
        }


        .quantity-input {

            display: flex;

            align-items: center;

            gap: 8px;
        }


        .quantity-input button {

            width: 42px;

            height: 42px;

            border: 1px solid #ddd;

            background: white;

            cursor: pointer;

            font-size: 22px;

            border-radius: 6px;

            transition: 0.2s;
        }


        .quantity-input button:hover {

            background: #ff6b00;

            color: white;

            border-color: #ff6b00;
        }


        .quantity-input input {

            width: 75px;

            height: 42px;

            text-align: center;

            border: 1px solid #ddd;

            border-radius: 6px;

            font-size: 18px;

            outline: none;
        }


        .quantity-input input:focus {

            border-color: #ff6b00;
        }


        /*
        |--------------------------------------------------------------------------
        | ADD TO CART
        |--------------------------------------------------------------------------
        */

        .cart-form {

            margin-top: 22px;
        }


        .add-cart {

            width: 100%;

            padding: 16px;

            border: none;

            background: #ff6b00;

            color: white;

            font-size: 18px;

            font-weight: bold;

            border-radius: 9px;

            cursor: pointer;

            transition: 0.2s;
        }


        .add-cart:hover {

            background: #e85d00;

            transform: translateY(-1px);
        }


        .disabled {

            background: #999;

            cursor: not-allowed;
        }


        .disabled:hover {

            background: #999;

            transform: none;
        }


        /*
        |--------------------------------------------------------------------------
        | NOTICE
        |--------------------------------------------------------------------------
        */

        .login-notice {

            margin-top: 12px;

            padding: 12px;

            background: #fff3e0;

            color: #e65100;

            border-radius: 7px;

            font-size: 14px;

            text-align: center;
        }


        .login-notice a {

            color: #e65100;

            font-weight: bold;
        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 900px) {

            .product-detail {

                grid-template-columns: 1fr;

                gap: 35px;
            }


            .main-image {

                height: 400px;
            }


            .product-name {

                font-size: 28px;
            }
        }


        @media (max-width: 600px) {

            .container {

                width: 94%;
            }


            .header-inner {

                flex-direction: column;

                align-items: flex-start;
            }


            .product-detail {

                padding: 20px;

                margin: 20px 0;
            }


            .main-image {

                height: 320px;
            }


            .product-name {

                font-size: 25px;
            }


            .price {

                font-size: 27px;
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
            <?= SITE_NAME ?>
        </a>


        <a
            href="products.php"
            class="back"
        >
            ← Quay lại sản phẩm
        </a>

    </div>

</header>


<!-- =========================================================
     MAIN
========================================================= -->

<main class="container">


    <div class="product-detail">


        <!-- =================================================
             GALLERY
        ================================================== -->

        <div class="gallery">


            <img
                id="mainProductImage"
                src="<?= htmlspecialchars(
                    $mainImage,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                alt="<?= htmlspecialchars(
                    $product['name'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                class="main-image"
            >


            <?php if (!empty($images)): ?>

                <div class="thumbnails">

                    <?php foreach ($images as $index => $image): ?>

                        <img
                            src="<?= htmlspecialchars(
                                $image['image_url'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            alt="<?= htmlspecialchars(
                                $product['name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="thumbnail <?= (
                                ($index === 0 &&
                                !$mainImage) ||
                                $image['image_url'] === $mainImage
                            ) ? 'active' : '' ?>"
                            onclick="changeMainImage(
                                this,
                                '<?= htmlspecialchars(
                                    $image['image_url'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>'
                            )"
                        >

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>


        </div>


        <!-- =================================================
             PRODUCT INFORMATION
        ================================================== -->

        <div class="information">


            <!-- CATEGORY -->

            <div class="category">

                Danh mục:

                <strong>

                    <?= htmlspecialchars(
                        $product['category_name'] ?? 'Không có',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </strong>

            </div>


            <!-- PRODUCT NAME -->

            <h1 class="product-name">

                <?= htmlspecialchars(
                    $product['name'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </h1>


            <!-- BRAND -->

            <div class="brand">

                Thương hiệu:

                <strong>

                    <?= htmlspecialchars(
                        $product['brand_name'] ?? 'Không có',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </strong>

            </div>


            <!-- PRICE -->

            <div class="price">

                <?= formatDetailPrice($displayPrice) ?>


                <?php if ($product['sale_price'] !== null): ?>

                    <span class="old-price">

                        <?= formatDetailPrice(
                            (float) $product['price']
                        ) ?>

                    </span>


                    <span class="sale-badge">

                        SALE

                    </span>

                <?php endif; ?>

            </div>


            <!-- STOCK -->

            <?php if ((int) $product['stock'] > 0): ?>

                <div class="stock available">

                    ✅ Còn

                    <strong>

                        <?= (int) $product['stock'] ?>

                    </strong>

                    sản phẩm

                </div>

            <?php else: ?>

                <div class="stock empty">

                    ❌ Sản phẩm hiện đã hết hàng

                </div>

            <?php endif; ?>


            <!-- DESCRIPTION -->

            <div class="description">

                <h3>

                    Mô tả sản phẩm

                </h3>


                <p>

                    <?= nl2br(
                        htmlspecialchars(
                            $product['description']
                                ?? 'Chưa có mô tả sản phẩm.',
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ) ?>

                </p>

            </div>


            <!-- =================================================
                 QUANTITY + CART
            ================================================== -->

            <?php if ((int) $product['stock'] > 0): ?>


                <div class="quantity-area">


                    <div class="quantity-title">

                        Số lượng

                    </div>


                    <div class="quantity-input">

                        <button
                            type="button"
                            onclick="decreaseQuantity()"
                        >
                            −
                        </button>


                        <input
                            type="number"
                            id="quantity"
                            value="1"
                            min="1"
                            max="<?= (int) $product['stock'] ?>"
                            onchange="validateQuantity()"
                        >


                        <button
                            type="button"
                            onclick="increaseQuantity()"
                        >
                            +
                        </button>


                    </div>


                </div>


                <?php if (isset($_SESSION['user'])): ?>


                    <form
                        method="POST"
                        action="cart_action.php"
                        class="cart-form"
                    >

                        <input
                            type="hidden"
                            name="action"
                            value="add"
                        >


                        <input
                            type="hidden"
                            name="product_id"
                            value="<?= (int) $product['id'] ?>"
                        >


                        <input
                            type="hidden"
                            name="quantity"
                            id="cartQuantity"
                            value="1"
                        >


                        <button
                            type="submit"
                            class="add-cart"
                        >

                            🛒 Thêm vào giỏ hàng

                        </button>

                    </form>


                <?php else: ?>


                    <div class="login-notice">

                        🔐 Bạn cần

                        <a href="login.php">
                            đăng nhập
                        </a>

                        để thêm sản phẩm vào giỏ hàng.

                    </div>


                <?php endif; ?>


            <?php else: ?>


                <button
                    type="button"
                    class="add-cart disabled"
                    disabled
                >

                    Hết hàng

                </button>


            <?php endif; ?>


        </div>


    </div>


</main>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>


function syncCartQuantity()
{
    const quantityInput =
        document.getElementById('quantity');

    const cartQuantity =
        document.getElementById('cartQuantity');


    if (
        quantityInput &&
        cartQuantity
    ) {

        cartQuantity.value =
            quantityInput.value;
    }
}



function decreaseQuantity()
{
    const input =
        document.getElementById('quantity');


    if (!input) {
        return;
    }


    let value =
        parseInt(input.value) || 1;


    if (value > 1) {

        value--;

        input.value = value;
    }


    syncCartQuantity();
}



function increaseQuantity()
{
    const input =
        document.getElementById('quantity');


    if (!input) {
        return;
    }


    let value =
        parseInt(input.value) || 1;


    const max =
        parseInt(input.max) || 1;


    if (value < max) {

        value++;

        input.value = value;
    }


    syncCartQuantity();
}



function validateQuantity()
{
    const input =
        document.getElementById('quantity');


    if (!input) {
        return;
    }


    const min =
        parseInt(input.min) || 1;


    const max =
        parseInt(input.max) || 1;


    let value =
        parseInt(input.value);


    if (isNaN(value)) {

        value = min;
    }


    if (value < min) {

        value = min;
    }


    if (value > max) {

        value = max;
    }


    input.value = value;


    syncCartQuantity();
}



function changeMainImage(
    element,
    imageUrl
)
{
    const mainImage =
        document.getElementById(
            'mainProductImage'
        );


    if (!mainImage) {
        return;
    }


    mainImage.src = imageUrl;


    /*
     * Bỏ active của tất cả thumbnail
     */

    document
        .querySelectorAll('.thumbnail')
        .forEach(function(thumbnail) {

            thumbnail.classList.remove(
                'active'
            );

        });


    /*
     * Active thumbnail đang chọn
     */

    if (element) {

        element.classList.add(
            'active'
        );

    }
}


/*
|--------------------------------------------------------------------------
| Đồng bộ quantity trước khi submit
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function() {

        const quantityInput =
            document.getElementById(
                'quantity'
            );


        if (quantityInput) {

            quantityInput.addEventListener(
                'input',
                syncCartQuantity
            );

        }

    }
);

</script>


</body>

</html>
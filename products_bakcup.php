<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/ProductController.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Brand.php';

$productController = new ProductController($pdo);
$categoryModel = new Category($pdo);

$data = $productController->index();

$products = $data['products'];
$keyword = $data['keyword'];
$categoryId = $data['category_id'];

$categories = $categoryModel->getParentCategories();

$user = $_SESSION['user'] ?? null;


/*
|--------------------------------------------------------------------------
| LẤY ẢNH PRIMARY CHO SẢN PHẨM
|--------------------------------------------------------------------------
*/

$productIds = [];

foreach ($products as $product) {
    $productIds[] = (int) $product['id'];
}

$productImages = [];

if (!empty($productIds)) {

    $placeholders = implode(
        ',',
        array_fill(
            0,
            count($productIds),
            '?'
        )
    );

    $sql = "SELECT
                product_id,
                image_url
            FROM product_images
            WHERE product_id IN ($placeholders)
            AND is_primary = 1";

    $stmt = $pdo->prepare($sql);

    $stmt->execute($productIds);

    $imageRows = $stmt->fetchAll();

    foreach ($imageRows as $row) {

        $productImages[
            (int) $row['product_id']
        ] = $row['image_url'];
    }
}


/*
|--------------------------------------------------------------------------
| FORMAT
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


function getProductImage(
    array $product,
    array $productImages
): string {

    $productId =
        (int) $product['id'];

    if (
        isset($productImages[$productId])
        &&
        trim($productImages[$productId]) !== ''
    ) {
        return $productImages[$productId];
    }

    /*
     * Fallback online.
     * Khi chưa có ảnh sản phẩm,
     * hiển thị ảnh theo tên sản phẩm.
     */

    return 'https://placehold.co/700x700/F4F4F4/777?text=' .
        urlencode($product['name']);
}


function calculateDiscountPercent(
    float $price,
    ?float $salePrice
): int {

    if (
        $salePrice === null
        ||
        $price <= 0
        ||
        $salePrice >= $price
    ) {
        return 0;
    }

    return (int) round(
        (($price - $salePrice) / $price) * 100
    );
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
        Nhà Mình Mart - Mua sắm tiện lợi mỗi ngày
    </title>

    <style>

        * {
            box-sizing: border-box;
        }


        html {
            scroll-behavior: smooth;
        }


        body {

            margin: 0;

            font-family:
                Inter,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                Arial,
                sans-serif;

            background:
                #f6f7f9;

            color:
                #202124;
        }


        a {
            text-decoration: none;
        }


        img {
            max-width: 100%;
            display: block;
        }


        .container {

            width: 92%;

            max-width: 1240px;

            margin: 0 auto;
        }


        /*
        ========================================================
        HEADER
        ========================================================
        */

        .header {

            position: sticky;

            top: 0;

            z-index: 1000;

            background:
                rgba(255, 107, 0, 0.97);

            backdrop-filter:
                blur(10px);

            box-shadow:
                0 4px 20px
                rgba(0, 0, 0, 0.12);
        }


        .header-inner {

            min-height: 74px;

            display: flex;

            align-items: center;

            gap: 25px;
        }


        /*
        LOGO
        */

        .brand {

            display: flex;

            align-items: center;

            gap: 10px;

            color: white;

            min-width: 230px;
        }


        .brand-logo {

            width: 44px;

            height: 44px;

            flex-shrink: 0;

            filter:
                drop-shadow(
                    0 3px 5px
                    rgba(0,0,0,.15)
                );
        }


        .brand-text {

            display: flex;

            flex-direction: column;
        }


        .brand-name {

            font-size: 22px;

            line-height: 1;

            font-weight: 800;

            letter-spacing: -.4px;
        }


        .brand-slogan {

            margin-top: 5px;

            font-size: 11px;

            opacity: .92;
        }


        /*
        SEARCH
        */

        .search-box {

            flex: 1;

            max-width: 560px;

            margin-left: auto;
        }


        .search-box form {

            display: flex;

            width: 100%;
        }


        .search-box input {

            flex: 1;

            height: 44px;

            padding:
                0 16px;

            border: none;

            outline: none;

            border-radius:
                10px 0 0 10px;

            font-size: 14px;
        }


        .search-box button {

            width: 76px;

            border: none;

            background:
                #202124;

            color: white;

            border-radius:
                0 10px 10px 0;

            font-weight: 700;

            cursor: pointer;
        }


        .search-box button:hover {

            background:
                #111;
        }


        /*
        HEADER ACTIONS
        */

        .header-actions {

            display: flex;

            align-items: center;

            gap: 10px;
        }


        .header-action {

            min-height: 42px;

            padding:
                0 13px;

            display: flex;

            align-items: center;

            gap: 7px;

            color: white;

            border:
                1px solid
                rgba(255,255,255,.35);

            border-radius: 9px;

            font-size: 14px;

            font-weight: 700;

            transition: .2s;
        }


        .header-action:hover {

            background:
                rgba(255,255,255,.14);

            transform:
                translateY(-1px);
        }


        /*
        ========================================================
        HERO
        ========================================================
        */

        .hero {

            position: relative;

            overflow: hidden;

            margin-top: 25px;

            min-height: 330px;

            border-radius: 24px;

            background:

                radial-gradient(
                    circle at 10% 20%,
                    rgba(255,255,255,.28),
                    transparent 30%
                ),

                radial-gradient(
                    circle at 90% 80%,
                    rgba(255,255,255,.2),
                    transparent 28%
                ),

                linear-gradient(
                    120deg,
                    #ff6b00 0%,
                    #ff8a2a 48%,
                    #ffb36b 100%
                );

            box-shadow:
                0 15px 45px
                rgba(255,107,0,.20);
        }


        .hero::before {

            content: "";

            position: absolute;

            width: 280px;

            height: 280px;

            right: -80px;

            top: -90px;

            border-radius: 50%;

            background:
                rgba(255,255,255,.10);
        }


        .hero::after {

            content: "";

            position: absolute;

            width: 180px;

            height: 180px;

            left: 42%;

            bottom: -100px;

            border-radius: 50%;

            background:
                rgba(255,255,255,.09);
        }


        .hero-content {

            position: relative;

            z-index: 2;

            padding: 55px;

            max-width: 700px;

            color: white;
        }


        .hero-badge {

            display: inline-block;

            padding:
                7px 12px;

            background:
                rgba(255,255,255,.18);

            border:
                1px solid
                rgba(255,255,255,.32);

            border-radius: 999px;

            font-size: 13px;

            font-weight: 700;

            margin-bottom: 18px;
        }


        .hero h1 {

            margin:
                0 0 14px;

            font-size:
                clamp(34px, 5vw, 52px);

            line-height:
                1.03;

            letter-spacing:
                -1.5px;
        }


        .hero p {

            margin:
                0 0 28px;

            font-size: 18px;

            line-height: 1.6;

            color:
                rgba(255,255,255,.94);
        }


        .hero-button {

            display: inline-flex;

            align-items: center;

            gap: 8px;

            background: white;

            color: #ed6500;

            padding:
                13px 20px;

            border-radius: 10px;

            font-weight: 800;

            transition: .2s;
        }


        .hero-button:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 8px 20px
                rgba(0,0,0,.12);
        }


        .hero-shopping {

            position: absolute;

            right: 7%;

            bottom: 25px;

            z-index: 2;

            font-size: 105px;

            filter:
                drop-shadow(
                    0 12px 20px
                    rgba(0,0,0,.13)
                );
        }


        /*
        ========================================================
        CATEGORY
        ========================================================
        */

        .section {

            padding-top: 45px;
        }


        .section-head {

            display: flex;

            align-items: flex-end;

            justify-content: space-between;

            margin-bottom: 20px;
        }


        .section-title {

            margin: 0;

            font-size: 28px;

            font-weight: 800;

            letter-spacing: -.6px;
        }


        .section-subtitle {

            margin:
                5px 0 0;

            color:
                #72757a;

            font-size: 14px;
        }


        .category-grid {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 15px;
        }


        .category-card {

            padding: 20px;

            background: white;

            border-radius: 14px;

            border:
                1px solid
                #eceef1;

            display: flex;

            align-items: center;

            gap: 15px;

            color: #252525;

            transition: .2s;

            box-shadow:
                0 5px 20px
                rgba(0,0,0,.03);
        }


        .category-card:hover {

            transform:
                translateY(-3px);

            border-color:
                #ffb37a;

            box-shadow:
                0 10px 24px
                rgba(255,107,0,.10);
        }


        .category-icon {

            width: 48px;

            height: 48px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 12px;

            background:
                #fff1e7;

            font-size: 24px;

            flex-shrink: 0;
        }


        .category-name {

            font-weight: 800;

            font-size: 15px;
        }


        .category-arrow {

            margin-left: auto;

            color:
                #ff6b00;

            font-size: 18px;
        }


        /*
        ========================================================
        CONTENT
        ========================================================
        */

        .content-layout {

            display: grid;

            grid-template-columns:
                225px 1fr;

            gap: 25px;

            align-items: start;
        }


        /*
        SIDEBAR
        */

        .sidebar {

            background: white;

            padding: 20px;

            border-radius: 16px;

            border:
                1px solid
                #eceef1;

            position: sticky;

            top: 100px;
        }


        .sidebar h3 {

            margin:
                0 0 15px;

            font-size: 18px;
        }


        .sidebar-link {

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding:
                11px 12px;

            margin-bottom: 4px;

            border-radius: 8px;

            color:
                #3d4044;

            transition: .2s;
        }


        .sidebar-link:hover {

            color:
                #ff6b00;

            background:
                #fff4ec;
        }


        .sidebar-link.active {

            color:
                #ff6b00;

            background:
                #fff0e6;

            font-weight: 800;
        }


        /*
        ========================================================
        PRODUCTS
        ========================================================
        */

        .product-toolbar {

            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 18px;

            padding-bottom: 15px;

            border-bottom:
                1px solid #e8eaed;
        }


        .result-count {

            color:
                #6f7378;

            font-size: 14px;
        }


        .product-grid {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 18px;
        }


        .product-card {

            position: relative;

            overflow: hidden;

            background: white;

            border-radius: 16px;

            border:
                1px solid
                #eceef1;

            box-shadow:
                0 6px 22px
                rgba(0,0,0,.04);

            transition:
                transform .2s,
                box-shadow .2s;
        }


        .product-card:hover {

            transform:
                translateY(-5px);

            box-shadow:
                0 15px 35px
                rgba(0,0,0,.10);
        }


        .sale-badge {

            position: absolute;

            left: 12px;

            top: 12px;

            z-index: 5;

            padding:
                6px 9px;

            border-radius: 7px;

            background:
                #ef4444;

            color: white;

            font-size: 11px;

            font-weight: 800;
        }


        .stock-badge {

            position: absolute;

            right: 12px;

            top: 12px;

            z-index: 5;

            padding:
                6px 9px;

            border-radius: 7px;

            background:
                rgba(255,255,255,.92);

            color:
                #2e7d32;

            font-size: 11px;

            font-weight: 800;
        }


        .stock-badge.out {

            color:
                #c62828;
        }


        .product-image-wrap {

            position: relative;

            overflow: hidden;

            background:
                #f2f3f5;

            aspect-ratio:
                1 / 1;
        }


        .product-image {

            width: 100%;

            height: 100%;

            object-fit: cover;

            transition:
                transform .35s;
        }


        .product-card:hover
        .product-image {

            transform:
                scale(1.045);
        }


        .product-info {

            padding: 16px;
        }


        .brand {

            margin-bottom: 6px;

            color:
                #8a8d91;

            font-size: 12px;
        }


        .product-name {

            min-height: 44px;

            color:
                #202124;

            font-size: 16px;

            font-weight: 800;

            line-height: 1.35;
        }


        .price-row {

            display: flex;

            align-items: center;

            gap: 8px;

            flex-wrap: wrap;

            margin:
                12px 0 8px;
        }


        .price {

            color:
                #e53935;

            font-size: 19px;

            font-weight: 900;
        }


        .old-price {

            color:
                #9aa0a6;

            font-size: 12px;

            text-decoration:
                line-through;
        }


        .product-stock {

            color:
                #656a70;

            font-size: 12px;

            margin-bottom: 13px;
        }


        .product-stock.out {

            color:
                #c62828;

            font-weight: 700;
        }


        .detail-btn {

            display: flex;

            justify-content: center;

            align-items: center;

            width: 100%;

            min-height: 42px;

            background:
                #ff6b00;

            color: white;

            border-radius: 9px;

            font-size: 14px;

            font-weight: 800;

            transition: .2s;
        }


        .detail-btn:hover {

            background:
                #ea5f00;
        }


        /*
        ========================================================
        EMPTY
        ========================================================
        */

        .empty {

            grid-column:
                1 / -1;

            padding:
                60px 20px;

            text-align: center;

            background: white;

            border-radius: 16px;
        }


        .empty-icon {

            font-size: 55px;

            margin-bottom: 10px;
        }


        .empty h2 {

            margin:
                0 0 7px;
        }


        .empty p {

            margin: 0;

            color: #777;
        }


        /*
        ========================================================
        FOOTER
        ========================================================
        */

        .footer {

            margin-top: 60px;

            padding:
                30px 0;

            background:
                #202124;

            color:
                #d7d9dc;
        }


        .footer-inner {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 20px;
        }


        .footer-brand {

            color: white;

            font-weight: 800;

            font-size: 18px;
        }


        .footer-small {

            font-size: 13px;

            color:
                #aeb1b5;
        }


        /*
        ========================================================
        RESPONSIVE
        ========================================================
        */

        @media (max-width: 1100px) {

            .product-grid {

                grid-template-columns:
                    repeat(3, 1fr);
            }

            .category-grid {

                grid-template-columns:
                    repeat(2, 1fr);
            }

            .hero-shopping {

                right: 3%;

                font-size: 85px;
            }
        }


        @media (max-width: 850px) {

            .header-inner {

                flex-wrap: wrap;

                padding:
                    12px 0;
            }

            .brand {

                min-width: auto;
            }

            .search-box {

                order: 3;

                width: 100%;

                max-width: none;
            }

            .content-layout {

                grid-template-columns:
                    1fr;
            }

            .sidebar {

                position: static;
            }

            .product-grid {

                grid-template-columns:
                    repeat(2, 1fr);
            }

            .hero-content {

                padding: 40px 30px;
            }

            .hero-shopping {

                opacity: .35;
            }
        }


        @media (max-width: 560px) {

            .container {

                width: 94%;
            }

            .header-action-text {

                display: none;
            }

            .header-action {

                padding:
                    0 10px;
            }

            .category-grid {

                grid-template-columns:
                    1fr;
            }

            .product-grid {

                grid-template-columns:
                    1fr 1fr;

                gap: 12px;
            }

            .product-info {

                padding: 12px;
            }

            .product-name {

                font-size: 14px;

                min-height: 39px;
            }

            .price {

                font-size: 16px;
            }

            .hero {

                min-height: 300px;
            }

            .hero h1 {

                font-size: 34px;
            }

            .hero p {

                font-size: 15px;
            }

            .section-title {

                font-size: 24px;
            }
        }

    </style>

</head>

<body>


<!-- ==========================================================
     HEADER
========================================================== -->

<header class="header">

    <div class="container header-inner">


        <!-- LOGO -->

        <a
            href="products.php"
            class="brand"
            aria-label="Nhà Mình Mart"
        >

            <svg
                class="brand-logo"
                viewBox="0 0 64 64"
                xmlns="http://www.w3.org/2000/svg"
            >

                <rect
                    x="7"
                    y="16"
                    width="50"
                    height="42"
                    rx="10"
                    fill="#ffffff"
                />

                <path
                    d="
                        M12 20
                        L32 7
                        L52 20
                        V25
                        H12
                        Z
                    "
                    fill="#fff"
                />

                <path
                    d="
                        M17 29
                        H47
                        V53
                        H17
                        Z
                    "
                    fill="#ff6b00"
                />

                <rect
                    x="24"
                    y="38"
                    width="7"
                    height="15"
                    rx="2"
                    fill="#ffffff"
                />

                <rect
                    x="35"
                    y="35"
                    width="7"
                    height="18"
                    rx="2"
                    fill="#ffffff"
                />

                <circle
                    cx="19"
                    cy="16"
                    r="3"
                    fill="#ffd180"
                />

                <circle
                    cx="45"
                    cy="16"
                    r="3"
                    fill="#ffd180"
                />

            </svg>


            <span class="brand-text">

                <span class="brand-name">
                    Nhà Mình Mart
                </span>

                <span class="brand-slogan">
                    Mua nhanh – Giá dễ chịu
                </span>

            </span>

        </a>


        <!-- SEARCH -->

        <div class="search-box">

            <form
                method="GET"
                action="products.php"
            >

                <input
                    type="text"
                    name="q"
                    value="<?= htmlspecialchars(
                        $keyword ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    placeholder="Bạn đang tìm gì hôm nay?"
                >

                <button type="submit">
                    🔍
                </button>

            </form>

        </div>


        <!-- ACTION -->

        <div class="header-actions">

            <a
                href="cart.php"
                class="header-action"
            >

                🛒

                <span class="header-action-text">
                    Giỏ hàng
                </span>

            </a>


            <?php if ($user): ?>

                <a
                    href="index.php"
                    class="header-action"
                >

                    👤

                    <span class="header-action-text">
                        <?= htmlspecialchars(
                            $user['name'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>

                </a>

            <?php else: ?>

                <a
                    href="login.php"
                    class="header-action"
                >

                    👤

                    <span class="header-action-text">
                        Đăng nhập
                    </span>

                </a>

            <?php endif; ?>

        </div>

    </div>

</header>


<!-- ==========================================================
     HERO
========================================================== -->

<main class="container">

    <section class="hero">

        <div class="hero-content">

            <div class="hero-badge">
                🏠 Tiện lợi cho cả nhà
            </div>

            <h1>
                Mua gì cũng gần.
                <br>
                Ghé Nhà Mình Mart.
            </h1>

            <p>
                Những món quen thuộc mỗi ngày,
                giá dễ chịu và đặt hàng thật nhanh.
                Mua online, giao tận nơi.
            </p>

            <a
                href="#products"
                class="hero-button"
            >
                🛍️ Khám phá sản phẩm
            </a>

        </div>


        <div class="hero-shopping">
            🛒
        </div>

    </section>


    <!-- ======================================================
         CATEGORY
    ======================================================= -->

    <section class="section">

        <div class="section-head">

            <div>

                <h2 class="section-title">
                    Mua theo danh mục
                </h2>

                <p class="section-subtitle">
                    Chọn nhanh nhóm sản phẩm bạn cần.
                </p>

            </div>

        </div>


        <div class="category-grid">

            <?php

            $categoryIcons = [
                'Bánh kẹo' => '🍪',
                'Gia dụng' => '🧴',
                'Đồ ăn' => '🍜',
                'Đồ uống' => '🥤'
            ];

            ?>


            <?php foreach (
                $categories as $category
            ): ?>

                <?php

                $categoryName =
                    $category['name'];

                $icon =
                    $categoryIcons[
                        $categoryName
                    ]
                    ?? '🛍️';

                ?>


                <a
                    href="
                        products.php?category_id=
                        <?= (int) $category['id'] ?>
                    "
                    class="category-card"
                >

                    <div class="category-icon">

                        <?= $icon ?>

                    </div>


                    <div>

                        <div class="category-name">

                            <?= htmlspecialchars(
                                $categoryName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>

                    </div>


                    <div class="category-arrow">
                        →
                    </div>

                </a>

            <?php endforeach; ?>

        </div>

    </section>


    <!-- ======================================================
         PRODUCTS
    ======================================================= -->

    <section
        class="section"
        id="products"
    >

        <div class="section-head">

            <div>

                <h2 class="section-title">

                    <?= $keyword !== ''
                        ? 'Kết quả tìm kiếm'
                        : 'Sản phẩm dành cho bạn' ?>

                </h2>


                <p class="section-subtitle">

                    <?php if (
                        $keyword !== ''
                    ): ?>

                        Từ khóa:

                        <strong>
                            <?= htmlspecialchars(
                                $keyword,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>

                    <?php else: ?>

                        Những sản phẩm quen thuộc
                        được khách hàng lựa chọn nhiều.

                    <?php endif; ?>

                </p>

            </div>

        </div>


        <div class="content-layout">


            <!-- SIDEBAR -->

            <aside class="sidebar">

                <h3>
                    📂 Danh mục
                </h3>


                <a
                    href="products.php"
                    class="
                        sidebar-link
                        <?= $categoryId === 0
                            ? 'active'
                            : '' ?>
                    "
                >

                    <span>
                        Tất cả sản phẩm
                    </span>

                    <span>
                        →
                    </span>

                </a>


                <?php foreach (
                    $categories as $category
                ): ?>

                    <a
                        href="
                            products.php?category_id=
                            <?= (int) $category['id'] ?>
                        "
                        class="
                            sidebar-link
                            <?= $categoryId ===
                                (int) $category['id']
                                ? 'active'
                                : '' ?>
                        "
                    >

                        <span>

                            <?= htmlspecialchars(
                                $category['name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </span>

                        <span>
                            →
                        </span>

                    </a>

                <?php endforeach; ?>

            </aside>


            <!-- PRODUCT LIST -->

            <section>

                <div class="product-toolbar">

                    <div class="result-count">

                        Hiển thị

                        <strong>
                            <?= count($products) ?>
                        </strong>

                        sản phẩm

                    </div>

                </div>


                <?php if (
                    empty($products)
                ): ?>

                    <div class="empty">

                        <div class="empty-icon">
                            😢
                        </div>

                        <h2>
                            Không tìm thấy sản phẩm
                        </h2>

                        <p>
                            Hãy thử một từ khóa khác
                            hoặc chọn danh mục khác.
                        </p>

                    </div>

                <?php else: ?>

                    <div class="product-grid">


                        <?php foreach (
                            $products as $product
                        ): ?>


                            <?php

                            $price =
                                (float) $product['price'];

                            $salePrice =
                                $product['sale_price'] !== null
                                ? (float) $product['sale_price']
                                : null;

                            $discountPercent =
                                calculateDiscountPercent(
                                    $price,
                                    $salePrice
                                );

                            $stock =
                                (int) $product['stock'];

                            $imageUrl =
                                getProductImage(
                                    $product,
                                    $productImages
                                );

                            ?>


                            <article
                                class="product-card"
                            >


                                <?php if (
                                    $discountPercent > 0
                                ): ?>

                                    <div class="sale-badge">

                                        -<?= $discountPercent ?>%

                                    </div>

                                <?php endif; ?>


                                <div
                                    class="
                                        stock-badge
                                        <?= $stock <= 0
                                            ? 'out'
                                            : '' ?>
                                    "
                                >

                                    <?= $stock > 0
                                        ? 'Còn hàng'
                                        : 'Hết hàng' ?>

                                </div>


                                <!-- IMAGE -->

                                <div
                                    class="
                                        product-image-wrap
                                    "
                                >

                                    <img
                                        src="<?= htmlspecialchars(
                                            $imageUrl,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        alt="<?= htmlspecialchars(
                                            $product['name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        class="
                                            product-image
                                        "
                                        loading="lazy"
                                        onerror="
                                            this.onerror=null;
                                            this.src='https://placehold.co/700x700/F4F4F4/777?text=<?= urlencode(
                                                $product['name']
                                            ) ?>';
                                        "
                                    >

                                </div>


                                <!-- INFO -->

                                <div
                                    class="
                                        product-info
                                    "
                                >

                                    <div class="brand">

                                        Thương hiệu:

                                        <?= htmlspecialchars(
                                            $product['brand_name']
                                                ?? 'Không có',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </div>


                                    <div
                                        class="
                                            product-name
                                        "
                                    >

                                        <?= htmlspecialchars(
                                            $product['name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </div>


                                    <div
                                        class="
                                            price-row
                                        "
                                    >

                                        <?php if (
                                            $salePrice !== null
                                        ): ?>

                                            <div
                                                class="
                                                    price
                                                "
                                            >

                                                <?= formatPrice(
                                                    $salePrice
                                                ) ?>

                                            </div>


                                            <div
                                                class="
                                                    old-price
                                                "
                                            >

                                                <?= formatPrice(
                                                    $price
                                                ) ?>

                                            </div>

                                        <?php else: ?>

                                            <div
                                                class="
                                                    price
                                                "
                                            >

                                                <?= formatPrice(
                                                    $price
                                                ) ?>

                                            </div>

                                        <?php endif; ?>

                                    </div>


                                    <div
                                        class="
                                            product-stock
                                            <?= $stock <= 0
                                                ? 'out'
                                                : '' ?>
                                        "
                                    >

                                        <?php if (
                                            $stock > 0
                                        ): ?>

                                            Còn
                                            <?= $stock ?>
                                            sản phẩm

                                        <?php else: ?>

                                            Sản phẩm đã hết hàng

                                        <?php endif; ?>

                                    </div>


                                    <a
                                        href="
                                            product_detail.php?id=
                                            <?= (int) $product['id'] ?>
                                        "
                                        class="
                                            detail-btn
                                        "
                                    >

                                        Xem chi tiết →

                                    </a>

                                </div>

                            </article>


                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </section>

        </div>

    </section>

</main>


<!-- ==========================================================
     FOOTER
========================================================== -->

<footer class="footer">

    <div class="container footer-inner">

        <div>

            <div class="footer-brand">
                🏠 Nhà Mình Mart
            </div>

            <div class="footer-small">
                Mua nhanh – Giá dễ chịu – Giao tận nơi
            </div>

        </div>


        <div class="footer-small">

            © <?= date('Y') ?>
            Nhà Mình Mart.
            All rights reserved.

        </div>

    </div>

</footer>

</body>

</html>
<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

function formatHomePrice(float $price): string
{
    return number_format($price, 0, ',', '.') . '₫';
}

/* =========================
   DANH MỤC
   ========================= */
$categories = [];

try {
    $categoryStmt = $pdo->query(
        "SELECT id, name
         FROM categories
         WHERE status = 'active'
           AND (parent_id IS NULL OR parent_id = 0)
         ORDER BY name ASC
         LIMIT 8"
    );

    $categories = $categoryStmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

/* =========================
   SẢN PHẨM NỔI BẬT
   ========================= */
$featuredProducts = [];

try {
    $productStmt = $pdo->query(
        "SELECT
            p.id,
            p.name,
            p.price,
            p.sale_price,
            p.stock,
            b.name AS brand_name,
            c.name AS category_name,
            pi.image_url
         FROM products p
         LEFT JOIN brands b ON b.id = p.brand_id
         LEFT JOIN categories c ON c.id = p.category_id
         LEFT JOIN product_images pi
            ON pi.product_id = p.id
           AND pi.is_primary = 1
         WHERE p.status = 'active'
         ORDER BY
            CASE WHEN p.sale_price IS NOT NULL THEN 0 ELSE 1 END,
            p.id DESC
         LIMIT 8"
    );

    $featuredProducts = $productStmt->fetchAll();
} catch (PDOException $e) {
    $featuredProducts = [];
}

/* =========================
   GIẢM GIÁ NỔI BẬT
   ========================= */
$saleProducts = [];

try {
    $saleStmt = $pdo->query(
        "SELECT
            p.id,
            p.name,
            p.price,
            p.sale_price,
            p.stock,
            b.name AS brand_name,
            pi.image_url
         FROM products p
         LEFT JOIN brands b ON b.id = p.brand_id
         LEFT JOIN product_images pi
            ON pi.product_id = p.id
           AND pi.is_primary = 1
         WHERE p.status = 'active'
           AND p.sale_price IS NOT NULL
           AND p.sale_price < p.price
         ORDER BY (p.price - p.sale_price) DESC, p.id DESC
         LIMIT 4"
    );

    $saleProducts = $saleStmt->fetchAll();
} catch (PDOException $e) {
    $saleProducts = [];
}

$user = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nhà Mình Mart - Mua nhanh, giá dễ chịu</title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --primary: #ff6b00;
            --primary-dark: #e45d00;
            --secondary: #ff9f1c;
            --danger: #e53935;
            --success: #148a4b;
            --text: #18212f;
            --muted: #697386;
            --border: #e8ebef;
            --bg: #f7f8fb;
            --white: #fff;
            --shadow: 0 18px 50px rgba(21, 30, 43, .08);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            color: var(--text);
            font-family: Inter, Arial, Helvetica, sans-serif;
            background:
                radial-gradient(circle at 8% 8%, rgba(255,107,0,.13), transparent 26%),
                radial-gradient(circle at 94% 10%, rgba(255,175,0,.12), transparent 23%),
                linear-gradient(135deg, #fff 0%, #f8f9fc 55%, #fff7ef 100%);
        }

        a {
            color: inherit;
        }

        .container {
            width: min(92%, 1240px);
            margin: auto;
        }

        /* HEADER */
        .header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 107, 0, .95);
            backdrop-filter: blur(15px);
            box-shadow: 0 8px 30px rgba(0,0,0,.10);
        }

        .header-inner {
            min-height: 76px;
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            text-decoration: none;
            font-size: 25px;
            font-weight: 950;
            letter-spacing: -.7px;
            white-space: nowrap;
        }

        .logo-mark {
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            border-radius: 13px;
            background: rgba(255,255,255,.18);
            font-size: 22px;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.18);
        }

        .search {
            flex: 1;
            max-width: 560px;
            margin-left: auto;
        }

        .search form {
            display: flex;
            background: #fff;
            border-radius: 13px;
            overflow: hidden;
            box-shadow: 0 10px 22px rgba(0,0,0,.10);
        }

        .search input {
            flex: 1;
            min-width: 0;
            border: 0;
            outline: 0;
            padding: 13px 15px;
            font: inherit;
        }

        .search button {
            border: 0;
            background: #20252d;
            color: #fff;
            font-weight: 850;
            padding: 0 17px;
            cursor: pointer;
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-left: auto;
        }

        .nav a {
            color: #fff;
            text-decoration: none;
            font-weight: 800;
            padding: 10px 11px;
            border-radius: 11px;
            transition: .2s;
            white-space: nowrap;
        }

        .nav a:hover {
            background: rgba(255,255,255,.16);
        }

        .nav .logout-link {
            background: rgba(0,0,0,.12);
        }

        .nav .logout-link:hover {
            background: rgba(0,0,0,.23);
        }

        /* HERO */
        .hero {
            position: relative;
            overflow: hidden;
            margin: 28px 0 25px;
            padding: 44px 46px;
            min-height: 360px;
            border-radius: 28px;
            color: #fff;
            background:
                radial-gradient(circle at 85% 22%, rgba(255,255,255,.18), transparent 23%),
                radial-gradient(circle at 20% 120%, rgba(255,255,255,.16), transparent 30%),
                linear-gradient(135deg, #ff6b00, #ff8a00 46%, #ffad2f);
            box-shadow: 0 28px 60px rgba(255,107,0,.24);
        }

        .hero:before,
        .hero:after {
            content: "";
            position: absolute;
            border-radius: 999px;
            filter: blur(4px);
            background: rgba(255,255,255,.12);
            animation: float 7s ease-in-out infinite;
        }

        .hero:before {
            width: 210px;
            height: 210px;
            right: 80px;
            top: -60px;
        }

        .hero:after {
            width: 130px;
            height: 130px;
            right: 275px;
            bottom: -45px;
            animation-delay: -3s;
        }

        @keyframes float {
            0%,100% { transform: translate3d(0,0,0) scale(1); }
            50% { transform: translate3d(12px,-14px,0) scale(1.06); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 650px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 11px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.18);
            font-size: 13px;
            font-weight: 900;
        }

        .hero h1 {
            margin: 16px 0 12px;
            font-size: clamp(37px, 5vw, 62px);
            line-height: 1.02;
            letter-spacing: -1.5px;
        }

        .hero p {
            margin: 0;
            max-width: 600px;
            line-height: 1.7;
            font-size: 17px;
            color: rgba(255,255,255,.92);
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 24px;
        }

        .hero-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 17px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 900;
        }

        .hero-btn.primary {
            background: #fff;
            color: #e85d00;
        }

        .hero-btn.secondary {
            background: rgba(0,0,0,.13);
            color: #fff;
            border: 1px solid rgba(255,255,255,.16);
        }

        /* BENEFITS */
        .benefits {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin: 0 0 32px;
        }

        .benefit {
            padding: 18px;
            border-radius: 18px;
            background: rgba(255,255,255,.88);
            border: 1px solid rgba(230,233,238,.8);
            box-shadow: 0 10px 30px rgba(22,29,40,.04);
        }

        .benefit-icon {
            font-size: 25px;
        }

        .benefit-title {
            margin-top: 7px;
            font-weight: 950;
        }

        .benefit-text {
            margin-top: 4px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.5;
        }

        /* SECTION */
        .section {
            margin: 34px 0;
        }

        .section-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 16px;
        }

        .section-title {
            margin: 0;
            font-size: 28px;
            letter-spacing: -.7px;
        }

        .section-desc {
            color: var(--muted);
            margin: 6px 0 0;
        }

        .see-all {
            color: var(--primary-dark);
            font-weight: 900;
            text-decoration: none;
            white-space: nowrap;
        }

        /* CATEGORY */
        .category-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
        }

        .category {
            min-height: 104px;
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 16px;
            border-radius: 18px;
            background: rgba(255,255,255,.9);
            text-decoration: none;
            border: 1px solid var(--border);
            transition: .2s;
        }

        .category:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 32px rgba(0,0,0,.07);
            border-color: #ffd0b0;
        }

        .category-icon {
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            border-radius: 15px;
            background: #fff1e7;
            font-size: 26px;
            flex: 0 0 auto;
        }

        .category-name {
            font-weight: 900;
            line-height: 1.35;
        }

        .category-sub {
            color: var(--muted);
            font-size: 12px;
            margin-top: 4px;
        }

        /* PRODUCT GRID */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .product-card {
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            background: #fff;
            border: 1px solid var(--border);
            text-decoration: none;
            transition: .2s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 38px rgba(0,0,0,.09);
        }

        .product-image-wrap {
            position: relative;
            padding: 12px;
            background: linear-gradient(145deg, #fff, #f6f7fb);
        }

        .product-image {
            width: 100%;
            height: 220px;
            display: block;
            object-fit: contain;
            border-radius: 12px;
        }

        .sale {
            position: absolute;
            left: 12px;
            top: 12px;
            padding: 6px 9px;
            border-radius: 999px;
            background: #fff0f0;
            color: var(--danger);
            border: 1px solid #ffd1d1;
            font-size: 11px;
            font-weight: 950;
        }

        .stock {
            position: absolute;
            right: 12px;
            top: 12px;
            padding: 6px 8px;
            border-radius: 999px;
            background: rgba(255,255,255,.92);
            color: var(--success);
            font-size: 11px;
            font-weight: 900;
        }

        .product-info {
            padding: 14px;
        }

        .product-category {
            color: var(--muted);
            font-size: 12px;
        }

        .product-name {
            min-height: 42px;
            margin-top: 5px;
            font-size: 15px;
            font-weight: 950;
            line-height: 1.4;
        }

        .product-brand {
            margin-top: 4px;
            color: #8a93a3;
            font-size: 12px;
        }

        .product-price {
            margin-top: 10px;
            color: var(--danger);
            font-size: 18px;
            font-weight: 950;
        }

        .product-old-price {
            color: #9ca3af;
            margin-left: 5px;
            font-size: 12px;
            text-decoration: line-through;
            font-weight: 650;
        }

        /* PROMO */
        .promo {
            position: relative;
            overflow: hidden;
            padding: 25px;
            border-radius: 22px;
            background: linear-gradient(135deg, #1d2430, #2e3747);
            color: #fff;
            box-shadow: 0 22px 45px rgba(20,28,40,.18);
        }

        .promo h2 {
            margin: 0;
            font-size: 28px;
        }

        .promo p {
            color: rgba(255,255,255,.76);
            max-width: 720px;
            line-height: 1.65;
        }

        .promo-code {
            display: inline-flex;
            padding: 9px 13px;
            border-radius: 10px;
            background: rgba(255,255,255,.08);
            border: 1px dashed rgba(255,255,255,.28);
            font-weight: 950;
            letter-spacing: 1px;
        }

        /* EMPTY */
        .empty {
            padding: 34px;
            text-align: center;
            border: 1px dashed #d8dde5;
            border-radius: 18px;
            color: var(--muted);
            background: rgba(255,255,255,.72);
        }

        /* FOOTER */
        footer {
            margin-top: 48px;
            padding: 34px 0 46px;
            color: #667085;
            border-top: 1px solid rgba(225,229,236,.9);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr;
            gap: 25px;
        }

        .footer-brand {
            color: var(--text);
            font-weight: 950;
            font-size: 22px;
        }

        .footer p {
            line-height: 1.65;
        }

        .footer-links {
            display: grid;
            gap: 8px;
        }

        .footer-links a {
            color: #667085;
            text-decoration: none;
        }

        .footer-links a:hover {
            color: var(--primary);
        }

        .copyright {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid var(--border);
            font-size: 13px;
        }

        /* RESPONSIVE */
        @media (max-width: 1060px) {
            .header-inner {
                flex-wrap: wrap;
                padding: 10px 0;
            }

            .search {
                order: 3;
                width: 100%;
                max-width: none;
                margin: 0;
            }

            .benefits,
            .category-grid,
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 680px) {
            .container {
                width: 94%;
            }

            .nav {
                width: 100%;
                overflow-x: auto;
                padding-bottom: 2px;
            }

            .hero {
                padding: 31px 23px;
                min-height: 330px;
            }

            .benefits,
            .category-grid,
            .product-grid,
            .footer-grid {
                grid-template-columns: 1fr;
            }

            .section-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .product-image {
                height: 240px;
            }
        }
    </style>
</head>

<body>

<header class="header">
    <div class="container header-inner">

        <a href="index.php" class="logo">
            <span class="logo-mark">🏪</span>
            <span>Nhà Mình Mart</span>
        </a>

        <div class="search">
            <form method="GET" action="products.php">
                <input
                    type="text"
                    name="keyword"
                    placeholder="Tìm Coca, mì, nước, snack..."
                >
                <button type="submit">🔎 Tìm</button>
            </form>
        </div>

        <nav class="nav">
            <a href="products.php">🛍️ Sản phẩm</a>
            <a href="cart.php">🛒 Giỏ hàng</a>

            <?php if ($user): ?>
                <a href="orders.php">📦 Đơn hàng</a>
                <a href="logout.php" class="logout-link">🚪 Đăng xuất</a>
            <?php else: ?>
                <a href="login.php">🔐 Đăng nhập</a>
            <?php endif; ?>
        </nav>

    </div>
</header>

<main class="container">

    <section class="hero">
        <div class="hero-content">
            <span class="eyebrow">✨ Cửa hàng tiện lợi của nhà mình</span>

            <h1>
                Mua nhanh.<br>
                Giá dễ chịu.<br>
                Giao tận nơi. 🛵
            </h1>

            <p>
                Chào mừng đến với <strong>Nhà Mình Mart</strong> —
                nơi bạn có thể tìm thấy đồ uống, mì, bánh, snack
                và những sản phẩm thiết yếu mỗi ngày.
            </p>

            <div class="hero-actions">
                <a href="products.php" class="hero-btn primary">
                    🛍️ Mua sắm ngay
                </a>

                <a href="#sale" class="hero-btn secondary">
                    🔥 Xem khuyến mãi
                </a>
            </div>
        </div>
    </section>

    <section class="benefits">
        <div class="benefit">
            <div class="benefit-icon">🚚</div>
            <div class="benefit-title">Giao hàng tiện lợi</div>
            <div class="benefit-text">Đặt hàng nhanh, theo dõi đơn dễ dàng.</div>
        </div>

        <div class="benefit">
            <div class="benefit-icon">💰</div>
            <div class="benefit-title">Giá dễ chịu</div>
            <div class="benefit-text">Nhiều sản phẩm có giá tốt và ưu đãi.</div>
        </div>

        <div class="benefit">
            <div class="benefit-icon">🎟️</div>
            <div class="benefit-title">Voucher hấp dẫn</div>
            <div class="benefit-text">Áp mã giảm giá ngay ở bước thanh toán.</div>
        </div>

        <div class="benefit">
            <div class="benefit-icon">🔒</div>
            <div class="benefit-title">Mua hàng an tâm</div>
            <div class="benefit-text">Tài khoản và đơn hàng được quản lý rõ ràng.</div>
        </div>
    </section>

    <!-- CATEGORIES -->
    <section class="section">

        <div class="section-head">
            <div>
                <h2 class="section-title">📚 Mua theo danh mục</h2>
                <p class="section-desc">Chọn nhanh nhóm sản phẩm bạn cần.</p>
            </div>

            <a href="products.php" class="see-all">
                Xem tất cả →
            </a>
        </div>

        <?php if (!empty($categories)): ?>

            <div class="category-grid">

                <?php
                $categoryIcons = ['🥤', '🍜', '🍪', '🍟', '🧴', '🧃', '🍫', '🛒'];
                ?>

                <?php foreach ($categories as $index => $category): ?>
                    <a
                        href="products.php?category_id=<?= (int) $category['id'] ?>"
                        class="category"
                    >
                        <div class="category-icon">
                            <?= $categoryIcons[$index % count($categoryIcons)] ?>
                        </div>

                        <div>
                            <div class="category-name">
                                <?= htmlspecialchars(
                                    $category['name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                            <div class="category-sub">
                                Xem sản phẩm →
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="empty">
                Chưa có danh mục nào.
            </div>

        <?php endif; ?>

    </section>

    <!-- FEATURED -->
    <section class="section">

        <div class="section-head">
            <div>
                <h2 class="section-title">⭐ Sản phẩm nổi bật</h2>
                <p class="section-desc">
                    Những sản phẩm được đưa lên trước để bạn dễ lựa chọn.
                </p>
            </div>

            <a href="products.php" class="see-all">
                Xem thêm →
            </a>
        </div>

        <?php if (!empty($featuredProducts)): ?>

            <div class="product-grid">

                <?php foreach ($featuredProducts as $product): ?>

                    <?php
                    $imageUrl = $product['image_url']
                        ?: 'https://placehold.co/700x700/png?text=' . urlencode($product['name']);

                    $price = $product['sale_price'] !== null
                        ? (float) $product['sale_price']
                        : (float) $product['price'];

                    $salePercent = 0;

                    if (
                        $product['sale_price'] !== null
                        && (float) $product['price'] > 0
                    ) {
                        $salePercent = (int) round(
                            (1 - (
                                (float) $product['sale_price']
                                / (float) $product['price']
                            )) * 100
                        );
                    }
                    ?>

                    <a
                        href="product_detail.php?id=<?= (int) $product['id'] ?>"
                        class="product-card"
                    >
                        <div class="product-image-wrap">

                            <?php if ($salePercent > 0): ?>
                                <div class="sale">
                                    🔥 -<?= $salePercent ?>%
                                </div>
                            <?php endif; ?>

                            <?php if ((int) $product['stock'] > 0): ?>
                                <div class="stock">
                                    Còn hàng
                                </div>
                            <?php endif; ?>

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
                                class="product-image"
                            >
                        </div>

                        <div class="product-info">

                            <div class="product-category">
                                <?= htmlspecialchars(
                                    $product['category_name'] ?? 'Sản phẩm',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                            <div class="product-name">
                                <?= htmlspecialchars(
                                    $product['name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                            <div class="product-brand">
                                <?= htmlspecialchars(
                                    $product['brand_name'] ?? 'Không có thương hiệu',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                            <div class="product-price">
                                <?= formatHomePrice($price) ?>

                                <?php if ($product['sale_price'] !== null): ?>
                                    <span class="product-old-price">
                                        <?= formatHomePrice(
                                            (float) $product['price']
                                        ) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                        </div>
                    </a>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="empty">
                Chưa có sản phẩm để hiển thị.
            </div>

        <?php endif; ?>

    </section>

    <!-- SALE -->
    <section class="section" id="sale">

        <div class="promo">
            <h2>🔥 Đang có ưu đãi tại Nhà Mình Mart</h2>

            <p>
                Nhập mã voucher ở trang thanh toán để nhận ưu đãi.
                Chương trình được quản lý trực tiếp từ hệ thống quản trị.
            </p>

            <span class="promo-code">
                🎟️ GIAM10
            </span>
        </div>

    </section>

    <?php if (!empty($saleProducts)): ?>

        <section class="section">

            <div class="section-head">
                <div>
                    <h2 class="section-title">🔥 Đang giảm giá</h2>
                    <p class="section-desc">
                        Chốt đơn sớm để không bỏ lỡ giá tốt.
                    </p>
                </div>

                <a href="products.php" class="see-all">
                    Xem tất cả →
                </a>
            </div>

            <div class="product-grid">

                <?php foreach ($saleProducts as $product): ?>

                    <?php
                    $imageUrl = $product['image_url']
                        ?: 'https://placehold.co/700x700/png?text=' . urlencode($product['name']);
                    ?>

                    <a
                        href="product_detail.php?id=<?= (int) $product['id'] ?>"
                        class="product-card"
                    >
                        <div class="product-image-wrap">

                            <div class="sale">
                                🔥 SALE
                            </div>

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
                                class="product-image"
                            >

                        </div>

                        <div class="product-info">

                            <div class="product-name">
                                <?= htmlspecialchars(
                                    $product['name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                            <div class="product-brand">
                                <?= htmlspecialchars(
                                    $product['brand_name'] ?? 'Không có thương hiệu',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                            <div class="product-price">
                                <?= formatHomePrice((float) $product['sale_price']) ?>

                                <span class="product-old-price">
                                    <?= formatHomePrice((float) $product['price']) ?>
                                </span>
                            </div>

                        </div>
                    </a>

                <?php endforeach; ?>

            </div>

        </section>

    <?php endif; ?>

</main>

<footer>
    <div class="container">

        <div class="footer-grid">

            <div>
                <div class="footer-brand">🏪 Nhà Mình Mart</div>

                <p>
                    Mua nhanh – Giá dễ chịu – Giao tận nơi.
                    Cửa hàng tiện lợi online dành cho mọi nhu cầu hằng ngày.
                </p>
            </div>

            <div>
                <strong>Khách hàng</strong>

                <div class="footer-links" style="margin-top:10px;">
                    <a href="products.php">Sản phẩm</a>
                    <a href="cart.php">Giỏ hàng</a>
                    <a href="orders.php">Đơn hàng</a>
                    <a href="login.php">Đăng nhập</a>
                </div>
            </div>

            <div>
                <strong>Hỗ trợ</strong>

                <div class="footer-links" style="margin-top:10px;">
                    <a href="products.php">Tra cứu sản phẩm</a>
                    <a href="checkout.php">Thanh toán</a>
                    <a href="orders.php">Theo dõi đơn</a>
                </div>
            </div>

        </div>

        <div class="copyright">
            © <?= date('Y') ?> Nhà Mình Mart · Website bán hàng kiểu cửa hàng tiện lợi.
        </div>

    </div>
</footer>

</body>
</html>

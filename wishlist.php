<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user']['id'];

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function money(float $price): string
{
    return number_format($price, 0, ',', '.') . '₫';
}

$items = [];
$error = '';

try {
    $stmt = $pdo->prepare(
        "SELECT
            w.id AS wishlist_id,
            p.id,
            p.name,
            p.price,
            p.sale_price,
            p.stock,
            b.name AS brand_name,
            c.name AS category_name,
            COALESCE(
                (
                    SELECT pi.image_url
                    FROM product_images pi
                    WHERE pi.product_id = p.id
                    ORDER BY pi.is_primary DESC, pi.id ASC
                    LIMIT 1
                ),
                ''
            ) AS image_url,
            CASE
                WHEN p.sale_price IS NOT NULL
                     AND p.sale_price < p.price
                THEN p.sale_price
                ELSE p.price
            END AS display_price
         FROM wishlists w
         INNER JOIN products p
             ON p.id = w.product_id
         LEFT JOIN brands b
             ON b.id = p.brand_id
         LEFT JOIN categories c
             ON c.id = p.category_id
         WHERE w.user_id = :user_id
           AND p.status = 'active'
         ORDER BY w.created_at DESC"
    );

    $stmt->execute(['user_id' => $userId]);
    $items = $stmt->fetchAll();

} catch (PDOException $e) {
    $error = 'Chưa khởi tạo bảng yêu thích hoặc database đang có lỗi.';
}

$userName = $_SESSION['user']['name'] ?? 'Khách hàng';

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Yêu thích - Nhà Mình Mart</title>

    <style>
        * { box-sizing: border-box; }

        :root {
            --primary: #ff6b00;
            --primary-dark: #e85d00;
            --text: #18212f;
            --muted: #667085;
            --border: #e7e9ee;
            --background: #f7f8fb;
            --white: #fff;
            --danger: #dc2626;
            --success: #15803d;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: var(--background);
            color: var(--text);
        }

        .container {
            width: min(94%, 1280px);
            margin: auto;
        }

        .header {
            position: sticky;
            top: 0;
            z-index: 10;
            background: rgba(255,255,255,.97);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(10px);
        }

        .header-inner {
            min-height: 76px;
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 20px;
            align-items: center;
        }

        .logo {
            text-decoration: none;
            font-size: 22px;
            font-weight: 950;
        }

        .search form {
            display: flex;
            gap: 8px;
        }

        .search input {
            width: 100%;
            min-width: 0;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 11px;
            outline: none;
            font-size: 14px;
        }

        .search button {
            border: 0;
            border-radius: 11px;
            padding: 0 17px;
            background: var(--primary);
            color: #fff;
            font-weight: 900;
            cursor: pointer;
        }

        .nav {
            display: flex;
            gap: 7px;
            white-space: nowrap;
        }

        .nav a {
            text-decoration: none;
            padding: 9px 11px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 900;
        }

        .nav a:hover,
        .nav a.active {
            background: #fff1e8;
            color: var(--primary-dark);
        }

        .page {
            padding: 32px 0 60px;
        }

        .title-row {
            margin-bottom: 22px;
        }

        h1 {
            margin: 0 0 7px;
            font-size: 34px;
        }

        .subtitle {
            margin: 0;
            color: var(--muted);
        }

        .message {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 12px;
            background: #fff1f2;
            color: #9f1239;
            border: 1px solid #fecdd3;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .card {
            position: relative;
            overflow: hidden;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            transition: .18s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(20,30,45,.08);
            border-color: #ffcab0;
        }

        .image-wrap {
            height: 240px;
            background: #fafafa;
            display: grid;
            place-items: center;
            position: relative;
        }

        .image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 14px;
        }

        .remove {
            position: absolute;
            right: 12px;
            top: 12px;
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border: 0;
            border-radius: 50%;
            background: #fff;
            color: var(--danger);
            box-shadow: 0 7px 20px rgba(20,30,45,.12);
            cursor: pointer;
            font-size: 18px;
        }

        .remove:hover {
            background: #fff1f2;
        }

        .info {
            padding: 15px;
        }

        .category {
            margin-bottom: 6px;
            color: var(--primary);
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .name {
            min-height: 43px;
            font-size: 15px;
            line-height: 1.4;
            font-weight: 950;
        }

        .brand {
            margin-top: 5px;
            color: var(--muted);
            font-size: 12px;
        }

        .price-row {
            display: flex;
            align-items: baseline;
            gap: 7px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .price {
            color: var(--danger);
            font-size: 18px;
            font-weight: 950;
        }

        .old-price {
            color: #9ca3af;
            text-decoration: line-through;
            font-size: 12px;
        }

        .stock {
            margin-top: 7px;
            font-size: 12px;
            font-weight: 800;
        }

        .stock.ok { color: var(--success); }
        .stock.out { color: #6b7280; }

        .detail {
            display: block;
            margin-top: 13px;
            padding: 11px;
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            text-align: center;
            text-decoration: none;
            font-size: 13px;
            font-weight: 900;
        }

        .empty {
            padding: 70px 20px;
            text-align: center;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
        }

        .empty-icon {
            font-size: 52px;
        }

        .empty h2 {
            margin: 8px 0;
        }

        .empty p {
            color: var(--muted);
        }

        .shop {
            display: inline-flex;
            margin-top: 10px;
            padding: 12px 18px;
            border-radius: 11px;
            background: var(--primary);
            color: #fff;
            text-decoration: none;
            font-weight: 900;
        }

        @media (max-width: 1100px) {
            .header-inner {
                grid-template-columns: 1fr;
                padding: 12px 0;
            }

            .nav {
                overflow-x: auto;
            }

            .grid {
                grid-template-columns: repeat(3, minmax(0,1fr));
            }
        }

        @media (max-width: 800px) {
            .grid {
                grid-template-columns: repeat(2, minmax(0,1fr));
            }
        }

        @media (max-width: 520px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .image-wrap {
                height: 270px;
            }
        }
    </style>
</head>

<body>

<header class="header">
    <div class="container header-inner">

        <a href="index.php" class="logo">
            🏪 Nhà Mình Mart
        </a>

        <div class="search">
            <form method="GET" action="products.php">
                <input
                    type="text"
                    name="keyword"
                    placeholder="Tìm sản phẩm..."
                >
                <button type="submit">🔎 Tìm</button>
            </form>
        </div>

        <nav class="nav">
            <a href="index.php">🏠 Trang chủ</a>
            <a href="products.php">🛍️ Sản phẩm</a>
            <a href="wishlist.php" class="active">❤️ Yêu thích</a>
            <a href="cart.php">🛒 Giỏ hàng</a>
            <a href="orders.php">📦 Đơn hàng</a>
            <a href="logout.php">🚪 Đăng xuất</a>
        </nav>

    </div>
</header>

<main class="container page">

    <div class="title-row">
        <h1>❤️ Sản phẩm yêu thích</h1>
        <p class="subtitle">
            Xin chào <?= e($userName) ?> · <?= count($items) ?> sản phẩm đã lưu.
        </p>
    </div>

    <?php if ($error !== ''): ?>

        <div class="message">
            ❌ <?= e($error) ?>
        </div>

    <?php elseif (empty($items)): ?>

        <div class="empty">
            <div class="empty-icon">❤️</div>

            <h2>Chưa có sản phẩm yêu thích</h2>

            <p>
                Hãy lưu những sản phẩm bạn đang quan tâm để quay lại mua nhanh hơn.
            </p>

            <a href="products.php" class="shop">
                🛍️ Khám phá sản phẩm
            </a>
        </div>

    <?php else: ?>

        <div class="grid">

            <?php foreach ($items as $item): ?>

                <?php
                $image = trim((string) $item['image_url']);

                if ($image === '') {
                    $image =
                        'https://placehold.co/700x700/png?text='
                        . urlencode($item['name']);
                }

                $price = (float) $item['display_price'];
                $original = (float) $item['price'];

                $isSale =
                    $item['sale_price'] !== null
                    && (float) $item['sale_price'] < $original;
                ?>

                <article class="card">

                    <div class="image-wrap">

                        <img
                            class="image"
                            src="<?= e($image) ?>"
                            alt="<?= e($item['name']) ?>"
                            loading="lazy"
                            onerror="this.onerror=null;this.src='https://placehold.co/700x700/png?text=No+Image';"
                        >

                        <form method="POST" action="wishlist_action.php">

                            <input
                                type="hidden"
                                name="action"
                                value="remove"
                            >

                            <input
                                type="hidden"
                                name="product_id"
                                value="<?= (int) $item['id'] ?>"
                            >

                            <input
                                type="hidden"
                                name="redirect"
                                value="wishlist.php"
                            >

                            <button
                                type="submit"
                                class="remove"
                                title="Bỏ khỏi yêu thích"
                                aria-label="Bỏ khỏi yêu thích"
                            >
                                ♥
                            </button>

                        </form>

                    </div>

                    <div class="info">

                        <div class="category">
                            <?= e($item['category_name'] ?? 'Sản phẩm') ?>
                        </div>

                        <div class="name">
                            <?= e($item['name']) ?>
                        </div>

                        <div class="brand">
                            <?= e($item['brand_name'] ?? 'Không có thương hiệu') ?>
                        </div>

                        <div class="price-row">

                            <span class="price">
                                <?= money($price) ?>
                            </span>

                            <?php if ($isSale): ?>

                                <span class="old-price">
                                    <?= money($original) ?>
                                </span>

                            <?php endif; ?>

                        </div>

                        <div
                            class="stock <?= (int) $item['stock'] > 0 ? 'ok' : 'out' ?>"
                        >
                            <?= (int) $item['stock'] > 0
                                ? '✅ Còn hàng'
                                : '❌ Hết hàng' ?>
                        </div>

                        <a
                            class="detail"
                            href="product_detail.php?id=<?= (int) $item['id'] ?>"
                        >
                            Xem sản phẩm →
                        </a>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</main>

</body>
</html>

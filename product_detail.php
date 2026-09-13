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
            * { box-sizing: border-box; }
            body {
                margin: 0;
                font-family: Arial, sans-serif;
                background: #f7f8fb;
                color: #222;
            }
            .container {
                width: min(92%, 1200px);
                margin: auto;
            }
            .error-box {
                background: white;
                margin: 80px auto;
                max-width: 620px;
                padding: 44px;
                text-align: center;
                border-radius: 20px;
                box-shadow: 0 18px 50px rgba(0, 0, 0, .08);
            }
            .error-box h1 { margin: 0 0 12px; }
            .back-btn {
                display: inline-block;
                margin-top: 18px;
                padding: 12px 20px;
                background: #ff6b00;
                color: white;
                text-decoration: none;
                border-radius: 10px;
                font-weight: 700;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="error-box">
                <div style="font-size:56px;">😢</div>
                <h1>Không tìm thấy sản phẩm</h1>
                <p>Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.</p>
                <a href="products.php" class="back-btn">← Quay lại sản phẩm</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

/* =========================
   HÌNH ẢNH
   ========================= */
$images = $product['images'] ?? [];
$mainImage = null;

foreach ($images as $image) {
    if ((int) ($image['is_primary'] ?? 0) === 1) {
        $mainImage = $image['image_url'];
        break;
    }
}

if (!$mainImage && !empty($images)) {
    $mainImage = $images[0]['image_url'];
}

if (!$mainImage) {
    $mainImage = 'https://placehold.co/800x800/png?text=' . urlencode($product['name']);
}

/* =========================
   GIÁ
   ========================= */
$displayPrice = $product['sale_price'] !== null
    ? (float) $product['sale_price']
    : (float) $product['price'];

$salePercent = 0;

if ($product['sale_price'] !== null && (float) $product['price'] > 0) {
    $salePercent = (int) round(
        (1 - ((float) $product['sale_price'] / (float) $product['price'])) * 100
    );
}

function formatDetailPrice(float $price): string
{
    return number_format($price, 0, ',', '.') . '₫';
}

/* =========================
   REVIEW DATA
   ========================= */
$reviewCount = 0;
$averageRating = 0;
$reviews = [];
$userReview = null;
$canReview = false;

try {
    $statsStmt = $pdo->prepare(
        "SELECT
            COUNT(*) AS review_count,
            COALESCE(AVG(rating), 0) AS average_rating
         FROM reviews
         WHERE product_id = :product_id
           AND status = 'approved'"
    );
    $statsStmt->execute(['product_id' => $productId]);
    $stats = $statsStmt->fetch();

    if ($stats) {
        $reviewCount = (int) ($stats['review_count'] ?? 0);
        $averageRating = round((float) ($stats['average_rating'] ?? 0), 1);
    }

    $reviewStmt = $pdo->prepare(
        "SELECT
            r.id,
            r.rating,
            r.comment,
            r.created_at,
            u.name AS user_name
         FROM reviews r
         INNER JOIN users u ON u.id = r.user_id
         WHERE r.product_id = :product_id
           AND r.status = 'approved'
         ORDER BY r.created_at DESC"
    );
    $reviewStmt->execute(['product_id' => $productId]);
    $reviews = $reviewStmt->fetchAll();

    if (isset($_SESSION['user']['id'])) {
        $userId = (int) $_SESSION['user']['id'];

        $purchaseStmt = $pdo->prepare(
            "SELECT 1
             FROM order_items oi
             INNER JOIN orders o ON o.id = oi.order_id
             WHERE o.user_id = :user_id
               AND oi.product_id = :product_id
               AND o.status = 'completed'
             LIMIT 1"
        );
        $purchaseStmt->execute([
            'user_id' => $userId,
            'product_id' => $productId
        ]);
        $canReview = (bool) $purchaseStmt->fetchColumn();

        $userReviewStmt = $pdo->prepare(
            "SELECT id, rating, comment, status, created_at, updated_at
             FROM reviews
             WHERE user_id = :user_id
               AND product_id = :product_id
             LIMIT 1"
        );
        $userReviewStmt->execute([
            'user_id' => $userId,
            'product_id' => $productId
        ]);
        $userReview = $userReviewStmt->fetch() ?: null;
    }
} catch (PDOException $e) {
    // Không để lỗi review làm hỏng toàn bộ trang sản phẩm.
    $reviewCount = 0;
    $averageRating = 0;
    $reviews = [];
    $userReview = null;
    $canReview = false;
}

/* =========================
   SẢN PHẨM LIÊN QUAN
   ========================= */
$relatedProducts = [];

try {
    $relatedStmt = $pdo->prepare(
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
           AND p.category_id = :category_id
           AND p.id <> :product_id
         ORDER BY p.id DESC
         LIMIT 4"
    );

    $relatedStmt->execute([
        'category_id' => (int) ($product['category_id'] ?? 0),
        'product_id' => $productId
    ]);

    $relatedProducts = $relatedStmt->fetchAll();
} catch (PDOException $e) {
    $relatedProducts = [];
}

function renderStars(float $rating): string
{
    $html = '';

    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= round($rating) ? '★' : '☆';
    }

    return $html;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
        - <?= SITE_NAME ?>
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --primary: #ff6b00;
            --primary-dark: #e85d00;
            --primary-soft: #fff4ea;
            --danger: #e53935;
            --success: #1f8a4c;
            --text: #1f2937;
            --muted: #6b7280;
            --border: #e5e7eb;
            --bg: #f6f7fb;
            --card: #ffffff;
            --shadow: 0 18px 45px rgba(31, 41, 55, .08);
        }

        body {
            margin: 0;
            font-family: Inter, Arial, Helvetica, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 10% 10%, rgba(255, 107, 0, .12), transparent 28%),
                radial-gradient(circle at 90% 15%, rgba(255, 193, 7, .10), transparent 24%),
                linear-gradient(135deg, #fff 0%, #f7f8fc 48%, #fff8f1 100%);
            min-height: 100vh;
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
            z-index: 50;
            backdrop-filter: blur(16px);
            background: rgba(255, 107, 0, .94);
            color: #fff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .10);
        }

        .header-inner {
            min-height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .logo {
            text-decoration: none;
            font-size: 24px;
            font-weight: 900;
            white-space: nowrap;
            letter-spacing: -.5px;
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .nav a {
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            padding: 10px 12px;
            border-radius: 10px;
            transition: .2s;
        }

        .nav a:hover {
            background: rgba(255,255,255,.16);
        }

        /* BREADCRUMB */
        .breadcrumb {
            margin-top: 24px;
            font-size: 14px;
            color: var(--muted);
        }

        .breadcrumb a {
            text-decoration: none;
            color: var(--primary);
            font-weight: 700;
        }

        /* MAIN PRODUCT */
        .product-detail {
            margin: 18px 0 28px;
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, .95fr);
            gap: 34px;
            padding: 28px;
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(255,255,255,.8);
            border-radius: 24px;
            box-shadow: var(--shadow);
        }

        .gallery {
            min-width: 0;
        }

        .main-image-wrap {
            position: relative;
            background:
                linear-gradient(145deg, #ffffff 0%, #f6f7fb 100%);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 18px;
            overflow: hidden;
        }

        .main-image {
            width: 100%;
            height: 500px;
            object-fit: contain;
            display: block;
            border-radius: 14px;
            transition: transform .3s ease;
        }

        .main-image-wrap:hover .main-image {
            transform: scale(1.02);
        }

        .sale-corner {
            position: absolute;
            top: 16px;
            left: 16px;
            background: var(--danger);
            color: #fff;
            padding: 8px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
            box-shadow: 0 8px 20px rgba(229, 57, 53, .24);
        }

        .thumbnails {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 14px;
        }

        .thumbnail {
            width: 78px;
            height: 78px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid #ddd;
            background: #fafafa;
            cursor: pointer;
            transition: .2s;
        }

        .thumbnail:hover,
        .thumbnail.active {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(255,107,0,.14);
        }

        /* INFO */
        .information {
            padding: 10px 0;
        }

        .category {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--primary-soft);
            color: var(--primary-dark);
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 14px;
        }

        .product-name {
            font-size: clamp(28px, 3vw, 40px);
            line-height: 1.15;
            margin: 0 0 10px;
            letter-spacing: -.8px;
        }

        .brand {
            color: var(--muted);
            margin-bottom: 18px;
            font-size: 15px;
        }

        .quick-rating {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .quick-rating .stars {
            color: #f59e0b;
            letter-spacing: 1px;
            font-size: 21px;
        }

        .rating-number {
            font-weight: 900;
        }

        .rating-link {
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 700;
        }

        .price {
            display: flex;
            align-items: baseline;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 6px;
        }

        .current-price {
            color: var(--danger);
            font-size: 34px;
            font-weight: 950;
        }

        .old-price {
            color: #9ca3af;
            text-decoration: line-through;
            font-size: 17px;
        }

        .sale-badge {
            display: inline-flex;
            padding: 5px 9px;
            background: #fff1f1;
            color: var(--danger);
            border: 1px solid #ffd6d6;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
        }

        .stock {
            margin: 15px 0;
            font-size: 15px;
            font-weight: 700;
        }

        .stock.available {
            color: var(--success);
        }

        .stock.empty {
            color: var(--danger);
        }

        .description {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            line-height: 1.75;
            color: #4b5563;
        }

        .description h3 {
            color: var(--text);
            margin: 0 0 8px;
            font-size: 17px;
        }

        /* QUANTITY */
        .quantity-area {
            margin-top: 24px;
        }

        .quantity-title {
            font-weight: 900;
            margin-bottom: 10px;
        }

        .quantity-input {
            display: inline-flex;
            align-items: center;
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }

        .quantity-input button {
            width: 44px;
            height: 44px;
            border: 0;
            background: #fff;
            cursor: pointer;
            font-size: 22px;
            transition: .2s;
        }

        .quantity-input button:hover {
            background: var(--primary);
            color: white;
        }

        .quantity-input input {
            width: 72px;
            height: 44px;
            border: 0;
            border-left: 1px solid var(--border);
            border-right: 1px solid var(--border);
            text-align: center;
            font-size: 17px;
            font-weight: 800;
            outline: none;
        }

        .cart-form {
            margin-top: 16px;
        }

        .add-cart {
            width: 100%;
            border: 0;
            padding: 16px 18px;
            border-radius: 14px;
            background: linear-gradient(135deg, #ff7a18, #ff5a00);
            color: #fff;
            font-size: 17px;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 0 14px 26px rgba(255, 107, 0, .22);
            transition: .2s;
        }

        .add-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 30px rgba(255, 107, 0, .28);
        }

        .disabled {
            background: #9ca3af !important;
            cursor: not-allowed;
            box-shadow: none;
        }

        .login-notice {
            margin-top: 14px;
            padding: 13px 14px;
            background: var(--primary-soft);
            color: var(--primary-dark);
            border-radius: 12px;
            font-size: 14px;
            text-align: center;
            border: 1px solid #ffd8bd;
        }

        .login-notice a {
            font-weight: 900;
        }

        /* SECTION */
        .section {
            margin: 28px 0;
            background: rgba(255,255,255,.94);
            border-radius: 22px;
            box-shadow: var(--shadow);
            padding: 28px;
        }

        .section-title {
            margin: 0;
            font-size: 25px;
            letter-spacing: -.4px;
        }

        .section-subtitle {
            margin: 7px 0 0;
            color: var(--muted);
        }

        /* REVIEWS SUMMARY */
        .review-summary {
            margin-top: 20px;
            display: grid;
            grid-template-columns: 230px 1fr;
            gap: 20px;
        }

        .review-score {
            border: 1px solid var(--border);
            border-radius: 18px;
            background: linear-gradient(145deg, #fffaf5, #fff);
            padding: 22px;
            text-align: center;
        }

        .score-number {
            font-size: 52px;
            line-height: 1;
            font-weight: 950;
            color: #111827;
        }

        .score-stars {
            margin-top: 8px;
            font-size: 24px;
            color: #f59e0b;
            letter-spacing: 2px;
        }

        .score-count {
            margin-top: 7px;
            color: var(--muted);
            font-size: 14px;
        }

        .review-guide {
            display: grid;
            gap: 9px;
            align-content: center;
        }

        .rating-row {
            display: grid;
            grid-template-columns: 42px 1fr 44px;
            gap: 10px;
            align-items: center;
            font-size: 13px;
            color: var(--muted);
        }

        .bar {
            height: 9px;
            border-radius: 999px;
            background: #edf0f3;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #ffb347, #ff7a18);
        }

        /* REVIEW FORM */
        .review-form {
            margin-top: 24px;
            border: 1px solid #ffd9ba;
            background: #fffaf5;
            border-radius: 18px;
            padding: 20px;
        }

        .review-form h3 {
            margin: 0 0 6px;
        }

        .review-form p {
            margin: 0 0 16px;
            color: var(--muted);
            font-size: 14px;
        }

        .rating-picker {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 4px;
            margin-bottom: 14px;
        }

        .rating-picker input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .rating-picker label {
            cursor: pointer;
            font-size: 30px;
            color: #d1d5db;
            transition: .15s;
        }

        .rating-picker label:hover,
        .rating-picker label:hover ~ label,
        .rating-picker input:checked ~ label {
            color: #f59e0b;
        }

        .review-textarea {
            width: 100%;
            min-height: 120px;
            resize: vertical;
            padding: 13px 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            outline: none;
            font: inherit;
        }

        .review-textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255,107,0,.10);
        }

        .review-submit {
            margin-top: 12px;
            border: 0;
            padding: 12px 18px;
            border-radius: 11px;
            background: var(--primary);
            color: white;
            font-weight: 900;
            cursor: pointer;
        }

        .review-submit:hover {
            background: var(--primary-dark);
        }

        .review-pending {
            margin-top: 14px;
            padding: 13px 14px;
            border-radius: 12px;
            background: #fff7dd;
            border: 1px solid #f5df94;
            color: #775c00;
            font-size: 14px;
        }

        .review-login {
            margin-top: 22px;
            padding: 18px;
            text-align: center;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            color: var(--muted);
        }

        .review-login a {
            color: var(--primary-dark);
            font-weight: 900;
            text-decoration: none;
        }

        /* REVIEW LIST */
        .review-list {
            margin-top: 24px;
            display: grid;
            gap: 14px;
        }

        .review-item {
            padding: 18px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: #fff;
        }

        .review-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
        }

        .review-user {
            font-weight: 900;
        }

        .review-date {
            color: #9ca3af;
            font-size: 12px;
            margin-top: 4px;
        }

        .review-stars {
            color: #f59e0b;
            letter-spacing: 1px;
            white-space: nowrap;
        }

        .review-comment {
            margin: 12px 0 0;
            line-height: 1.65;
            color: #4b5563;
            white-space: pre-line;
        }

        .empty-reviews {
            padding: 28px 16px;
            text-align: center;
            color: var(--muted);
            background: #fafafa;
            border-radius: 14px;
            border: 1px dashed var(--border);
        }

        /* RELATED */
        .related-grid {
            margin-top: 20px;
            display: grid;
            grid-template-columns: repeat(4, minmax(0,1fr));
            gap: 16px;
        }

        .related-card {
            overflow: hidden;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 17px;
            text-decoration: none;
            transition: .2s;
        }

        .related-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 34px rgba(0,0,0,.08);
        }

        .related-image {
            width: 100%;
            height: 190px;
            display: block;
            object-fit: contain;
            background: #f8f9fb;
        }

        .related-info {
            padding: 14px;
        }

        .related-name {
            min-height: 42px;
            font-size: 15px;
            font-weight: 900;
            line-height: 1.4;
        }

        .related-brand {
            margin-top: 5px;
            font-size: 12px;
            color: var(--muted);
        }

        .related-price {
            margin-top: 8px;
            color: var(--danger);
            font-size: 17px;
            font-weight: 950;
        }

        .related-old-price {
            margin-left: 6px;
            color: #9ca3af;
            font-size: 12px;
            font-weight: 600;
            text-decoration: line-through;
        }

        /* RESPONSIVE */
        @media (max-width: 980px) {
            .product-detail {
                grid-template-columns: 1fr;
            }

            .main-image {
                height: 440px;
            }

            .review-summary {
                grid-template-columns: 1fr;
            }

            .related-grid {
                grid-template-columns: repeat(2, minmax(0,1fr));
            }
        }

        @media (max-width: 620px) {
            .container {
                width: 94%;
            }

            .header-inner {
                padding: 10px 0;
                align-items: flex-start;
                flex-direction: column;
            }

            .nav {
                justify-content: flex-start;
            }

            .product-detail,
            .section {
                padding: 18px;
                border-radius: 18px;
            }

            .main-image {
                height: 320px;
            }

            .current-price {
                font-size: 28px;
            }

            .review-head {
                flex-direction: column;
            }

            .related-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<header class="header">
    <div class="container header-inner">
        <a href="index.php" class="logo">
            🏪 <?= SITE_NAME ?>
        </a>

        <nav class="nav">
            <a href="products.php">🛍️ Sản phẩm</a>
            <a href="cart.php">🛒 Giỏ hàng</a>

            <?php if (isset($_SESSION['user'])): ?>
                <a href="orders.php">📦 Đơn hàng</a>
                <a href="logout.php">Đăng xuất</a>
            <?php else: ?>
                <a href="login.php">🔐 Đăng nhập</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="container">

    <div class="breadcrumb">
        <a href="products.php">Sản phẩm</a>
        <span> / </span>
        <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
    </div>

    <section class="product-detail">

        <!-- GALLERY -->
        <div class="gallery">

            <div class="main-image-wrap">

                <?php if ($salePercent > 0): ?>
                    <div class="sale-corner">
                        🔥 -<?= $salePercent ?>%
                    </div>
                <?php endif; ?>

                <img
                    id="mainProductImage"
                    src="<?= htmlspecialchars($mainImage, ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"
                    class="main-image"
                >
            </div>

            <?php if (!empty($images)): ?>
                <div class="thumbnails">

                    <?php foreach ($images as $index => $image): ?>
                        <img
                            src="<?= htmlspecialchars($image['image_url'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>"
                            class="thumbnail <?= $image['image_url'] === $mainImage ? 'active' : '' ?>"
                            onclick="changeMainImage(
                                this,
                                '<?= htmlspecialchars($image['image_url'], ENT_QUOTES, 'UTF-8') ?>'
                            )"
                        >
                    <?php endforeach; ?>

                </div>
            <?php endif; ?>

        </div>

        <!-- INFORMATION -->
        <div class="information">

            <div class="category">
                🏷️ <?= htmlspecialchars($product['category_name'] ?? 'Sản phẩm', ENT_QUOTES, 'UTF-8') ?>
            </div>

            <h1 class="product-name">
                <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
            </h1>

            <div class="brand">
                Thương hiệu:
                <strong>
                    <?= htmlspecialchars($product['brand_name'] ?? 'Không có', ENT_QUOTES, 'UTF-8') ?>
                </strong>
            </div>

            <div class="quick-rating">
                <span class="stars"><?= renderStars($averageRating) ?></span>
                <span class="rating-number">
                    <?= number_format($averageRating, 1, ',', '.') ?>/5
                </span>
                <span>
                    (<?= $reviewCount ?> đánh giá)
                </span>
                <a href="#reviews" class="rating-link">Xem đánh giá</a>
            </div>

            <div class="price">
                <span class="current-price">
                    <?= formatDetailPrice($displayPrice) ?>
                </span>

                <?php if ($product['sale_price'] !== null): ?>
                    <span class="old-price">
                        <?= formatDetailPrice((float) $product['price']) ?>
                    </span>

                    <span class="sale-badge">
                        Đang giảm giá
                    </span>
                <?php endif; ?>
            </div>

            <?php if ((int) $product['stock'] > 0): ?>
                <div class="stock available">
                    ✅ Còn <strong><?= (int) $product['stock'] ?></strong> sản phẩm
                </div>
            <?php else: ?>
                <div class="stock empty">
                    ❌ Sản phẩm hiện đã hết hàng
                </div>
            <?php endif; ?>

            <div class="description">
                <h3>📋 Mô tả sản phẩm</h3>
                <p>
                    <?= nl2br(
                        htmlspecialchars(
                            $product['description'] ?? 'Chưa có mô tả sản phẩm.',
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    ) ?>
                </p>
            </div>

            <?php if ((int) $product['stock'] > 0): ?>

                <div class="quantity-area">
                    <div class="quantity-title">Số lượng</div>

                    <div class="quantity-input">
                        <button type="button" onclick="decreaseQuantity()">−</button>

                        <input
                            type="number"
                            id="quantity"
                            value="1"
                            min="1"
                            max="<?= (int) $product['stock'] ?>"
                            onchange="validateQuantity()"
                        >

                        <button type="button" onclick="increaseQuantity()">+</button>
                    </div>
                </div>

                <?php if (isset($_SESSION['user'])): ?>

                    <form method="POST" action="cart_action.php" class="cart-form">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                        <input type="hidden" name="quantity" id="cartQuantity" value="1">

                        <button type="submit" class="add-cart">
                            🛒 Thêm vào giỏ hàng
                        </button>
                    </form>

                <?php else: ?>

                    <div class="login-notice">
                        🔐 Bạn cần
                        <a href="login.php">đăng nhập</a>
                        để thêm sản phẩm vào giỏ hàng.
                    </div>

                <?php endif; ?>

            <?php else: ?>

                <button type="button" class="add-cart disabled" disabled>
                    Hết hàng
                </button>

            <?php endif; ?>

        </div>
    </section>

    <!-- REVIEWS -->
    <section class="section" id="reviews">

        <h2 class="section-title">⭐ Đánh giá sản phẩm</h2>
        <p class="section-subtitle">
            Chia sẻ trải nghiệm thực tế của bạn để giúp khách hàng khác lựa chọn dễ dàng hơn.
        </p>

        <div class="review-summary">

            <div class="review-score">
                <div class="score-number">
                    <?= number_format($averageRating, 1, ',', '.') ?>
                </div>

                <div class="score-stars">
                    <?= renderStars($averageRating) ?>
                </div>

                <div class="score-count">
                    <?= $reviewCount ?> lượt đánh giá
                </div>
            </div>

            <div class="review-guide">
                <?php
                $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

                foreach ($reviews as $review) {
                    $ratingValue = (int) ($review['rating'] ?? 0);
                    if (isset($ratingCounts[$ratingValue])) {
                        $ratingCounts[$ratingValue]++;
                    }
                }
                ?>

                <?php for ($star = 5; $star >= 1; $star--): ?>
                    <?php
                    $count = $ratingCounts[$star];
                    $percent = $reviewCount > 0
                        ? ($count / $reviewCount) * 100
                        : 0;
                    ?>
                    <div class="rating-row">
                        <span><?= $star ?> ★</span>

                        <div class="bar">
                            <div
                                class="bar-fill"
                                style="width: <?= $percent ?>%;"
                            ></div>
                        </div>

                        <span><?= $count ?></span>
                    </div>
                <?php endfor; ?>
            </div>

        </div>

        <?php if (isset($_SESSION['user'])): ?>

            <?php if ($canReview): ?>

                <?php if ($userReview): ?>

                    <?php if (($userReview['status'] ?? '') === 'pending'): ?>

                        <div class="review-pending">
                            ⏳ Đánh giá của bạn đang chờ quản trị viên duyệt.
                            Bạn có thể chỉnh sửa và đánh giá sẽ được gửi lại để duyệt.
                        </div>

                    <?php elseif (($userReview['status'] ?? '') === 'hidden'): ?>

                        <div class="review-pending">
                            ⚠️ Đánh giá trước đó của bạn đang bị ẩn.
                            Bạn có thể gửi lại đánh giá mới bên dưới.
                        </div>

                    <?php endif; ?>

                    <form method="POST" action="review_action.php" class="review-form">
                        <h3>✏️ Chỉnh sửa đánh giá của bạn</h3>
                        <p>
                            Sau khi cập nhật, đánh giá sẽ trở về trạng thái chờ duyệt.
                        </p>

                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="product_id" value="<?= (int) $productId ?>">

                        <div class="rating-picker" aria-label="Chọn số sao">
                            <?php for ($star = 5; $star >= 1; $star--): ?>
                                <input
                                    type="radio"
                                    id="update-star-<?= $star ?>"
                                    name="rating"
                                    value="<?= $star ?>"
                                    <?= (int) $userReview['rating'] === $star ? 'checked' : '' ?>
                                >
                                <label for="update-star-<?= $star ?>">★</label>
                            <?php endfor; ?>
                        </div>

                        <textarea
                            name="comment"
                            class="review-textarea"
                            placeholder="Hãy chia sẻ cảm nhận của bạn..."
                            required
                        ><?= htmlspecialchars($userReview['comment'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

                        <button type="submit" class="review-submit">
                            💾 Cập nhật đánh giá
                        </button>
                    </form>

                <?php else: ?>

                    <form method="POST" action="review_action.php" class="review-form">
                        <h3>✍️ Viết đánh giá</h3>
                        <p>
                            Bạn đã mua sản phẩm này. Hãy chia sẻ trải nghiệm thực tế của mình.
                        </p>

                        <input type="hidden" name="action" value="create">
                        <input type="hidden" name="product_id" value="<?= (int) $productId ?>">

                        <div class="rating-picker" aria-label="Chọn số sao">
                            <?php for ($star = 5; $star >= 1; $star--): ?>
                                <input
                                    type="radio"
                                    id="create-star-<?= $star ?>"
                                    name="rating"
                                    value="<?= $star ?>"
                                    <?= $star === 5 ? 'checked' : '' ?>
                                >
                                <label for="create-star-<?= $star ?>">★</label>
                            <?php endfor; ?>
                        </div>

                        <textarea
                            name="comment"
                            class="review-textarea"
                            placeholder="Ví dụ: Sản phẩm đúng mô tả, đóng gói đẹp, giao hàng nhanh..."
                            required
                        ></textarea>

                        <button type="submit" class="review-submit">
                            ⭐ Gửi đánh giá
                        </button>
                    </form>

                <?php endif; ?>

            <?php else: ?>

                <div class="review-login">
                    🛍️ Bạn cần <strong>mua và nhận hàng thành công</strong> sản phẩm này
                    để có thể gửi đánh giá.
                </div>

            <?php endif; ?>

        <?php else: ?>

            <div class="review-login">
                🔐
                <a href="login.php">Đăng nhập</a>
                để đánh giá sản phẩm sau khi mua hàng.
            </div>

        <?php endif; ?>

        <div class="review-list">

            <?php if (empty($reviews)): ?>

                <div class="empty-reviews">
                    <div style="font-size:42px;">💬</div>
                    <strong>Chưa có đánh giá nào</strong>
                    <div style="margin-top:5px;">
                        Hãy là người đầu tiên chia sẻ trải nghiệm về sản phẩm này.
                    </div>
                </div>

            <?php else: ?>

                <?php foreach ($reviews as $review): ?>
                    <article class="review-item">

                        <div class="review-head">
                            <div>
                                <div class="review-user">
                                    <?= htmlspecialchars(
                                        $review['user_name'] ?? 'Khách hàng',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </div>

                                <div class="review-date">
                                    <?= date(
                                        'd/m/Y H:i',
                                        strtotime($review['created_at'])
                                    ) ?>
                                </div>
                            </div>

                            <div class="review-stars">
                                <?= renderStars((float) $review['rating']) ?>
                            </div>
                        </div>

                        <div class="review-comment">
                            <?= htmlspecialchars(
                                $review['comment'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    </article>
                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </section>

    <!-- RELATED PRODUCTS -->
    <?php if (!empty($relatedProducts)): ?>
        <section class="section">

            <h2 class="section-title">🛍️ Có thể bạn cũng thích</h2>
            <p class="section-subtitle">
                Một vài sản phẩm cùng danh mục được chọn cho bạn.
            </p>

            <div class="related-grid">

                <?php foreach ($relatedProducts as $related): ?>

                    <?php
                    $relatedImage = $related['image_url']
                        ?: 'https://placehold.co/600x600/png?text=' . urlencode($related['name']);

                    $relatedPrice = $related['sale_price'] !== null
                        ? (float) $related['sale_price']
                        : (float) $related['price'];
                    ?>

                    <a
                        class="related-card"
                        href="product_detail.php?id=<?= (int) $related['id'] ?>"
                    >
                        <img
                            src="<?= htmlspecialchars($relatedImage, ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($related['name'], ENT_QUOTES, 'UTF-8') ?>"
                            class="related-image"
                        >

                        <div class="related-info">

                            <div class="related-name">
                                <?= htmlspecialchars($related['name'], ENT_QUOTES, 'UTF-8') ?>
                            </div>

                            <div class="related-brand">
                                <?= htmlspecialchars(
                                    $related['brand_name'] ?? 'Không có thương hiệu',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                            <div class="related-price">
                                <?= formatDetailPrice($relatedPrice) ?>

                                <?php if ($related['sale_price'] !== null): ?>
                                    <span class="related-old-price">
                                        <?= formatDetailPrice((float) $related['price']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                        </div>
                    </a>

                <?php endforeach; ?>

            </div>

        </section>
    <?php endif; ?>

</main>

<script>
function syncCartQuantity() {
    const quantityInput = document.getElementById('quantity');
    const cartQuantity = document.getElementById('cartQuantity');

    if (quantityInput && cartQuantity) {
        cartQuantity.value = quantityInput.value;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');

    if (!input) {
        return;
    }

    let value = parseInt(input.value) || 1;

    if (value > 1) {
        value--;
        input.value = value;
    }

    syncCartQuantity();
}

function increaseQuantity() {
    const input = document.getElementById('quantity');

    if (!input) {
        return;
    }

    let value = parseInt(input.value) || 1;
    const max = parseInt(input.max) || 1;

    if (value < max) {
        value++;
        input.value = value;
    }

    syncCartQuantity();
}

function validateQuantity() {
    const input = document.getElementById('quantity');

    if (!input) {
        return;
    }

    const min = parseInt(input.min) || 1;
    const max = parseInt(input.max) || 1;

    let value = parseInt(input.value);

    if (Number.isNaN(value)) {
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

function changeMainImage(element, imageUrl) {
    const mainImage = document.getElementById('mainProductImage');

    if (!mainImage) {
        return;
    }

    mainImage.src = imageUrl;

    document.querySelectorAll('.thumbnail').forEach(function(thumbnail) {
        thumbnail.classList.remove('active');
    });

    if (element) {
        element.classList.add('active');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');

    if (quantityInput) {
        quantityInput.addEventListener('input', syncCartQuantity);
    }
});
</script>

</body>
</html>

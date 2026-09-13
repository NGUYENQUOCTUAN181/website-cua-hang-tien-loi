<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

function formatProductPrice(float $price): string
{
    return number_format($price, 0, ',', '.') . '₫';
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/*
|--------------------------------------------------------------------------
| GET FILTERS
|--------------------------------------------------------------------------
*/
$keyword = trim($_GET['keyword'] ?? '');
$categoryId = (int) ($_GET['category_id'] ?? 0);
$brandId = (int) ($_GET['brand_id'] ?? 0);
$sort = $_GET['sort'] ?? 'latest';

$allowedSorts = [
    'latest' => 'p.id DESC',
    'price_asc' => 'display_price ASC, p.id DESC',
    'price_desc' => 'display_price DESC, p.id DESC',
    'name_asc' => 'p.name ASC, p.id DESC',
    'name_desc' => 'p.name DESC, p.id DESC',
];

if (!isset($allowedSorts[$sort])) {
    $sort = 'latest';
}

/*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/
$perPage = 16;
$page = max(1, (int) ($_GET['page'] ?? 1));

/*
|--------------------------------------------------------------------------
| CATEGORY DATA
|--------------------------------------------------------------------------
*/
$categories = [];
$brands = [];
$selectedCategory = null;
$categoryIdsForFilter = [];

try {
    $categoryStmt = $pdo->query(
        "SELECT id, name, parent_id
         FROM categories
         WHERE status = 'active'
         ORDER BY
             CASE WHEN parent_id IS NULL OR parent_id = 0 THEN 0 ELSE 1 END,
             name ASC"
    );

    $categories = $categoryStmt->fetchAll();

    $brandStmt = $pdo->query(
        "SELECT id, name
         FROM brands
         ORDER BY name ASC"
    );

    $brands = $brandStmt->fetchAll();

    if ($categoryId > 0) {
        $selectedStmt = $pdo->prepare(
            "SELECT id, name, parent_id
             FROM categories
             WHERE id = :id
               AND status = 'active'
             LIMIT 1"
        );
        $selectedStmt->execute(['id' => $categoryId]);
        $selectedCategory = $selectedStmt->fetch();

        if ($selectedCategory) {
            $categoryIdsForFilter[] = (int) $selectedCategory['id'];

            /*
             * Khi chọn category cha, lấy luôn các category con.
             */
            if (
                $selectedCategory['parent_id'] === null ||
                (int) $selectedCategory['parent_id'] === 0
            ) {
                $childStmt = $pdo->prepare(
                    "SELECT id
                     FROM categories
                     WHERE parent_id = :parent_id
                       AND status = 'active'"
                );
                $childStmt->execute([
                    'parent_id' => $categoryId
                ]);

                foreach ($childStmt->fetchAll() as $child) {
                    $categoryIdsForFilter[] = (int) $child['id'];
                }
            }
        } else {
            $categoryId = 0;
        }
    }

    $where = [
        "p.status = 'active'"
    ];

    $params = [];

    /*
     * Search theo tên sản phẩm, tên thương hiệu, tên danh mục.
     */
    if ($keyword !== '') {
        $where[] = "(
            p.name LIKE :keyword
            OR b.name LIKE :keyword
            OR c.name LIKE :keyword
        )";
        $params['keyword'] = '%' . $keyword . '%';
    }

    if (!empty($categoryIdsForFilter)) {
        $placeholders = [];

        foreach ($categoryIdsForFilter as $index => $id) {
            $placeholder = ':category_' . $index;
            $placeholders[] = $placeholder;
            $params[$placeholder] = $id;
        }

        $where[] = "p.category_id IN (" . implode(',', $placeholders) . ")";
    }

    if ($brandId > 0) {
        $where[] = "p.brand_id = :brand_id";
        $params['brand_id'] = $brandId;
    }

    $whereSql = implode(' AND ', $where);

    /*
     * Count tổng sản phẩm.
     */
    $countSql = "
        SELECT COUNT(*)
        FROM products p
        LEFT JOIN brands b ON b.id = p.brand_id
        LEFT JOIN categories c ON c.id = p.category_id
        WHERE $whereSql
    ";

    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);

    $totalProducts = (int) $countStmt->fetchColumn();

    $totalPages = max(1, (int) ceil($totalProducts / $perPage));

    if ($page > $totalPages) {
        $page = $totalPages;
    }

    $offset = ($page - 1) * $perPage;

    /*
     * Lấy sản phẩm.
     */
    $productSql = "
        SELECT
            p.id,
            p.name,
            p.price,
            p.sale_price,
            p.stock,
            p.description,
            p.brand_id,
            p.category_id,
            b.name AS brand_name,
            c.name AS category_name,
            COALESCE(
                (
                    SELECT pi2.image_url
                    FROM product_images pi2
                    WHERE pi2.product_id = p.id
                    ORDER BY pi2.is_primary DESC, pi2.id ASC
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
        FROM products p
        LEFT JOIN brands b ON b.id = p.brand_id
        LEFT JOIN categories c ON c.id = p.category_id
        WHERE $whereSql
        ORDER BY {$allowedSorts[$sort]}
        LIMIT :limit OFFSET :offset
    ";

    $productStmt = $pdo->prepare($productSql);

    foreach ($params as $key => $value) {
        $productStmt->bindValue(
            $key,
            $value,
            is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
        );
    }

    $productStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $productStmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $productStmt->execute();

    $products = $productStmt->fetchAll();

} catch (PDOException $e) {
    $products = [];
    $totalProducts = 0;
    $totalPages = 1;
    $page = 1;
    $errorMessage = 'Không thể tải danh sách sản phẩm. Vui lòng kiểm tra kết nối cơ sở dữ liệu.';
}

/*
|--------------------------------------------------------------------------
| CATEGORY TREE
|--------------------------------------------------------------------------
*/
$parentCategories = [];
$childrenByParent = [];

foreach ($categories as $category) {
    $parentId = $category['parent_id'];

    if ($parentId === null || (int) $parentId === 0) {
        $parentCategories[] = $category;
    } else {
        $childrenByParent[(int) $parentId][] = $category;
    }
}

/*
|--------------------------------------------------------------------------
| QUERY STRING HELPER
|--------------------------------------------------------------------------
*/
function buildProductUrl(array $changes = []): string
{
    $query = [
        'keyword' => $_GET['keyword'] ?? '',
        'category_id' => $_GET['category_id'] ?? '',
        'brand_id' => $_GET['brand_id'] ?? '',
        'sort' => $_GET['sort'] ?? 'latest',
        'page' => $_GET['page'] ?? 1,
    ];

    foreach ($changes as $key => $value) {
        $query[$key] = $value;
    }

    foreach ($query as $key => $value) {
        if ($value === '' || $value === null || ($key === 'page' && (int) $value <= 1)) {
            unset($query[$key]);
        }
    }

    $queryString = http_build_query($query);

    return 'products.php' . ($queryString !== '' ? '?' . $queryString : '');
}

$user = $_SESSION['user'] ?? null;

$wishlistIds = [];

if (!empty($user['id'])) {
    try {
        $wishlistStmt = $pdo->prepare(
            "SELECT product_id
             FROM wishlists
             WHERE user_id = :user_id"
        );
        $wishlistStmt->execute([
            'user_id' => (int) $user['id']
        ]);

        foreach ($wishlistStmt->fetchAll() as $wishlistRow) {
            $wishlistIds[(int) $wishlistRow['product_id']] = true;
        }
    } catch (PDOException $e) {
        /*
         * Bảng wishlist chưa tồn tại thì catalog vẫn hoạt động.
         * Người dùng chỉ không thấy trạng thái yêu thích.
         */
    }
}

$currentBrandName = '';

foreach ($brands as $brand) {
    if ((int) $brand['id'] === $brandId) {
        $currentBrandName = $brand['name'];
        break;
    }
}

$pageTitle = 'Tất cả sản phẩm';

if ($selectedCategory) {
    $pageTitle = $selectedCategory['name'];
}

if ($currentBrandName !== '') {
    $pageTitle .= ' · ' . $currentBrandName;
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($pageTitle) ?> - Nhà Mình Mart</title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --primary: #ff6b00;
            --primary-dark: #e85d00;
            --text: #18212f;
            --muted: #6b7280;
            --border: #e7e9ee;
            --background: #f7f8fb;
            --white: #ffffff;
            --danger: #dc2626;
            --success: #15803d;
            --shadow: 0 12px 35px rgba(20, 30, 45, .08);
        }

        body {
            margin: 0;
            background: var(--background);
            color: var(--text);
            font-family: Arial, Helvetica, sans-serif;
        }

        a {
            color: inherit;
        }

        .container {
            width: min(94%, 1280px);
            margin: auto;
        }

        .header {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(255, 255, 255, .96);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(12px);
        }

        .header-inner {
            min-height: 78px;
            display: grid;
            grid-template-columns: auto minmax(260px, 1fr) auto;
            align-items: center;
            gap: 24px;
        }

        .logo {
            text-decoration: none;
            font-size: 22px;
            font-weight: 900;
            white-space: nowrap;
        }

        .logo-mark {
            display: inline-grid;
            place-items: center;
            width: 42px;
            height: 42px;
            margin-right: 8px;
            border-radius: 13px;
            background: #fff0e5;
            vertical-align: middle;
        }

        .search form {
            display: flex;
            gap: 8px;
        }

        .search input {
            width: 100%;
            min-width: 0;
            padding: 13px 15px;
            border: 1px solid var(--border);
            border-radius: 12px;
            outline: none;
            background: #fff;
            font-size: 14px;
        }

        .search input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(255,107,0,.10);
        }

        .search button {
            border: 0;
            border-radius: 12px;
            padding: 0 18px;
            background: var(--primary);
            color: #fff;
            font-weight: 800;
            cursor: pointer;
        }

        .search button:hover {
            background: var(--primary-dark);
        }

        .nav {
            display: flex;
            gap: 8px;
            align-items: center;
            white-space: nowrap;
        }

        .nav a {
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 800;
        }

        .nav a:hover {
            background: #fff3ea;
            color: var(--primary);
        }

        .page {
            padding: 30px 0 60px;
        }

        .breadcrumb {
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 18px;
        }

        .breadcrumb a {
            text-decoration: none;
        }

        .breadcrumb a:hover {
            color: var(--primary);
        }

        .page-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 22px;
        }

        .page-head h1 {
            margin: 0 0 6px;
            font-size: clamp(28px, 4vw, 40px);
            line-height: 1.05;
        }

        .page-head p {
            margin: 0;
            color: var(--muted);
        }

        .layout {
            display: grid;
            grid-template-columns: 250px minmax(0, 1fr);
            gap: 24px;
            align-items: start;
        }

        .sidebar {
            position: sticky;
            top: 98px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 18px;
            box-shadow: var(--shadow);
        }

        .filter-title {
            margin: 0 0 12px;
            font-size: 16px;
        }

        .filter-section + .filter-section {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        .category-link,
        .brand-link {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            padding: 9px 10px;
            border-radius: 10px;
            text-decoration: none;
            color: #3b4350;
            font-size: 14px;
        }

        .category-link:hover,
        .brand-link:hover {
            background: #fff4eb;
            color: var(--primary);
        }

        .category-link.active,
        .brand-link.active {
            background: #fff0e5;
            color: var(--primary);
            font-weight: 900;
        }

        .children {
            padding-left: 14px;
            border-left: 2px solid #ffe1cc;
            margin: 2px 0 6px 8px;
        }

        .clear-filter {
            display: inline-flex;
            width: 100%;
            justify-content: center;
            align-items: center;
            min-height: 42px;
            margin-top: 12px;
            border-radius: 11px;
            background: #f3f4f6;
            color: #374151;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
        }

        .clear-filter:hover {
            background: #e5e7eb;
        }

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 18px;
            padding: 14px 16px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 15px;
        }

        .result-count {
            color: var(--muted);
            font-size: 13px;
        }

        .sort-form {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .sort-form label {
            font-size: 13px;
            color: var(--muted);
            font-weight: 700;
        }

        .sort-select {
            min-width: 185px;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            outline: none;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .product-card {
            position: relative;
            overflow: hidden;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            text-decoration: none;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .product-card:hover {
            transform: translateY(-4px);
            border-color: #ffc9a8;
            box-shadow: var(--shadow);
        }

        .wishlist-form {
            position: absolute;
            top: 11px;
            right: 11px;
            z-index: 4;
        }

        .wishlist-button {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border: 0;
            border-radius: 50%;
            background: rgba(255,255,255,.96);
            color: #d1d5db;
            box-shadow: 0 6px 16px rgba(20,30,45,.12);
            cursor: pointer;
            font-size: 18px;
        }

        .wishlist-button.active {
            color: #ef4444;
        }

        .wishlist-button:hover {
            color: #ef4444;
            transform: scale(1.05);
        }

        .product-image-wrap {
            position: relative;
            height: 230px;
            background: #fafafa;
            display: grid;
            place-items: center;
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 12px;
        }

        .sale-badge,
        .stock-badge,
        .soldout-badge {
            position: absolute;
            top: 12px;
            padding: 6px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            z-index: 2;
        }

        .sale-badge {
            left: 12px;
            background: #fff1f2;
            color: var(--danger);
        }

        .stock-badge,
        .soldout-badge {
            right: 12px;
        }

        .stock-badge {
            background: #ecfdf3;
            color: var(--success);
        }

        .soldout-badge {
            background: #f3f4f6;
            color: #6b7280;
        }

        .product-info {
            padding: 15px;
        }

        .category-name {
            color: var(--primary);
            font-size: 11px;
            font-weight: 900;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .product-name {
            min-height: 43px;
            font-size: 15px;
            font-weight: 900;
            line-height: 1.4;
        }

        .brand-name {
            min-height: 18px;
            margin-top: 5px;
            color: var(--muted);
            font-size: 12px;
        }

        .price-row {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: 7px;
            margin-top: 10px;
        }

        .price {
            color: var(--danger);
            font-size: 18px;
            font-weight: 950;
        }

        .old-price {
            color: #9ca3af;
            font-size: 12px;
            text-decoration: line-through;
        }

        .empty {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 60px 25px;
            text-align: center;
            color: var(--muted);
        }

        .empty-icon {
            font-size: 46px;
            margin-bottom: 10px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 7px;
            margin-top: 30px;
        }

        .page-link {
            min-width: 40px;
            height: 40px;
            display: inline-grid;
            place-items: center;
            padding: 0 12px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            text-decoration: none;
            color: #374151;
            font-weight: 800;
            font-size: 13px;
        }

        .page-link:hover,
        .page-link.active {
            border-color: var(--primary);
            background: var(--primary);
            color: #fff;
        }

        .error-box {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 12px;
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #9f1239;
            font-size: 14px;
        }

        @media (max-width: 1100px) {
            .header-inner {
                grid-template-columns: 1fr;
                padding: 12px 0;
            }

            .nav {
                overflow-x: auto;
            }

            .product-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 850px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
            }

            .product-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .toolbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .sort-form {
                width: 100%;
            }

            .sort-select {
                width: 100%;
            }
        }

        @media (max-width: 520px) {
            .page-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .product-grid {
                grid-template-columns: 1fr;
            }

            .product-image-wrap {
                height: 260px;
            }
        }
    </style>
</head>

<body>

<header class="header">
    <div class="container header-inner">

        <a href="index.php" class="logo">
            <span class="logo-mark">🏪</span>
            Nhà Mình Mart
        </a>

        <div class="search">
            <form method="GET" action="products.php">

                <?php if ($categoryId > 0): ?>
                    <input type="hidden" name="category_id" value="<?= $categoryId ?>">
                <?php endif; ?>

                <?php if ($brandId > 0): ?>
                    <input type="hidden" name="brand_id" value="<?= $brandId ?>">
                <?php endif; ?>

                <?php if ($sort !== 'latest'): ?>
                    <input type="hidden" name="sort" value="<?= e($sort) ?>">
                <?php endif; ?>

                <input
                    type="text"
                    name="keyword"
                    value="<?= e($keyword) ?>"
                    placeholder="Tìm Coca, mì, nước, snack..."
                    autocomplete="off"
                >

                <button type="submit">🔎 Tìm</button>
            </form>
        </div>

        <nav class="nav">
            <a href="index.php">🏠 Trang chủ</a>
            <a href="products.php">🛍️ Sản phẩm</a>
            <?php if ($user): ?>
                <a href="wishlist.php">❤️ Yêu thích</a>
            <?php endif; ?>
            <a href="cart.php">🛒 Giỏ hàng</a>

            <?php if ($user): ?>
                <a href="orders.php">📦 Đơn hàng</a>
                <a href="logout.php">🚪 Đăng xuất</a>
            <?php else: ?>
                <a href="login.php">🔐 Đăng nhập</a>
            <?php endif; ?>
        </nav>

    </div>
</header>

<main class="container page">

    <div class="breadcrumb">
        <a href="index.php">Trang chủ</a>
        <span> / </span>
        <span>Sản phẩm</span>

        <?php if ($selectedCategory): ?>
            <span> / <?= e($selectedCategory['name']) ?></span>
        <?php endif; ?>

        <?php if ($currentBrandName !== ''): ?>
            <span> / <?= e($currentBrandName) ?></span>
        <?php endif; ?>
    </div>

    <div class="page-head">
        <div>
            <h1><?= e($pageTitle) ?></h1>

            <p>
                <?php if ($keyword !== ''): ?>
                    Kết quả cho từ khóa <strong>"<?= e($keyword) ?>"</strong>.
                <?php else: ?>
                    Khám phá sản phẩm đang có tại Nhà Mình Mart.
                <?php endif; ?>
            </p>
        </div>
    </div>

    <?php if (!empty($errorMessage)): ?>
        <div class="error-box">
            ❌ <?= e($errorMessage) ?>
        </div>
    <?php endif; ?>

    <div class="layout">

        <aside class="sidebar">

            <div class="filter-section">
                <h3 class="filter-title">📚 Danh mục</h3>

                <a
                    href="products.php"
                    class="category-link <?= ($categoryId === 0 && $brandId === 0 && $keyword === '') ? 'active' : '' ?>"
                >
                    <span>Tất cả sản phẩm</span>
                </a>

                <?php foreach ($parentCategories as $parent): ?>

                    <?php
                    $parentId = (int) $parent['id'];
                    $isParentActive = $categoryId === $parentId;
                    ?>

                    <a
                        href="<?= e(buildProductUrl([
                            'category_id' => $parentId,
                            'brand_id' => '',
                            'page' => 1
                        ])) ?>"
                        class="category-link <?= $isParentActive ? 'active' : '' ?>"
                    >
                        <span><?= e($parent['name']) ?></span>
                        <span>→</span>
                    </a>

                    <?php if (!empty($childrenByParent[$parentId])): ?>
                        <div class="children">

                            <?php foreach ($childrenByParent[$parentId] as $child): ?>

                                <?php
                                $childId = (int) $child['id'];
                                ?>

                                <a
                                    href="<?= e(buildProductUrl([
                                        'category_id' => $childId,
                                        'brand_id' => '',
                                        'page' => 1
                                    ])) ?>"
                                    class="category-link <?= $categoryId === $childId ? 'active' : '' ?>"
                                >
                                    <span>↳ <?= e($child['name']) ?></span>
                                </a>

                            <?php endforeach; ?>

                        </div>
                    <?php endif; ?>

                <?php endforeach; ?>
            </div>

            <div class="filter-section">
                <h3 class="filter-title">🏷️ Thương hiệu</h3>

                <?php foreach ($brands as $brand): ?>

                    <?php $currentBrandId = (int) $brand['id']; ?>

                    <a
                        href="<?= e(buildProductUrl([
                            'brand_id' => $currentBrandId,
                            'page' => 1
                        ])) ?>"
                        class="brand-link <?= $brandId === $currentBrandId ? 'active' : '' ?>"
                    >
                        <span><?= e($brand['name']) ?></span>
                    </a>

                <?php endforeach; ?>
            </div>

            <?php if (
                $keyword !== '' ||
                $categoryId > 0 ||
                $brandId > 0 ||
                $sort !== 'latest'
            ): ?>
                <a href="products.php" class="clear-filter">
                    ✕ Xóa tất cả bộ lọc
                </a>
            <?php endif; ?>

        </aside>

        <section>

            <div class="toolbar">

                <div class="result-count">
                    Hiển thị
                    <strong><?= number_format($totalProducts, 0, ',', '.') ?></strong>
                    sản phẩm
                    <?php if ($totalPages > 1): ?>
                        · Trang <?= $page ?>/<?= $totalPages ?>
                    <?php endif; ?>
                </div>

                <form method="GET" action="products.php" class="sort-form">

                    <input type="hidden" name="keyword" value="<?= e($keyword) ?>">
                    <input type="hidden" name="category_id" value="<?= $categoryId ?>">
                    <input type="hidden" name="brand_id" value="<?= $brandId ?>">

                    <label for="sort">Sắp xếp:</label>

                    <select
                        class="sort-select"
                        id="sort"
                        name="sort"
                        onchange="this.form.submit()"
                    >
                        <option value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>>
                            Mới nhất
                        </option>

                        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>
                            Giá thấp → cao
                        </option>

                        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>
                            Giá cao → thấp
                        </option>

                        <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>
                            Tên A → Z
                        </option>

                        <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>
                            Tên Z → A
                        </option>
                    </select>

                </form>

            </div>

            <?php if (empty($products)): ?>

                <div class="empty">
                    <div class="empty-icon">🛒</div>

                    <h2>
                        Không tìm thấy sản phẩm
                    </h2>

                    <p>
                        Hãy thử từ khóa khác hoặc bỏ bớt bộ lọc.
                    </p>

                    <a
                        href="products.php"
                        class="clear-filter"
                        style="max-width:230px;margin:18px auto 0;"
                    >
                        Xem tất cả sản phẩm
                    </a>
                </div>

            <?php else: ?>

                <div class="product-grid">

                    <?php foreach ($products as $product): ?>

                        <?php
                        $originalPrice = (float) $product['price'];
                        $salePrice = $product['sale_price'] !== null
                            ? (float) $product['sale_price']
                            : null;

                        $displayPrice = $salePrice !== null && $salePrice < $originalPrice
                            ? $salePrice
                            : $originalPrice;

                        $salePercent = 0;

                        if ($salePrice !== null && $originalPrice > 0 && $salePrice < $originalPrice) {
                            $salePercent = (int) round(
                                (1 - ($salePrice / $originalPrice)) * 100
                            );
                        }

                        $imageUrl = trim((string) ($product['image_url'] ?? ''));

                        if ($imageUrl === '') {
                            $imageUrl =
                                'https://placehold.co/700x700/png?text=' .
                                urlencode($product['name']);
                        }
                        ?>

                        <article class="product-card">

                            <div style="position:relative;">

                                <?php if ($user): ?>
                                    <?php $isWishlisted = isset($wishlistIds[(int) $product['id']]); ?>

                                    <form
                                        method="POST"
                                        action="wishlist_action.php"
                                        class="wishlist-form"
                                    >
                                        <input
                                            type="hidden"
                                            name="product_id"
                                            value="<?= (int) $product['id'] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="action"
                                            value="<?= $isWishlisted ? 'remove' : 'add' ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="redirect"
                                            value="<?= e($_SERVER['REQUEST_URI'] ?? 'products.php') ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="wishlist-button <?= $isWishlisted ? 'active' : '' ?>"
                                            title="<?= $isWishlisted ? 'Bỏ khỏi yêu thích' : 'Thêm vào yêu thích' ?>"
                                            aria-label="<?= $isWishlisted ? 'Bỏ khỏi yêu thích' : 'Thêm vào yêu thích' ?>"
                                        >
                                            <?= $isWishlisted ? '♥' : '♡' ?>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <a
                                    href="product_detail.php?id=<?= (int) $product['id'] ?>"
                                    aria-label="Xem <?= e($product['name']) ?>"
                                    style="display:block;text-decoration:none;color:inherit;"
                                >

                            <div class="product-image-wrap">

                                <?php if ($salePercent > 0): ?>
                                    <span class="sale-badge">
                                        🔥 -<?= $salePercent ?>%
                                    </span>
                                <?php endif; ?>

                                <?php if ((int) $product['stock'] > 0): ?>
                                    <span class="stock-badge">
                                        Còn hàng
                                    </span>
                                <?php else: ?>
                                    <span class="soldout-badge">
                                        Hết hàng
                                    </span>
                                <?php endif; ?>

                                <img
                                    class="product-image"
                                    src="<?= e($imageUrl) ?>"
                                    alt="<?= e($product['name']) ?>"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='https://placehold.co/700x700/png?text=No+Image';"
                                >

                            </div>

                            <div class="product-info">

                                <div class="category-name">
                                    <?= e($product['category_name'] ?? 'Sản phẩm') ?>
                                </div>

                                <div class="product-name">
                                    <?= e($product['name']) ?>
                                </div>

                                <div class="brand-name">
                                    <?= e($product['brand_name'] ?? 'Không có thương hiệu') ?>
                                </div>

                                <div class="price-row">

                                    <span class="price">
                                        <?= formatProductPrice($displayPrice) ?>
                                    </span>

                                    <?php if ($salePercent > 0): ?>
                                        <span class="old-price">
                                            <?= formatProductPrice($originalPrice) ?>
                                        </span>
                                    <?php endif; ?>

                                </div>

                            </div>

                            </a>

                            </div>

                            <?php if ((int) $product['stock'] > 0): ?>
                                <div style="padding:0 15px 15px;">
                                    <a
                                        href="product_detail.php?id=<?= (int) $product['id'] ?>"
                                        style="display:block;text-align:center;padding:10px;border-radius:10px;background:#ff6b00;color:#fff;text-decoration:none;font-size:13px;font-weight:900;"
                                    >
                                        Xem sản phẩm →
                                    </a>
                                </div>
                            <?php endif; ?>

                        </article>

                    <?php endforeach; ?>

                </div>

                <?php if ($totalPages > 1): ?>

                    <nav class="pagination" aria-label="Phân trang">

                        <?php if ($page > 1): ?>
                            <a
                                class="page-link"
                                href="<?= e(buildProductUrl(['page' => $page - 1])) ?>"
                            >
                                ←
                            </a>
                        <?php endif; ?>

                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);

                        if ($startPage > 1):
                        ?>
                            <a
                                class="page-link"
                                href="<?= e(buildProductUrl(['page' => 1])) ?>"
                            >
                                1
                            </a>

                            <?php if ($startPage > 2): ?>
                                <span class="page-link" style="pointer-events:none;">…</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($pageNumber = $startPage; $pageNumber <= $endPage; $pageNumber++): ?>

                            <a
                                class="page-link <?= $pageNumber === $page ? 'active' : '' ?>"
                                href="<?= e(buildProductUrl(['page' => $pageNumber])) ?>"
                            >
                                <?= $pageNumber ?>
                            </a>

                        <?php endfor; ?>

                        <?php if ($endPage < $totalPages): ?>

                            <?php if ($endPage < $totalPages - 1): ?>
                                <span class="page-link" style="pointer-events:none;">…</span>
                            <?php endif; ?>

                            <a
                                class="page-link"
                                href="<?= e(buildProductUrl(['page' => $totalPages])) ?>"
                            >
                                <?= $totalPages ?>
                            </a>

                        <?php endif; ?>

                        <?php if ($page < $totalPages): ?>
                            <a
                                class="page-link"
                                href="<?= e(buildProductUrl(['page' => $page + 1])) ?>"
                            >
                                →
                            </a>
                        <?php endif; ?>

                    </nav>

                <?php endif; ?>

            <?php endif; ?>

        </section>

    </div>

</main>

</body>
</html>

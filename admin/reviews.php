<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function ratingStars(int $rating): string
{
    $rating = max(0, min(5, $rating));

    return str_repeat('★', $rating)
        . str_repeat('☆', 5 - $rating);
}

/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/
$status = trim($_GET['status'] ?? '');
$keyword = trim($_GET['keyword'] ?? '');

$allowedStatuses = [
    'pending',
    'approved',
    'hidden'
];

if ($status !== '' && !in_array($status, $allowedStatuses, true)) {
    $status = '';
}

/*
|--------------------------------------------------------------------------
| DATA
|--------------------------------------------------------------------------
*/
$reviews = [];
$counts = [
    'all' => 0,
    'pending' => 0,
    'approved' => 0,
    'hidden' => 0,
];

$error = '';

try {
    /*
     * Thống kê trạng thái.
     */
    $countStmt = $pdo->query(
        "SELECT status, COUNT(*) AS total
         FROM reviews
         GROUP BY status"
    );

    foreach ($countStmt->fetchAll() as $row) {
        $reviewStatus = (string) ($row['status'] ?? '');

        if (isset($counts[$reviewStatus])) {
            $counts[$reviewStatus] = (int) $row['total'];
        }
    }

    $counts['all'] =
        $counts['pending']
        + $counts['approved']
        + $counts['hidden'];

    /*
     * Danh sách review.
     */
    $where = [];
    $params = [];

    if ($status !== '') {
        $where[] = 'r.status = :status';
        $params['status'] = $status;
    }

    if ($keyword !== '') {
        $where[] = "(
            u.name LIKE :keyword
            OR u.email LIKE :keyword
            OR p.name LIKE :keyword
            OR r.comment LIKE :keyword
        )";

        $params['keyword'] = '%' . $keyword . '%';
    }

    $whereSql =
        !empty($where)
        ? 'WHERE ' . implode(' AND ', $where)
        : '';

    $sql = "
        SELECT
            r.id,
            r.product_id,
            r.user_id,
            r.rating,
            r.comment,
            r.status,
            r.created_at,
            r.updated_at,
            u.name AS user_name,
            u.email AS user_email,
            p.name AS product_name,
            COALESCE(
                (
                    SELECT pi.image_url
                    FROM product_images pi
                    WHERE pi.product_id = p.id
                    ORDER BY pi.is_primary DESC, pi.id ASC
                    LIMIT 1
                ),
                ''
            ) AS product_image
        FROM reviews r
        INNER JOIN users u
            ON u.id = r.user_id
        INNER JOIN products p
            ON p.id = r.product_id
        $whereSql
        ORDER BY
            CASE r.status
                WHEN 'pending' THEN 0
                WHEN 'approved' THEN 1
                WHEN 'hidden' THEN 2
                ELSE 3
            END,
            r.created_at DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $reviews = $stmt->fetchAll();

} catch (Throwable $e) {
    $error =
        'Không thể tải danh sách đánh giá. '
        . 'Vui lòng kiểm tra bảng reviews, users và products.';
}

$currentUser = $_SESSION['user']['name'] ?? 'Quản trị viên';

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Kiểm duyệt đánh giá - Nhà Mình Mart Admin</title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --primary: #ff6b00;
            --primary-dark: #e85d00;
            --text: #18212f;
            --muted: #667085;
            --border: #e6e8ed;
            --background: #f7f8fb;
            --white: #ffffff;
            --success: #15803d;
            --warning: #b45309;
            --danger: #b91c1c;
            --hidden: #6b7280;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: var(--background);
            color: var(--text);
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
            background: #fff;
            border-bottom: 1px solid var(--border);
        }

        .header-inner {
            min-height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            text-decoration: none;
            font-size: 21px;
            font-weight: 950;
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
        }

        .nav a {
            padding: 10px 12px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
            white-space: nowrap;
        }

        .nav a:hover {
            background: #fff3eb;
            color: var(--primary);
        }

        .page {
            padding: 30px 0 60px;
        }

        .title-row {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
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

        .error {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 12px;
            color: var(--danger);
            background: #fff1f2;
            border: 1px solid #fecdd3;
        }

        .tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 18px;
        }

        .tab {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 13px;
            border-radius: 11px;
            background: #fff;
            border: 1px solid var(--border);
            text-decoration: none;
            font-size: 13px;
            font-weight: 900;
        }

        .tab:hover,
        .tab.active {
            color: #fff;
            background: var(--primary);
            border-color: var(--primary);
        }

        .filters {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 16px;
            background: #fff;
            border: 1px solid var(--border);
        }

        .filters input {
            min-width: 0;
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 11px;
            outline: none;
            font: inherit;
        }

        .filters input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(255,107,0,.10);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-height: 42px;
            padding: 10px 14px;
            border: 0;
            border-radius: 10px;
            cursor: pointer;
            font: inherit;
            font-size: 13px;
            font-weight: 900;
            text-decoration: none;
        }

        .btn-search {
            background: #17202f;
            color: #fff;
        }

        .btn-search:hover {
            background: #0d1420;
        }

        .reviews {
            display: grid;
            gap: 16px;
        }

        .review-card {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 18px;
            padding: 20px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: 0 10px 28px rgba(20,30,45,.04);
        }

        .review-main {
            min-width: 0;
        }

        .review-top {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 13px;
        }

        .product {
            display: flex;
            gap: 12px;
            min-width: 0;
        }

        .product-image {
            width: 58px;
            height: 58px;
            flex: 0 0 58px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: #fafafa;
            object-fit: contain;
            padding: 4px;
        }

        .product-name {
            font-size: 15px;
            font-weight: 950;
            line-height: 1.4;
        }

        .review-date {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
        }

        .rating {
            margin: 10px 0;
            color: #f59e0b;
            font-size: 18px;
            letter-spacing: 1px;
        }

        .comment {
            padding: 14px;
            border-radius: 12px;
            background: #f8fafc;
            color: #344054;
            line-height: 1.65;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .user {
            margin-top: 12px;
            color: #475467;
            font-size: 13px;
        }

        .user strong {
            color: var(--text);
        }

        .actions {
            width: 190px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            justify-content: center;
        }

        .status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 7px 10px;
            margin-bottom: 2px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 950;
        }

        .status.pending {
            color: var(--warning);
            background: #fff7ed;
        }

        .status.approved {
            color: var(--success);
            background: #ecfdf3;
        }

        .status.hidden {
            color: var(--hidden);
            background: #f3f4f6;
        }

        .btn-approve {
            color: #fff;
            background: #15803d;
        }

        .btn-approve:hover {
            background: #166534;
        }

        .btn-hide {
            color: #fff;
            background: #6b7280;
        }

        .btn-hide:hover {
            background: #4b5563;
        }

        .btn-pending {
            color: #fff;
            background: #d97706;
        }

        .btn-pending:hover {
            background: #b45309;
        }

        .empty {
            padding: 55px 20px;
            text-align: center;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            color: var(--muted);
        }

        .empty-icon {
            margin-bottom: 8px;
            font-size: 42px;
        }

        @media (max-width: 850px) {
            .review-card {
                grid-template-columns: 1fr;
            }

            .actions {
                width: 100%;
                flex-direction: row;
                flex-wrap: wrap;
            }

            .actions .status {
                width: 100%;
            }

            .actions form {
                flex: 1;
                min-width: 140px;
            }

            .actions .btn {
                width: 100%;
            }
        }

        @media (max-width: 600px) {
            .header-inner {
                align-items: flex-start;
                flex-direction: column;
                padding: 10px 0;
            }

            .title-row {
                align-items: flex-start;
                flex-direction: column;
            }

            .filters {
                grid-template-columns: 1fr;
            }

            .review-top {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<header class="header">

    <div class="container header-inner">

        <a href="index.php" class="brand">
            🏪 Nhà Mình Mart Admin
        </a>

        <nav class="nav">
            <a href="index.php">📊 Dashboard</a>
            <a href="statistics.php">📈 Thống kê</a>
            <a href="orders.php">🚚 Đơn hàng</a>
            <a href="../index.php">🌐 Website</a>
            <a href="../logout.php">🚪 Đăng xuất</a>
        </nav>

    </div>

</header>

<main class="container page">

    <div class="title-row">

        <div>
            <h1>⭐ Kiểm duyệt đánh giá</h1>

            <p class="subtitle">
                Xin chào <?= e($currentUser) ?> ·
                Kiểm tra nội dung trước khi hiển thị cho khách hàng.
            </p>
        </div>

    </div>

    <?php if ($error !== ''): ?>

        <div class="error">
            ❌ <?= e($error) ?>
        </div>

    <?php endif; ?>

    <div class="tabs">

        <a
            class="tab <?= $status === '' ? 'active' : '' ?>"
            href="reviews.php"
        >
            📋 Tất cả
            <span>(<?= $counts['all'] ?>)</span>
        </a>

        <a
            class="tab <?= $status === 'pending' ? 'active' : '' ?>"
            href="reviews.php?status=pending"
        >
            ⏳ Chờ duyệt
            <span>(<?= $counts['pending'] ?>)</span>
        </a>

        <a
            class="tab <?= $status === 'approved' ? 'active' : '' ?>"
            href="reviews.php?status=approved"
        >
            ✅ Đã duyệt
            <span>(<?= $counts['approved'] ?>)</span>
        </a>

        <a
            class="tab <?= $status === 'hidden' ? 'active' : '' ?>"
            href="reviews.php?status=hidden"
        >
            🙈 Đã ẩn
            <span>(<?= $counts['hidden'] ?>)</span>
        </a>

    </div>

    <form
        method="GET"
        action="reviews.php"
        class="filters"
    >

        <?php if ($status !== ''): ?>

            <input
                type="hidden"
                name="status"
                value="<?= e($status) ?>"
            >

        <?php endif; ?>

        <input
            type="text"
            name="keyword"
            value="<?= e($keyword) ?>"
            placeholder="Tìm theo khách hàng, email, sản phẩm hoặc nội dung đánh giá..."
        >

        <button
            type="submit"
            class="btn btn-search"
        >
            🔎 Tìm kiếm
        </button>

    </form>

    <?php if (empty($reviews)): ?>

        <div class="empty">
            <div class="empty-icon">⭐</div>

            <h2>Chưa có đánh giá phù hợp</h2>

            <p>
                Chưa có đánh giá nào theo bộ lọc hiện tại.
            </p>
        </div>

    <?php else: ?>

        <div class="reviews">

            <?php foreach ($reviews as $review): ?>

                <?php
                $reviewStatus =
                    (string) ($review['status'] ?? 'pending');

                $statusText = [
                    'pending' => '⏳ Chờ duyệt',
                    'approved' => '✅ Đã duyệt',
                    'hidden' => '🙈 Đã ẩn',
                ];

                $image =
                    trim((string) ($review['product_image'] ?? ''));

                if ($image === '') {
                    $image =
                        'https://placehold.co/120x120/png?text=No+Image';
                }
                ?>

                <article class="review-card">

                    <div class="review-main">

                        <div class="review-top">

                            <div class="product">

                                <img
                                    class="product-image"
                                    src="<?= e($image) ?>"
                                    alt="<?= e($review['product_name']) ?>"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='https://placehold.co/120x120/png?text=No+Image';"
                                >

                                <div>
                                    <div class="product-name">
                                        <?= e($review['product_name']) ?>
                                    </div>

                                    <div class="review-date">
                                        🕒
                                        <?= e(
                                            date(
                                                'd/m/Y H:i',
                                                strtotime(
                                                    $review['created_at']
                                                )
                                            )
                                        ) ?>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="rating">
                            <?= e(
                                ratingStars(
                                    (int) $review['rating']
                                )
                            ) ?>

                            <span
                                style="color:#475467;font-size:12px;letter-spacing:0;"
                            >
                                <?= (int) $review['rating'] ?>/5
                            </span>
                        </div>

                        <div class="comment">
                            <?= e($review['comment']) ?>
                        </div>

                        <div class="user">
                            👤
                            <strong><?= e($review['user_name']) ?></strong>

                            ·

                            <?= e($review['user_email']) ?>
                        </div>

                    </div>

                    <div class="actions">

                        <div
                            class="status <?= e($reviewStatus) ?>"
                        >
                            <?= e(
                                $statusText[$reviewStatus]
                                ?? 'Trạng thái khác'
                            ) ?>
                        </div>

                        <?php if ($reviewStatus !== 'approved'): ?>

                            <form
                                method="POST"
                                action="review_action.php"
                            >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="approve"
                                >

                                <input
                                    type="hidden"
                                    name="review_id"
                                    value="<?= (int) $review['id'] ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-approve"
                                >
                                    ✅ Duyệt đánh giá
                                </button>

                            </form>

                        <?php endif; ?>

                        <?php if ($reviewStatus !== 'hidden'): ?>

                            <form
                                method="POST"
                                action="review_action.php"
                            >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="hide"
                                >

                                <input
                                    type="hidden"
                                    name="review_id"
                                    value="<?= (int) $review['id'] ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-hide"
                                >
                                    🙈 Ẩn đánh giá
                                </button>

                            </form>

                        <?php endif; ?>

                        <?php if ($reviewStatus !== 'pending'): ?>

                            <form
                                method="POST"
                                action="review_action.php"
                            >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="pending"
                                >

                                <input
                                    type="hidden"
                                    name="review_id"
                                    value="<?= (int) $review['id'] ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-pending"
                                >
                                    🔄 Đưa về chờ duyệt
                                </button>

                            </form>

                        <?php endif; ?>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</main>

</body>
</html>

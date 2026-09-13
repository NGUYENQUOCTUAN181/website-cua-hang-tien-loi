<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function money(float $value): string
{
    return number_format($value, 0, ',', '.') . '₫';
}

$stats = [
    'revenue' => 0,
    'monthly_revenue' => 0,
    'today_revenue' => 0,
    'orders' => 0,
    'completed_orders' => 0,
    'pending_orders' => 0,
    'customers' => 0,
    'average_order' => 0,
];

$dailyRevenue = [];
$monthlyRevenue = [];
$topProducts = [];
$error = '';

try {
    /*
     * Doanh thu chỉ tính đơn completed để tránh tính các đơn
     * pending / cancelled vào doanh thu thực tế.
     */
    $stats['revenue'] = (float) $pdo->query(
        "SELECT COALESCE(SUM(total), 0)
         FROM orders
         WHERE status = 'completed'"
    )->fetchColumn();

    $stmt = $pdo->prepare(
        "SELECT COALESCE(SUM(total), 0)
         FROM orders
         WHERE status = 'completed'
           AND YEAR(created_at) = YEAR(CURRENT_DATE)
           AND MONTH(created_at) = MONTH(CURRENT_DATE)"
    );
    $stmt->execute();
    $stats['monthly_revenue'] = (float) $stmt->fetchColumn();

    $stmt = $pdo->prepare(
        "SELECT COALESCE(SUM(total), 0)
         FROM orders
         WHERE status = 'completed'
           AND DATE(created_at) = CURRENT_DATE"
    );
    $stmt->execute();
    $stats['today_revenue'] = (float) $stmt->fetchColumn();

    $stats['orders'] = (int) $pdo->query(
        "SELECT COUNT(*) FROM orders"
    )->fetchColumn();

    $stats['completed_orders'] = (int) $pdo->query(
        "SELECT COUNT(*)
         FROM orders
         WHERE status = 'completed'"
    )->fetchColumn();

    $stats['pending_orders'] = (int) $pdo->query(
        "SELECT COUNT(*)
         FROM orders
         WHERE status IN ('pending', 'confirmed', 'shipping')"
    )->fetchColumn();

    $stats['customers'] = (int) $pdo->query(
        "SELECT COUNT(*)
         FROM users u
         INNER JOIN roles r ON r.id = u.role_id
         WHERE r.name = 'CUSTOMER'
           AND u.status = 'active'"
    )->fetchColumn();

    if ($stats['completed_orders'] > 0) {
        $stats['average_order'] =
            $stats['revenue'] / $stats['completed_orders'];
    }

    /*
     * Doanh thu 7 ngày gần nhất.
     */
    $dailyStmt = $pdo->query(
        "SELECT
            DATE(created_at) AS revenue_date,
            COALESCE(SUM(total), 0) AS revenue
         FROM orders
         WHERE status = 'completed'
           AND created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 6 DAY)
         GROUP BY DATE(created_at)
         ORDER BY revenue_date ASC"
    );

    $dailyRows = $dailyStmt->fetchAll();

    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-{$i} days"));

        $dailyRevenue[$date] = 0;
    }

    foreach ($dailyRows as $row) {
        $dailyRevenue[$row['revenue_date']] =
            (float) $row['revenue'];
    }

    /*
     * Doanh thu theo tháng trong năm hiện tại.
     */
    $monthlyStmt = $pdo->query(
        "SELECT
            MONTH(created_at) AS month_number,
            COALESCE(SUM(total), 0) AS revenue
         FROM orders
         WHERE status = 'completed'
           AND YEAR(created_at) = YEAR(CURRENT_DATE)
         GROUP BY MONTH(created_at)
         ORDER BY month_number ASC"
    );

    foreach ($monthlyStmt->fetchAll() as $row) {
        $monthlyRevenue[(int) $row['month_number']] =
            (float) $row['revenue'];
    }

    /*
     * Top sản phẩm bán chạy.
     *
     * order_items của project sử dụng product_id + quantity.
     * Không phụ thuộc vào product_name / unit_price để tránh lỗi
     * khi schema không có hai cột snapshot này.
     *
     * Doanh thu theo sản phẩm dùng giá bán hiện tại của products.
     * Tổng doanh thu tổng thể phía trên vẫn lấy chính xác từ orders.total.
     */
    $topStmt = $pdo->query(
        "SELECT
            p.name AS product_name,
            SUM(oi.quantity) AS sold_quantity,
            SUM(
                oi.quantity *
                CASE
                    WHEN p.sale_price IS NOT NULL
                         AND p.sale_price < p.price
                    THEN p.sale_price
                    ELSE p.price
                END
            ) AS product_revenue
         FROM order_items oi
         INNER JOIN orders o ON o.id = oi.order_id
         INNER JOIN products p ON p.id = oi.product_id
         WHERE o.status = 'completed'
         GROUP BY p.id, p.name, p.price, p.sale_price
         ORDER BY sold_quantity DESC, product_revenue DESC
         LIMIT 10"
    );

    $topProducts = $topStmt->fetchAll();

} catch (Throwable $e) {
    $error =
        'Không thể tải thống kê. Kiểm tra lại cấu trúc database và bảng orders/order_items.';
}

$userName = $_SESSION['user']['name'] ?? 'Quản trị viên';

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Thống kê - Nhà Mình Mart Admin</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f7f8fb;
            color: #17202f;
        }

        .container {
            width: min(94%, 1280px);
            margin: auto;
        }

        .header {
            background: #fff;
            border-bottom: 1px solid #e7e9ee;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .header-inner {
            min-height: 72px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .brand {
            text-decoration: none;
            font-weight: 950;
            font-size: 21px;
        }

        .nav {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .nav a {
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 800;
        }

        .nav a:hover {
            background: #fff1e8;
            color: #ff6b00;
        }

        .page {
            padding: 30px 0 55px;
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
            color: #667085;
        }

        .error {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 18px;
            color: #991b1b;
            background: #fff1f2;
            border: 1px solid #fecdd3;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .card {
            background: #fff;
            border: 1px solid #e7e9ee;
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(20,30,45,.05);
        }

        .label {
            color: #667085;
            font-size: 13px;
            font-weight: 800;
        }

        .value {
            margin-top: 9px;
            font-size: 28px;
            font-weight: 950;
        }

        .accent {
            color: #e85d00;
        }

        .success {
            color: #15803d;
        }

        .blue {
            color: #2563eb;
        }

        .purple {
            color: #7c3aed;
        }

        .section {
            margin-top: 24px;
        }

        .section h2 {
            margin: 0 0 14px;
            font-size: 22px;
        }

        .charts {
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 18px;
        }

        .bars {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            min-height: 250px;
            padding: 20px 12px 0;
        }

        .bar-item {
            flex: 1;
            min-width: 0;
            text-align: center;
        }

        .bar-wrap {
            height: 185px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        .bar {
            width: min(52px, 70%);
            min-height: 3px;
            border-radius: 10px 10px 3px 3px;
            background: linear-gradient(180deg, #ff8a33, #ff6b00);
        }

        .bar-label {
            margin-top: 8px;
            font-size: 11px;
            color: #667085;
        }

        .bar-value {
            margin-top: 5px;
            font-size: 10px;
            color: #344054;
            word-break: break-word;
        }

        .month-list {
            display: grid;
            gap: 10px;
        }

        .month-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            border-radius: 11px;
            background: #f8fafc;
        }

        .month-row span:first-child {
            font-weight: 800;
            color: #475467;
        }

        .month-row strong {
            color: #e85d00;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 13px 12px;
            text-align: left;
            border-bottom: 1px solid #edf0f4;
            font-size: 14px;
        }

        th {
            color: #667085;
            font-size: 12px;
            text-transform: uppercase;
        }

        .empty {
            padding: 30px;
            text-align: center;
            color: #667085;
        }

        @media (max-width: 1000px) {
            .grid {
                grid-template-columns: repeat(2, minmax(0,1fr));
            }

            .charts {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 620px) {
            .header-inner {
                align-items: flex-start;
                flex-direction: column;
                padding: 10px 0;
            }

            .nav {
                overflow-x: auto;
                width: 100%;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .title-row {
                align-items: flex-start;
                flex-direction: column;
            }

            th,
            td {
                padding: 10px 7px;
                font-size: 12px;
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
            <a href="orders.php">🚚 Đơn hàng</a>
            <a href="../index.php">🌐 Website</a>
            <a href="../logout.php">🚪 Đăng xuất</a>
        </nav>

    </div>
</header>

<main class="container page">

    <div class="title-row">
        <div>
            <h1>📈 Thống kê & doanh thu</h1>
            <p class="subtitle">
                Xin chào <?= e($userName) ?> · Doanh thu chỉ tính đơn đã hoàn thành.
            </p>
        </div>
    </div>

    <?php if ($error !== ''): ?>
        <div class="error">
            ❌ <?= e($error) ?>
        </div>
    <?php endif; ?>

    <section class="grid">

        <div class="card">
            <div class="label">💰 Tổng doanh thu</div>
            <div class="value accent"><?= money($stats['revenue']) ?></div>
        </div>

        <div class="card">
            <div class="label">📅 Doanh thu tháng này</div>
            <div class="value success"><?= money($stats['monthly_revenue']) ?></div>
        </div>

        <div class="card">
            <div class="label">☀️ Doanh thu hôm nay</div>
            <div class="value blue"><?= money($stats['today_revenue']) ?></div>
        </div>

        <div class="card">
            <div class="label">🧾 Giá trị đơn trung bình</div>
            <div class="value purple"><?= money($stats['average_order']) ?></div>
        </div>

        <div class="card">
            <div class="label">📦 Tổng số đơn</div>
            <div class="value"><?= number_format($stats['orders'], 0, ',', '.') ?></div>
        </div>

        <div class="card">
            <div class="label">✅ Đơn hoàn thành</div>
            <div class="value success"><?= number_format($stats['completed_orders'], 0, ',', '.') ?></div>
        </div>

        <div class="card">
            <div class="label">⏳ Đơn đang xử lý</div>
            <div class="value accent"><?= number_format($stats['pending_orders'], 0, ',', '.') ?></div>
        </div>

        <div class="card">
            <div class="label">👥 Khách hàng hoạt động</div>
            <div class="value blue"><?= number_format($stats['customers'], 0, ',', '.') ?></div>
        </div>

    </section>

    <section class="section charts">

        <div class="card">
            <h2>📊 Doanh thu 7 ngày gần nhất</h2>

            <?php
            $maxDaily = max(1, max($dailyRevenue ?: [0]));
            ?>

            <div class="bars">

                <?php foreach ($dailyRevenue as $date => $revenue): ?>

                    <?php
                    $height = ($revenue / $maxDaily) * 100;
                    ?>

                    <div class="bar-item">

                        <div class="bar-wrap">
                            <div
                                class="bar"
                                style="height: <?= max(2, $height) ?>%;"
                                title="<?= money($revenue) ?>"
                            ></div>
                        </div>

                        <div class="bar-label">
                            <?= date('d/m', strtotime($date)) ?>
                        </div>

                        <div class="bar-value">
                            <?= money($revenue) ?>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>
        </div>

        <div class="card">
            <h2>📅 Doanh thu theo tháng</h2>

            <div class="month-list">

                <?php for ($month = 1; $month <= 12; $month++): ?>

                    <?php
                    $monthRevenue =
                        (float) ($monthlyRevenue[$month] ?? 0);
                    ?>

                    <div class="month-row">
                        <span>Tháng <?= $month ?></span>
                        <strong><?= money($monthRevenue) ?></strong>
                    </div>

                <?php endfor; ?>

            </div>
        </div>

    </section>

    <section class="section card">

        <h2>🏆 Top sản phẩm bán chạy</h2>

        <?php if (!empty($topProducts)): ?>

            <table>

                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đã bán</th>
                        <th>Doanh thu</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($topProducts as $product): ?>

                        <tr>
                            <td>
                                <?= e($product['product_name'] ?? 'Sản phẩm') ?>
                            </td>

                            <td>
                                <?= number_format(
                                    (int) $product['sold_quantity'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </td>

                            <td>
                                <?= money(
                                    (float) $product['product_revenue']
                                ) ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="empty">
                Chưa có đơn hoàn thành để thống kê doanh thu.
            </div>

        <?php endif; ?>

    </section>

</main>

</body>
</html>

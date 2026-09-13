<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';

$user = $_SESSION['user'];

/*
|--------------------------------------------------------------------------
| DASHBOARD STATS
|--------------------------------------------------------------------------
| Chỉ lấy các số liệu cơ bản từ database để dashboard có dữ liệu thực tế.
*/
$stats = [
    'products' => 0,
    'users' => 0,
    'orders' => 0,
    'reviews' => 0,
    'pending_orders' => 0,
    'low_stock' => 0
];

try {
    $stats['products'] = (int) $pdo
        ->query("SELECT COUNT(*) FROM products WHERE status = 'active'")
        ->fetchColumn();

    $stats['users'] = (int) $pdo
        ->query("SELECT COUNT(*) FROM users")
        ->fetchColumn();

    $stats['orders'] = (int) $pdo
        ->query("SELECT COUNT(*) FROM orders")
        ->fetchColumn();

    $stats['reviews'] = (int) $pdo
        ->query("SELECT COUNT(*) FROM reviews")
        ->fetchColumn();

    $stats['pending_orders'] = (int) $pdo
        ->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")
        ->fetchColumn();

    $stats['low_stock'] = (int) $pdo
        ->query("
            SELECT COUNT(*)
            FROM products
            WHERE status = 'active'
              AND stock <= 10
        ")
        ->fetchColumn();

} catch (PDOException $e) {
    // Dashboard vẫn hoạt động nếu một truy vấn thống kê gặp lỗi.
}

$roleName = $user['role_name'] ?? 'ADMIN';
$userName = $user['name'] ?? 'Quản trị viên';

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
        Nhà Mình Mart Admin
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        :root {
            --primary: #ff6b00;
            --primary-dark: #e85d00;
            --dark: #1f2329;
            --dark-2: #292f36;
            --bg: #f5f7fa;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --border: #e7eaf0;
            --green: #15803d;
            --red: #dc2626;
            --blue: #2563eb;
            --orange-soft: #fff2e8;
            --shadow: 0 14px 35px rgba(18, 26, 38, .07);
        }

        body {
            margin: 0;
            font-family:
                Inter,
                "Segoe UI",
                Arial,
                sans-serif;
            background:
                radial-gradient(
                    circle at 5% 0%,
                    rgba(255, 107, 0, .08),
                    transparent 24%
                ),
                linear-gradient(
                    135deg,
                    #f7f8fb 0%,
                    #f5f7fa 55%,
                    #fff8f2 100%
                );
            color: var(--text);
        }

        a {
            color: inherit;
        }

        .container {
            width: min(94%, 1320px);
            margin: auto;
        }

        /*
        |--------------------------------------------------------------------------
        | TOP HEADER
        |--------------------------------------------------------------------------
        */

        .header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(31, 35, 41, .97);
            color: #fff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .14);
            backdrop-filter: blur(14px);
        }

        .header-inner {
            min-height: 74px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            text-decoration: none;
            color: #fff;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background:
                linear-gradient(
                    135deg,
                    #ff7b18,
                    #ff5900
                );
            font-size: 23px;
            box-shadow:
                0 8px 20px rgba(255, 107, 0, .26);
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .brand-name {
            font-size: 22px;
            font-weight: 950;
            letter-spacing: -.4px;
        }

        .brand-sub {
            font-size: 11px;
            color: rgba(255,255,255,.64);
            letter-spacing: .6px;
            text-transform: uppercase;
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .header-nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 10px;
            font-weight: 750;
            transition: .2s;
        }

        .header-nav a:hover {
            background: rgba(255,255,255,.10);
        }

        .header-nav .logout {
            background: rgba(255,255,255,.08);
        }

        .header-nav .logout:hover {
            background: rgba(220,38,38,.25);
        }

        /*
        |--------------------------------------------------------------------------
        | MAIN
        |--------------------------------------------------------------------------
        */

        .dashboard {
            padding: 34px 0 55px;
        }

        .welcome {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
        }

        .welcome-left h1 {
            margin: 0;
            font-size: clamp(30px, 4vw, 42px);
            letter-spacing: -1px;
        }

        .welcome-left p {
            margin: 8px 0 12px;
            color: var(--muted);
            font-size: 15px;
        }

        .role {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 11px;
            background: var(--orange-soft);
            color: var(--primary-dark);
            border: 1px solid #ffd6bb;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
        }

        .welcome-right {
            min-width: 230px;
            padding: 17px 18px;
            border-radius: 17px;
            background: rgba(255,255,255,.9);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        .welcome-right small {
            display: block;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .welcome-right strong {
            font-size: 16px;
        }

        /*
        |--------------------------------------------------------------------------
        | STATS
        |--------------------------------------------------------------------------
        */

        .stats-grid {
            display: grid;
            grid-template-columns:
                repeat(6, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255,255,255,.95);
            border: 1px solid var(--border);
            border-radius: 17px;
            padding: 18px;
            box-shadow: var(--shadow);
        }

        .stat-icon {
            font-size: 25px;
        }

        .stat-number {
            margin-top: 8px;
            font-size: 28px;
            line-height: 1;
            font-weight: 950;
        }

        .stat-label {
            margin-top: 7px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.4;
        }

        .stat-card.orange .stat-number {
            color: var(--primary);
        }

        .stat-card.blue .stat-number {
            color: var(--blue);
        }

        .stat-card.green .stat-number {
            color: var(--green);
        }

        .stat-card.red .stat-number {
            color: var(--red);
        }

        /*
        |--------------------------------------------------------------------------
        | SECTION
        |--------------------------------------------------------------------------
        */

        .section-heading {
            margin-bottom: 15px;
        }

        .section-heading h2 {
            margin: 0;
            font-size: 24px;
        }

        .section-heading p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 14px;
        }

        /*
        |--------------------------------------------------------------------------
        | MENU
        |--------------------------------------------------------------------------
        */

        .menu {
            display: grid;
            grid-template-columns:
                repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .menu-card {
            position: relative;
            overflow: hidden;
            background: rgba(255,255,255,.96);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 23px;
            text-decoration: none;
            color: var(--text);
            box-shadow: var(--shadow);
            transition:
                transform .2s ease,
                box-shadow .2s ease,
                border-color .2s ease;
        }

        .menu-card::after {
            content: "";
            position: absolute;
            width: 95px;
            height: 95px;
            border-radius: 50%;
            right: -34px;
            top: -34px;
            background: rgba(255, 107, 0, .06);
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow:
                0 20px 42px rgba(18, 26, 38, .11);
            border-color: #ffd3bb;
        }

        .icon {
            position: relative;
            z-index: 2;
            width: 56px;
            height: 56px;
            display: grid;
            place-items: center;
            border-radius: 15px;
            background: var(--orange-soft);
            font-size: 29px;
            margin-bottom: 14px;
        }

        .menu-card h3 {
            position: relative;
            z-index: 2;
            margin: 0 0 7px;
            font-size: 18px;
        }

        .menu-card p {
            position: relative;
            z-index: 2;
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.55;
        }

        .menu-card .arrow {
            position: absolute;
            right: 18px;
            bottom: 16px;
            color: var(--primary);
            font-size: 18px;
            font-weight: 900;
            z-index: 3;
        }

        /*
        |--------------------------------------------------------------------------
        | QUICK LINKS
        |--------------------------------------------------------------------------
        */

        .quick {
            margin-top: 28px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .quick-card {
            padding: 20px;
            border-radius: 18px;
            border: 1px solid var(--border);
            background: #fff;
            box-shadow: var(--shadow);
        }

        .quick-card h3 {
            margin: 0 0 8px;
        }

        .quick-card p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.55;
        }

        .quick-card a {
            display: inline-flex;
            margin-top: 13px;
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 900;
        }

        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1150px) {

            .stats-grid {
                grid-template-columns:
                    repeat(3, minmax(0, 1fr));
            }

            .menu {
                grid-template-columns:
                    repeat(3, minmax(0, 1fr));
            }

        }

        @media (max-width: 820px) {

            .welcome {
                flex-direction: column;
            }

            .welcome-right {
                width: 100%;
            }

            .menu {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

            .quick {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 560px) {

            .header-inner {
                padding: 10px 0;
                align-items: flex-start;
                flex-direction: column;
            }

            .header-nav {
                width: 100%;
                justify-content: flex-start;
                overflow-x: auto;
            }

            .stats-grid,
            .menu {
                grid-template-columns: 1fr;
            }

            .dashboard {
                padding-top: 25px;
            }

        }

    </style>

</head>

<body>

<header class="header">

    <div class="container header-inner">

        <a
            href="index.php"
            class="brand"
        >

            <span class="brand-mark">
                🏪
            </span>

            <span class="brand-text">

                <span class="brand-name">
                    Nhà Mình Mart Admin
                </span>

                <span class="brand-sub">
                    Hệ thống quản trị
                </span>

            </span>

        </a>

        <nav class="header-nav">

            <a href="index.php">
                📊 Dashboard
            </a>

            <a href="../index.php">
                🌐 Website
            </a>

            <a
                href="../logout.php"
                class="logout"
            >
                🚪 Đăng xuất
            </a>

        </nav>

    </div>

</header>


<main class="container dashboard">

    <section class="welcome">

        <div class="welcome-left">

            <h1>
                Chào mừng trở lại 👋
            </h1>

            <p>
                Xin chào
                <strong>
                    <?= htmlspecialchars(
                        $userName,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>.
                Đây là khu vực quản trị của Nhà Mình Mart.
            </p>

            <span class="role">
                🛡️
                <?= htmlspecialchars(
                    $roleName,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>

        </div>

        <div class="welcome-right">

            <small>
                Trạng thái tài khoản
            </small>

            <strong>
                ✅ Đang hoạt động
            </strong>

            <div
                style="
                    margin-top:6px;
                    color:#667085;
                    font-size:12px;
                "
            >
                Có quyền truy cập khu vực quản trị.
            </div>

        </div>

    </section>


    <!-- STATS -->

    <section class="stats-grid">

        <div class="stat-card orange">

            <div class="stat-icon">
                📦
            </div>

            <div class="stat-number">
                <?= $stats['products'] ?>
            </div>

            <div class="stat-label">
                Sản phẩm đang bán
            </div>

        </div>


        <div class="stat-card blue">

            <div class="stat-icon">
                👥
            </div>

            <div class="stat-number">
                <?= $stats['users'] ?>
            </div>

            <div class="stat-label">
                Tài khoản người dùng
            </div>

        </div>


        <div class="stat-card green">

            <div class="stat-icon">
                🚚
            </div>

            <div class="stat-number">
                <?= $stats['orders'] ?>
            </div>

            <div class="stat-label">
                Tổng đơn hàng
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                ⭐
            </div>

            <div class="stat-number">
                <?= $stats['reviews'] ?>
            </div>

            <div class="stat-label">
                Lượt đánh giá
            </div>

        </div>


        <div class="stat-card red">

            <div class="stat-icon">
                ⏳
            </div>

            <div class="stat-number">
                <?= $stats['pending_orders'] ?>
            </div>

            <div class="stat-label">
                Đơn chờ xử lý
            </div>

        </div>


        <div class="stat-card orange">

            <div class="stat-icon">
                ⚠️
            </div>

            <div class="stat-number">
                <?= $stats['low_stock'] ?>
            </div>

            <div class="stat-label">
                Sản phẩm sắp hết
            </div>

        </div>

    </section>


    <!-- MENU -->

    <section>

        <div class="section-heading">

            <h2>
                ⚡ Quản trị nhanh
            </h2>

            <p>
                Truy cập nhanh các module chính của hệ thống.
            </p>

        </div>


        <div class="menu">

            <a
                href="products.php"
                class="menu-card"
            >

                <div class="icon">
                    📦
                </div>

                <h3>
                    Sản phẩm
                </h3>

                <p>
                    Quản lý sản phẩm, giá, trạng thái và tồn kho.
                </p>

                <span class="arrow">
                    →
                </span>

            </a>


            <a
                href="categories.php"
                class="menu-card"
            >

                <div class="icon">
                    🗂️
                </div>

                <h3>
                    Danh mục
                </h3>

                <p>
                    Quản lý danh mục cha, danh mục con và trạng thái.
                </p>

                <span class="arrow">
                    →
                </span>

            </a>


            <a
                href="brands.php"
                class="menu-card"
            >

                <div class="icon">
                    🏷️
                </div>

                <h3>
                    Thương hiệu
                </h3>

                <p>
                    Quản lý thương hiệu sản phẩm.
                </p>

                <span class="arrow">
                    →
                </span>

            </a>


            <a
                href="orders.php"
                class="menu-card"
            >

                <div class="icon">
                    🚚
                </div>

                <h3>
                    Đơn hàng
                </h3>

                <p>
                    Theo dõi, xác nhận, giao hàng và hoàn tất đơn.
                </p>

                <span class="arrow">
                    →
                </span>

            </a>


            <a
                href="users.php"
                class="menu-card"
            >

                <div class="icon">
                    👥
                </div>

                <h3>
                    Người dùng
                </h3>

                <p>
                    Quản lý tài khoản, vai trò và trạng thái.
                </p>

                <span class="arrow">
                    →
                </span>

            </a>


            <a
                href="vouchers.php"
                class="menu-card"
            >

                <div class="icon">
                    🎟️
                </div>

                <h3>
                    Voucher
                </h3>

                <p>
                    Tạo và quản lý mã giảm giá.
                </p>

                <span class="arrow">
                    →
                </span>

            </a>


            <a
                href="reviews.php"
                class="menu-card"
            >

                <div class="icon">
                    ⭐
                </div>

                <h3>
                    Đánh giá
                </h3>

                <p>
                    Kiểm duyệt, ẩn hoặc duyệt review của khách.
                </p>

                <span class="arrow">
                    →
                </span>

            </a>


            <a
                href="statistics.php"
                class="menu-card"
            >

                <div class="icon">
                    📈
                </div>

                <h3>
                    Thống kê
                </h3>

                <p>
                    Theo dõi doanh thu và tình hình kinh doanh.
                </p>

                <span class="arrow">
                    →
                </span>

            </a>

        </div>

    </section>


    <!-- QUICK ACTIONS -->

    <section class="quick">

        <div class="quick-card">

            <h3>
                🚨 Cần chú ý
            </h3>

            <p>
                Hiện có
                <strong>
                    <?= $stats['pending_orders'] ?>
                </strong>
                đơn hàng đang chờ xử lý và
                <strong>
                    <?= $stats['low_stock'] ?>
                </strong>
                sản phẩm có tồn kho từ 10 sản phẩm trở xuống.
            </p>

            <a href="orders.php">
                Xử lý đơn hàng →
            </a>

        </div>


        <div class="quick-card">

            <h3>
                🌐 Trải nghiệm website
            </h3>

            <p>
                Mở giao diện khách hàng để kiểm tra sản phẩm,
                giỏ hàng, checkout, voucher và luồng mua hàng.
            </p>

            <a href="../index.php">
                Mở Nhà Mình Mart →
            </a>

        </div>

    </section>

</main>

</body>

</html>

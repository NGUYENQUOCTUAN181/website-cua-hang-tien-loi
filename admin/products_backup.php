<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';
require_once __DIR__ . '/../models/AdminProduct.php';
require_once __DIR__ . '/../models/Category.php';

$productModel = new AdminProduct($pdo);
$categoryModel = new Category($pdo);

$keyword = trim($_GET['keyword'] ?? '');
$categoryId = (int) ($_GET['category_id'] ?? 0);

$products = $productModel->getAll(
    $keyword,
    $categoryId
);

$categories = $categoryModel->getAll();

function formatAdminPrice(float $price): string
{
    return number_format($price, 0, ',', '.') . '₫';
}

function stockClass(int $stock): string
{
    if ($stock <= 0) {
        return 'out';
    }

    if ($stock <= 10) {
        return 'low';
    }

    return 'good';
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
        Quản lý sản phẩm - Admin
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
            background: #222;
            color: white;
            padding: 18px 0;
        }

        .container {
            width: 94%;
            max-width: 1400px;
            margin: auto;
        }

        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 23px;
            font-weight: bold;
        }

        .header-links {
            display: flex;
            gap: 15px;
        }

        .header-links a {
            color: white;
            text-decoration: none;
        }

        .page {
            padding: 30px 0;
        }

        .page-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .page-head h1 {
            margin: 0;
        }

        .add-btn {
            display: inline-block;
            padding: 12px 18px;
            background: #ff6b00;
            color: white;
            text-decoration: none;
            border-radius: 7px;
            font-weight: bold;
        }

        .filter-box {
            background: white;
            padding: 18px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .filter-form {
            display: grid;
            grid-template-columns: 1fr 220px auto;
            gap: 12px;
        }

        .filter-form input,
        .filter-form select {
            width: 100%;
            padding: 11px;
            border: 1px solid #ddd;
            border-radius: 7px;
            font-size: 15px;
        }

        .filter-form button {
            border: none;
            padding: 11px 18px;
            background: #222;
            color: white;
            border-radius: 7px;
            cursor: pointer;
        }

        .table-box {
            background: white;
            border-radius: 10px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        th,
        td {
            padding: 14px;
            border-bottom: 1px solid #eee;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #fafafa;
        }

        .product-name {
            font-weight: bold;
        }

        .slug {
            color: #888;
            font-size: 13px;
            margin-top: 4px;
        }

        .sale-price {
            color: #e53935;
            font-weight: bold;
        }

        .stock {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 13px;
        }

        .stock.good {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .stock.low {
            background: #fff3e0;
            color: #e65100;
        }

        .stock.out {
            background: #ffebee;
            color: #c62828;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
        }

        .status.active {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status.inactive {
            background: #eee;
            color: #777;
        }

        .actions {
            display: flex;
            gap: 7px;
        }

        .edit-btn,
        .delete-btn {
            display: inline-block;
            padding: 8px 11px;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 13px;
        }

        .edit-btn {
            background: #fff3e0;
            color: #e65100;
        }

        .delete-btn {
            background: #ffebee;
            color: #c62828;
        }

        .empty {
            padding: 50px;
            text-align: center;
            color: #777;
        }

        .message {
            padding: 13px 15px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        @media (max-width: 750px) {

            .page-head {
                flex-direction: column;
                align-items: flex-start;
            }

            .filter-form {
                grid-template-columns: 1fr;
            }

            .header-inner {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }

    </style>

</head>

<body>

<header class="header">

    <div class="container header-inner">

        <div class="logo">
            🏪 Convenience Store Admin
        </div>

        <div class="header-links">

            <a href="index.php">
                Dashboard
            </a>

            <a href="../index.php">
                Website
            </a>

            <a href="../logout.php">
                Đăng xuất
            </a>

        </div>

    </div>

</header>


<main class="container page">


    <div class="page-head">

        <div>

            <h1>
                📦 Quản lý sản phẩm
            </h1>

            <p>
                Thêm, sửa, quản lý giá và tồn kho sản phẩm.
            </p>

        </div>


        <a
            href="product_form.php"
            class="add-btn"
        >
            ➕ Thêm sản phẩm
        </a>

    </div>


    <?php if (isset($_GET['success'])): ?>

        <div class="message">

            ✅

            <?= $_GET['success'] === 'created'
                ? 'Thêm sản phẩm thành công.'
                : (
                    $_GET['success'] === 'updated'
                        ? 'Cập nhật sản phẩm thành công.'
                        : 'Thao tác thành công.'
                ) ?>

        </div>

    <?php endif; ?>


    <?php if (isset($_GET['deleted'])): ?>

        <div class="message">

            ✅ Sản phẩm đã được chuyển sang trạng thái ngừng kinh doanh.

        </div>

    <?php endif; ?>


    <div class="filter-box">

        <form
            method="GET"
            class="filter-form"
        >

            <input
                type="text"
                name="keyword"
                value="<?= htmlspecialchars(
                    $keyword,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                placeholder="Tìm theo tên, slug, thương hiệu..."
            >


            <select name="category_id">

                <option value="0">
                    Tất cả danh mục
                </option>

                <?php foreach ($categories as $category): ?>

                    <option
                        value="<?= (int) $category['id'] ?>"
                        <?= $categoryId === (int) $category['id']
                            ? 'selected'
                            : '' ?>
                    >

                        <?= htmlspecialchars(
                            $category['name'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </option>

                <?php endforeach; ?>

            </select>


            <button type="submit">
                🔍 Tìm kiếm
            </button>

        </form>

    </div>


    <div class="table-box">

        <?php if (empty($products)): ?>

            <div class="empty">

                Không tìm thấy sản phẩm.

            </div>

        <?php else: ?>

            <table>

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Thương hiệu</th>
                        <th>Giá</th>
                        <th>Giá sale</th>
                        <th>Tồn kho</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>

                    </tr>

                </thead>

                <tbody>

                <?php foreach ($products as $product): ?>

                    <tr>

                        <td>
                            #<?= (int) $product['id'] ?>
                        </td>


                        <td>

                            <div class="product-name">

                                <?= htmlspecialchars(
                                    $product['name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                            <div class="slug">

                                <?= htmlspecialchars(
                                    $product['slug'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $product['category_name']
                                    ?? 'Không có',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $product['brand_name']
                                    ?? 'Không có',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <td>

                            <?= formatAdminPrice(
                                (float) $product['price']
                            ) ?>

                        </td>


                        <td>

                            <?php if (
                                $product['sale_price'] !== null
                            ): ?>

                                <span class="sale-price">

                                    <?= formatAdminPrice(
                                        (float) $product['sale_price']
                                    ) ?>

                                </span>

                            <?php else: ?>

                                —

                            <?php endif; ?>

                        </td>


                        <td>

                            <span
                                class="stock <?= stockClass(
                                    (int) $product['stock']
                                ) ?>"
                            >

                                <?= (int) $product['stock'] ?>

                                <?php if (
                                    (int) $product['stock'] === 0
                                ): ?>

                                    — Hết hàng

                                <?php elseif (
                                    (int) $product['stock'] <= 10
                                ): ?>

                                    — Sắp hết

                                <?php endif; ?>

                            </span>

                        </td>


                        <td>

                            <span
                                class="status <?= $product['status'] === 'active'
                                    ? 'active'
                                    : 'inactive' ?>"
                            >

                                <?= $product['status'] === 'active'
                                    ? 'Đang bán'
                                    : 'Ngừng bán' ?>

                            </span>

                        </td>


                        <td>

                            <div class="actions">

                                <a
                                    href="product_form.php?id=<?= (int) $product['id'] ?>"
                                    class="edit-btn"
                                >
                                    ✏️ Sửa
                                </a>


                                <?php if (
                                    in_array(
                                        $_SESSION['user']['role_name'],
                                        ['SUPER_ADMIN', 'ADMIN'],
                                        true
                                    ) &&
                                    $product['status'] === 'active'
                                ): ?>

                                    <form
                                        method="POST"
                                        action="product_action.php"
                                        onsubmit="return confirm(
                                            'Bạn có chắc muốn ngừng bán sản phẩm này?'
                                        );"
                                    >

                                        <input
                                            type="hidden"
                                            name="action"
                                            value="delete"
                                        >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $product['id'] ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="delete-btn"
                                        >
                                            🗑️ Ngừng bán
                                        </button>

                                    </form>

                                <?php endif; ?>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php endif; ?>

    </div>

</main>

</body>

</html>
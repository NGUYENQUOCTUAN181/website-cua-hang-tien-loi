<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';
require_once __DIR__ . '/../models/AdminProduct.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Brand.php';

$productModel = new AdminProduct($pdo);
$categoryModel = new Category($pdo);
$brandModel = new Brand($pdo);

$id = (int) ($_GET['id'] ?? 0);

$product = null;

if ($id > 0) {
    $product = $productModel->findById($id);

    if (!$product) {
        header('Location: products.php');
        exit;
    }
}

$categories = $categoryModel->getAll();
$brands = $brandModel->getAll();

$isEdit = $product !== null;

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
        <?= $isEdit
            ? 'Sửa sản phẩm'
            : 'Thêm sản phẩm' ?>
        - Admin
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .header {
            background: #222;
            color: white;
            padding: 18px 0;
        }

        .container {
            width: 92%;
            max-width: 1000px;
            margin: auto;
        }

        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            text-decoration: none;
            font-size: 23px;
            font-weight: bold;
        }

        .back {
            color: white;
            text-decoration: none;
        }

        .page {
            padding: 35px 0;
        }

        .box {
            background: white;
            padding: 30px;
            border-radius: 12px;
        }

        h1 {
            margin-top: 0;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .full {
            grid-column: 1 / -1;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 7px;
            font-size: 15px;
            outline: none;
            font-family: Arial, sans-serif;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #ff6b00;
        }

        textarea {
            min-height: 140px;
            resize: vertical;
        }

        .actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 7px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        .cancel {
            background: #eee;
            color: #333;
        }

        .save {
            background: #ff6b00;
            color: white;
        }

        @media (max-width: 700px) {

            .grid {
                grid-template-columns: 1fr;
            }

            .full {
                grid-column: auto;
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

        <a
            href="products.php"
            class="logo"
        >
            🏪 Admin — Sản phẩm
        </a>

        <a
            href="products.php"
            class="back"
        >
            ← Danh sách sản phẩm
        </a>

    </div>

</header>


<main class="container page">

    <div class="box">

        <h1>

            <?= $isEdit
                ? '✏️ Sửa sản phẩm'
                : '➕ Thêm sản phẩm' ?>

        </h1>


        <form
            method="POST"
            action="product_action.php"
        >

            <input
                type="hidden"
                name="action"
                value="<?= $isEdit
                    ? 'update'
                    : 'create' ?>"
            >


            <?php if ($isEdit): ?>

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int) $product['id'] ?>"
                >

            <?php endif; ?>


            <div class="grid">


                <!-- CATEGORY -->

                <div class="form-group">

                    <label>
                        Danh mục *
                    </label>

                    <select
                        name="category_id"
                        required
                    >

                        <option value="">
                            -- Chọn danh mục --
                        </option>

                        <?php foreach ($categories as $category): ?>

                            <option
                                value="<?= (int) $category['id'] ?>"
                                <?= $isEdit &&
                                    (int) $product['category_id']
                                    === (int) $category['id']
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

                </div>


                <!-- BRAND -->

                <div class="form-group">

                    <label>
                        Thương hiệu
                    </label>

                    <select name="brand_id">

                        <option value="">
                            -- Không có --
                        </option>

                        <?php foreach ($brands as $brand): ?>

                            <option
                                value="<?= (int) $brand['id'] ?>"
                                <?= $isEdit &&
                                    (int) ($product['brand_id'] ?? 0)
                                    === (int) $brand['id']
                                    ? 'selected'
                                    : '' ?>
                            >

                                <?= htmlspecialchars(
                                    $brand['name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- NAME -->

                <div class="form-group">

                    <label>
                        Tên sản phẩm *
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="<?= htmlspecialchars(
                            $product['name'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required
                    >

                </div>


                <!-- SLUG -->

                <div class="form-group">

                    <label>
                        Slug
                    </label>

                    <input
                        type="text"
                        name="slug"
                        value="<?= htmlspecialchars(
                            $product['slug'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        placeholder="tu-dong-tao-neu-de-trong"
                    >

                </div>


                <!-- PRICE -->

                <div class="form-group">

                    <label>
                        Giá gốc *
                    </label>

                    <input
                        type="number"
                        name="price"
                        min="0"
                        step="0.01"
                        value="<?= htmlspecialchars(
                            $product['price'] ?? '0',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required
                    >

                </div>


                <!-- SALE PRICE -->

                <div class="form-group">

                    <label>
                        Giá sale
                    </label>

                    <input
                        type="number"
                        name="sale_price"
                        min="0"
                        step="0.01"
                        value="<?= htmlspecialchars(
                            $product['sale_price'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        placeholder="Để trống nếu không sale"
                    >

                </div>


                <!-- STOCK -->

                <div class="form-group">

                    <label>
                        Tồn kho *
                    </label>

                    <input
                        type="number"
                        name="stock"
                        min="0"
                        step="1"
                        value="<?= htmlspecialchars(
                            $product['stock'] ?? '0',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required
                    >

                </div>


                <!-- STATUS -->

                <div class="form-group">

                    <label>
                        Trạng thái
                    </label>

                    <select name="status">

                        <option
                            value="active"
                            <?= !$isEdit ||
                                $product['status'] === 'active'
                                ? 'selected'
                                : '' ?>
                        >
                            Đang bán
                        </option>

                        <option
                            value="inactive"
                            <?= $isEdit &&
                                $product['status'] === 'inactive'
                                ? 'selected'
                                : '' ?>
                        >
                            Ngừng bán
                        </option>

                    </select>

                </div>


                <!-- DESCRIPTION -->

                <div class="form-group full">

                    <label>
                        Mô tả sản phẩm
                    </label>

                    <textarea
                        name="description"
                        placeholder="Nhập mô tả sản phẩm..."
                    ><?= htmlspecialchars(
                        $product['description'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?></textarea>

                </div>

            </div>


            <div class="actions">

                <a
                    href="products.php"
                    class="btn cancel"
                >
                    Hủy
                </a>

                <button
                    type="submit"
                    class="btn save"
                >
                    💾
                    <?= $isEdit
                        ? 'Lưu thay đổi'
                        : 'Thêm sản phẩm' ?>
                </button>

            </div>

        </form>

    </div>

</main>

</body>
</html>
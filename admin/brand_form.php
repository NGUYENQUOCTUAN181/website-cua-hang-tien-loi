<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';
require_once __DIR__ . '/../models/AdminBrand.php';

$brandModel = new AdminBrand($pdo);

$id = (int) ($_GET['id'] ?? 0);

$brand = null;

if ($id > 0) {

    $brand = $brandModel->findById($id);

    if (!$brand) {
        header('Location: brands.php');
        exit;
    }
}

$isEdit = $brand !== null;

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
            ? 'Sửa thương hiệu'
            : 'Thêm thương hiệu' ?>
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
            max-width: 850px;
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
        }

        textarea {
            min-height: 130px;
            resize: vertical;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 7px;
            border: none;
            text-decoration: none;
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

    </style>

</head>

<body>

<header class="header">

    <div class="container header-inner">

        <a
            href="brands.php"
            class="logo"
        >
            🏷️ Admin — Thương hiệu
        </a>

        <a
            href="brands.php"
            class="back"
        >
            ← Danh sách
        </a>

    </div>

</header>

<main class="container page">

    <div class="box">

        <h1>
            <?= $isEdit
                ? '✏️ Sửa thương hiệu'
                : '➕ Thêm thương hiệu' ?>
        </h1>

        <form
            method="POST"
            action="brand_action.php"
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
                    value="<?= (int) $brand['id'] ?>"
                >

            <?php endif; ?>

            <div class="form-group">

                <label>
                    Tên thương hiệu *
                </label>

                <input
                    type="text"
                    name="name"
                    value="<?= htmlspecialchars(
                        $brand['name'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label>
                    Mô tả
                </label>

                <textarea
                    name="description"
                ><?= htmlspecialchars(
                    $brand['description'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?></textarea>

            </div>

            <div class="form-group">

                <label>
                    Trạng thái
                </label>

                <select name="status">

                    <option
                        value="active"
                        <?= !$isEdit ||
                            $brand['status'] === 'active'
                            ? 'selected'
                            : '' ?>
                    >
                        Đang hoạt động
                    </option>

                    <option
                        value="inactive"
                        <?= $isEdit &&
                            $brand['status'] === 'inactive'
                            ? 'selected'
                            : '' ?>
                    >
                        Ngừng hoạt động
                    </option>

                </select>

            </div>

            <div class="actions">

                <a
                    href="brands.php"
                    class="btn cancel"
                >
                    Hủy
                </a>

                <button
                    type="submit"
                    class="btn save"
                >
                    💾 Lưu thương hiệu
                </button>

            </div>

        </form>

    </div>

</main>

</body>
</html>
<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';
require_once __DIR__ . '/../models/AdminCategory.php';

$categoryModel = new AdminCategory($pdo);

$id = (int) ($_GET['id'] ?? 0);

$category = null;

if ($id > 0) {

    $category = $categoryModel->findById($id);

    if (!$category) {
        header('Location: categories.php');
        exit;
    }
}

$allCategories = $categoryModel->getAll();

$isEdit = $category !== null;

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
        <?= $isEdit ? 'Sửa danh mục' : 'Thêm danh mục' ?>
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
            max-width: 900px;
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
            href="categories.php"
            class="logo"
        >
            🗂️ Admin — Danh mục
        </a>

        <a
            href="categories.php"
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
                ? '✏️ Sửa danh mục'
                : '➕ Thêm danh mục' ?>
        </h1>

        <form
            method="POST"
            action="category_action.php"
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
                    value="<?= (int) $category['id'] ?>"
                >

            <?php endif; ?>

            <div class="form-group">

                <label>
                    Danh mục cha
                </label>

                <select name="parent_id">

                    <option value="">
                        -- Danh mục gốc --
                    </option>

                    <?php foreach ($allCategories as $item): ?>

                        <?php
                        if (
                            $isEdit &&
                            (int) $item['id'] ===
                            (int) $category['id']
                        ) {
                            continue;
                        }
                        ?>

                        <option
                            value="<?= (int) $item['id'] ?>"
                            <?= $isEdit &&
                                (int) ($category['parent_id'] ?? 0)
                                === (int) $item['id']
                                ? 'selected'
                                : '' ?>
                        >

                            <?= htmlspecialchars(
                                $item['name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="form-group">

                <label>
                    Tên danh mục *
                </label>

                <input
                    type="text"
                    name="name"
                    value="<?= htmlspecialchars(
                        $category['name'] ?? '',
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
                    $category['description'] ?? '',
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
                            $category['status'] === 'active'
                            ? 'selected'
                            : '' ?>
                    >
                        Đang hoạt động
                    </option>

                    <option
                        value="inactive"
                        <?= $isEdit &&
                            $category['status'] === 'inactive'
                            ? 'selected'
                            : '' ?>
                    >
                        Ngừng hoạt động
                    </option>

                </select>

            </div>

            <div class="actions">

                <a
                    href="categories.php"
                    class="btn cancel"
                >
                    Hủy
                </a>

                <button
                    type="submit"
                    class="btn save"
                >
                    💾 Lưu danh mục
                </button>

            </div>

        </form>

    </div>

</main>

</body>
</html>
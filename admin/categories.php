<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';
require_once __DIR__ . '/../models/AdminCategory.php';

$categoryModel = new AdminCategory($pdo);

$keyword = trim($_GET['keyword'] ?? '');
$status = $_GET['status'] ?? '';

$categories = $categoryModel->getAll(
    $keyword,
    $status
);

function categoryStatusClass(string $status): string
{
    return $status === 'active'
        ? 'active'
        : 'inactive';
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

    <title>Quản lý danh mục - Admin</title>

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
            max-width: 1250px;
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

        .links {
            display: flex;
            gap: 15px;
        }

        .links a {
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
            margin-bottom: 20px;
        }

        .page-head h1 {
            margin: 0;
        }

        .add-btn {
            padding: 12px 18px;
            background: #ff6b00;
            color: white;
            text-decoration: none;
            border-radius: 7px;
            font-weight: bold;
        }

        .filter-box,
        .table-box {
            background: white;
            border-radius: 10px;
        }

        .filter-box {
            padding: 18px;
            margin-bottom: 20px;
        }

        .filter-form {
            display: grid;
            grid-template-columns: 1fr 220px auto;
            gap: 10px;
        }

        input,
        select {
            width: 100%;
            padding: 11px;
            border: 1px solid #ddd;
            border-radius: 7px;
            font-size: 15px;
        }

        .filter-form button {
            border: none;
            background: #222;
            color: white;
            padding: 0 18px;
            border-radius: 7px;
            cursor: pointer;
        }

        .table-box {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #fafafa;
        }

        .name {
            font-weight: bold;
        }

        .parent {
            color: #777;
            font-size: 13px;
            margin-top: 4px;
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

        .message {
            padding: 13px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .empty {
            text-align: center;
            padding: 40px;
        }

    </style>

</head>

<body>

<header class="header">

    <div class="container header-inner">

        <a href="index.php" class="logo">
            🏪 Convenience Store Admin
        </a>

        <div class="links">
            <a href="index.php">Dashboard</a>
            <a href="products.php">Sản phẩm</a>
            <a href="../logout.php">Đăng xuất</a>
        </div>

    </div>

</header>

<main class="container page">

    <div class="page-head">

        <div>
            <h1>🗂️ Quản lý danh mục</h1>
            <p>Quản lý danh mục cha, danh mục con.</p>
        </div>

        <a
            href="category_form.php"
            class="add-btn"
        >
            ➕ Thêm danh mục
        </a>

    </div>

    <?php if (isset($_GET['success'])): ?>

        <div class="message">
            ✅ Thao tác danh mục thành công.
        </div>

    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>

        <div class="message">
            ✅ Danh mục đã được chuyển sang trạng thái ngừng hoạt động.
        </div>

    <?php endif; ?>

    <div class="filter-box">

        <form method="GET" class="filter-form">

            <input
                type="text"
                name="keyword"
                value="<?= htmlspecialchars(
                    $keyword,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                placeholder="Tìm tên danh mục..."
            >

            <select name="status">

                <option value="">
                    Tất cả trạng thái
                </option>

                <option
                    value="active"
                    <?= $status === 'active'
                        ? 'selected'
                        : '' ?>
                >
                    Đang hoạt động
                </option>

                <option
                    value="inactive"
                    <?= $status === 'inactive'
                        ? 'selected'
                        : '' ?>
                >
                    Ngừng hoạt động
                </option>

            </select>

            <button type="submit">
                🔍 Tìm kiếm
            </button>

        </form>

    </div>

    <div class="table-box">

        <?php if (empty($categories)): ?>

            <div class="empty">
                Không tìm thấy danh mục.
            </div>

        <?php else: ?>

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Tên danh mục</th>
                        <th>Mô tả</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach ($categories as $category): ?>

                    <tr>

                        <td>
                            #<?= (int) $category['id'] ?>
                        </td>

                        <td>

                            <div class="name">

                                <?= htmlspecialchars(
                                    $category['name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                            <?php if (!empty($category['parent_name'])): ?>

                                <div class="parent">

                                    ↳ Cha:

                                    <?= htmlspecialchars(
                                        $category['parent_name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </div>

                            <?php endif; ?>

                        </td>

                        <td>

                            <?= htmlspecialchars(
                                $category['description'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>

                        <td>

                            <span
                                class="status <?= categoryStatusClass(
                                    $category['status']
                                ) ?>"
                            >
                                <?= $category['status'] === 'active'
                                    ? 'Đang hoạt động'
                                    : 'Ngừng hoạt động' ?>
                            </span>

                        </td>

                        <td>

                            <div class="actions">

                                <a
                                    href="category_form.php?id=<?= (int) $category['id'] ?>"
                                    class="edit-btn"
                                >
                                    ✏️ Sửa
                                </a>

                                <?php if (
                                    $_SESSION['user']['role_name']
                                    !== 'STAFF'
                                    &&
                                    $category['status'] === 'active'
                                ): ?>

                                    <form
                                        method="POST"
                                        action="category_action.php"
                                        onsubmit="return confirm(
                                            'Bạn có chắc muốn ngừng danh mục này?'
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
                                            value="<?= (int) $category['id'] ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="delete-btn"
                                        >
                                            🗑️ Ngừng
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
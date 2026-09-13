<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';
require_once __DIR__ . '/../models/AdminBrand.php';

$brandModel = new AdminBrand($pdo);

$keyword = trim($_GET['keyword'] ?? '');
$status = $_GET['status'] ?? '';

$brands = $brandModel->getAll(
    $keyword,
    $status
);

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Quản lý thương hiệu - Admin</title>

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
            width: 94%;
            max-width: 1200px;
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
            min-width: 800px;
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
            font-size: 17px;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
        }

        .active {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .inactive {
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

    </style>

</head>

<body>

<header class="header">

    <div class="container header-inner">

        <a
            href="index.php"
            class="logo"
        >
            🏪 Convenience Store Admin
        </a>

        <div class="links">

            <a href="index.php">
                Dashboard
            </a>

            <a href="categories.php">
                Danh mục
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
                🏷️ Quản lý thương hiệu
            </h1>

            <p>
                Thêm, sửa và quản lý thương hiệu.
            </p>

        </div>

        <a
            href="brand_form.php"
            class="add-btn"
        >
            ➕ Thêm thương hiệu
        </a>

    </div>


    <?php if (isset($_GET['success'])): ?>

        <div class="message">
            ✅ Thao tác thương hiệu thành công.
        </div>

    <?php endif; ?>


    <?php if (isset($_GET['deleted'])): ?>

        <div class="message">
            ✅ Thương hiệu đã được ngừng hoạt động.
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
                placeholder="Tìm tên thương hiệu..."
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

        <table>

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Thương hiệu</th>
                    <th>Mô tả</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>

            </thead>

            <tbody>

            <?php if (empty($brands)): ?>

                <tr>

                    <td
                        colspan="5"
                        style="text-align:center;padding:40px;"
                    >
                        Không tìm thấy thương hiệu.

                    </td>

                </tr>

            <?php else: ?>

                <?php foreach ($brands as $brand): ?>

                    <tr>

                        <td>
                            #<?= (int) $brand['id'] ?>
                        </td>

                        <td>

                            <div class="name">

                                <?= htmlspecialchars(
                                    $brand['name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </div>

                        </td>

                        <td>

                            <?= htmlspecialchars(
                                $brand['description'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>

                        <td>

                            <span
                                class="status <?= $brand['status'] === 'active'
                                    ? 'active'
                                    : 'inactive' ?>"
                            >

                                <?= $brand['status'] === 'active'
                                    ? 'Đang hoạt động'
                                    : 'Ngừng hoạt động' ?>

                            </span>

                        </td>

                        <td>

                            <div class="actions">

                                <a
                                    href="brand_form.php?id=<?= (int) $brand['id'] ?>"
                                    class="edit-btn"
                                >
                                    ✏️ Sửa
                                </a>

                                <?php if (
                                    $_SESSION['user']['role_name']
                                    !== 'STAFF'
                                    &&
                                    $brand['status'] === 'active'
                                ): ?>

                                    <form
                                        method="POST"
                                        action="brand_action.php"
                                        onsubmit="return confirm(
                                            'Bạn có chắc muốn ngừng thương hiệu này?'
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
                                            value="<?= (int) $brand['id'] ?>"
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

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</main>

</body>

</html>
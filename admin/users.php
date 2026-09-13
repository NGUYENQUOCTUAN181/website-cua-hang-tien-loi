<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';
require_once __DIR__ . '/../models/AdminUser.php';

$userModel = new AdminUser($pdo);

$keyword = trim($_GET['keyword'] ?? '');
$roleId = (int) ($_GET['role_id'] ?? 0);
$status = $_GET['status'] ?? '';

$users = $userModel->getAll(
    $keyword,
    $roleId,
    $status
);

$roles = $userModel->getRoles();

function userStatusText(string $status): string
{
    return $status === 'active'
        ? 'Đang hoạt động'
        : 'Đã khóa';
}

function userStatusClass(string $status): string
{
    return $status === 'active'
        ? 'active'
        : 'locked';
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

    <title>Quản lý người dùng - Admin</title>

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
            max-width: 1350px;
            margin: auto;
        }

        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
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

        .filter-box {
            background: white;
            padding: 18px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .filter-form {
            display: grid;
            grid-template-columns: 1fr 210px 210px auto;
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

        .filter-btn {
            border: none;
            background: #222;
            color: white;
            padding: 11px 18px;
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
            min-width: 1100px;
            border-collapse: collapse;
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

        .name {
            font-weight: bold;
        }

        .small {
            color: #777;
            font-size: 13px;
            margin-top: 4px;
        }

        .role {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 5px;
            background: #fff3e0;
            color: #e65100;
            font-size: 13px;
            font-weight: bold;
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

        .status.locked {
            background: #ffebee;
            color: #c62828;
        }

        .actions {
            display: flex;
            gap: 7px;
        }

        .action-form {
            margin: 0;
        }

        .btn {
            padding: 8px 11px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
        }

        .role-btn {
            background: #e3f2fd;
            color: #1565c0;
        }

        .lock-btn {
            background: #ffebee;
            color: #c62828;
        }

        .unlock-btn {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .message {
            padding: 13px 15px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        @media (max-width: 800px) {

            .filter-form {
                grid-template-columns: 1fr;
            }

            .header-inner {
                flex-direction: column;
                align-items: flex-start;
            }

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

            <a href="orders.php">
                Đơn hàng
            </a>

            <a href="../logout.php">
                Đăng xuất
            </a>

        </div>

    </div>

</header>

<main class="container page">

    <h1>
        👥 Quản lý người dùng
    </h1>

    <p>
        Quản lý tài khoản, quyền và trạng thái người dùng.
    </p>

    <?php if (isset($_GET['success'])): ?>

        <div class="message">
            ✅ Cập nhật người dùng thành công.
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
                placeholder="Tên, email hoặc số điện thoại..."
            >

            <select name="role_id">

                <option value="0">
                    Tất cả quyền
                </option>

                <?php foreach ($roles as $role): ?>

                    <option
                        value="<?= (int) $role['id'] ?>"
                        <?= $roleId === (int) $role['id']
                            ? 'selected'
                            : '' ?>
                    >

                        <?= htmlspecialchars(
                            $role['name'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </option>

                <?php endforeach; ?>

            </select>

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
                    value="locked"
                    <?= $status === 'locked'
                        ? 'selected'
                        : '' ?>
                >
                    Đã khóa
                </option>

            </select>

            <button
                type="submit"
                class="filter-btn"
            >
                🔍 Lọc
            </button>

        </form>

    </div>


    <div class="table-box">

        <table>

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Người dùng</th>
                    <th>Điện thoại</th>
                    <th>Quyền</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>

            </thead>

            <tbody>

            <?php foreach ($users as $user): ?>

                <tr>

                    <td>
                        #<?= (int) $user['id'] ?>
                    </td>

                    <td>

                        <div class="name">

                            <?= htmlspecialchars(
                                $user['name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>

                        <div class="small">

                            <?= htmlspecialchars(
                                $user['email'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>

                    </td>

                    <td>

                        <?= htmlspecialchars(
                            $user['phone'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </td>

                    <td>

                        <span class="role">

                            <?= htmlspecialchars(
                                $user['role_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </span>

                    </td>

                    <td>

                        <span
                            class="status <?= userStatusClass(
                                $user['status']
                            ) ?>"
                        >

                            <?= userStatusText(
                                $user['status']
                            ) ?>

                        </span>

                    </td>

                    <td>

                        <?= htmlspecialchars(
                            $user['created_at'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </td>

                    <td>

                        <div class="actions">

                            <?php if (
                                in_array(
                                    $_SESSION['user']['role_name'],
                                    ['SUPER_ADMIN', 'ADMIN'],
                                    true
                                )
                            ): ?>

                                <form
                                    method="POST"
                                    action="user_action.php"
                                    class="action-form"
                                >

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="role"
                                    >

                                    <input
                                        type="hidden"
                                        name="user_id"
                                        value="<?= (int) $user['id'] ?>"
                                    >

                                    <select
                                        name="role_id"
                                        onchange="this.form.submit()"
                                    >

                                        <?php foreach ($roles as $role): ?>

                                            <option
                                                value="<?= (int) $role['id'] ?>"
                                                <?= (int) $user['role_id']
                                                    === (int) $role['id']
                                                    ? 'selected'
                                                    : '' ?>
                                            >

                                                <?= htmlspecialchars(
                                                    $role['name'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </form>

                            <?php endif; ?>


                            <?php if (
                                (int) $user['id']
                                !== (int) $_SESSION['user']['id']
                                &&
                                in_array(
                                    $_SESSION['user']['role_name'],
                                    ['SUPER_ADMIN', 'ADMIN'],
                                    true
                                )
                            ): ?>

                                <form
                                    method="POST"
                                    action="user_action.php"
                                    class="action-form"
                                    onsubmit="return confirm(
                                        'Bạn có chắc muốn thay đổi trạng thái tài khoản này?'
                                    );"
                                >

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="status"
                                    >

                                    <input
                                        type="hidden"
                                        name="user_id"
                                        value="<?= (int) $user['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="<?= $user['status'] === 'active'
                                            ? 'locked'
                                            : 'active' ?>"
                                    >

                                    <?php if (
                                        $user['status'] === 'active'
                                    ): ?>

                                        <button
                                            type="submit"
                                            class="btn lock-btn"
                                        >
                                            🔒 Khóa
                                        </button>

                                    <?php else: ?>

                                        <button
                                            type="submit"
                                            class="btn unlock-btn"
                                        >
                                            🔓 Mở khóa
                                        </button>

                                    <?php endif; ?>

                                </form>

                            <?php endif; ?>

                        </div>

                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</main>

</body>

</html>
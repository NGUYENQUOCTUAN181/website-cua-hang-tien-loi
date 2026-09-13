<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

if (isset($_SESSION['user'])) {

    $role = $_SESSION['user']['role_name'] ?? '';

    if (in_array(
        $role,
        ['SUPER_ADMIN', 'ADMIN', 'STAFF'],
        true
    )) {
        header('Location: index.php');
        exit;
    }
}

$userModel = new User($pdo);

$email = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {

        $error = 'Vui lòng nhập đầy đủ email và mật khẩu.';

    } else {

        $user = $userModel->findByEmail($email);

        if (!$user) {

            $error = 'Email hoặc mật khẩu không chính xác.';

        } elseif (
            !in_array(
                $user['role_name'],
                ['SUPER_ADMIN', 'ADMIN', 'STAFF'],
                true
            )
        ) {

            $error = 'Tài khoản không có quyền quản trị.';

        } elseif ($user['status'] !== 'active') {

            $error = 'Tài khoản đã bị khóa.';

        } elseif (
            !$userModel->verifyPassword(
                $password,
                $user['password']
            )
        ) {

            $error = 'Email hoặc mật khẩu không chính xác.';

        } else {

            session_regenerate_id(true);

            $_SESSION['user'] = [
                'id' => (int) $user['id'],
                'role_id' => (int) $user['role_id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'role_name' => $user['role_name']
            ];

            header('Location: index.php');
            exit;
        }
    }
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

    <title>Admin Login - <?= SITE_NAME ?></title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .login-box {
            width: 420px;
            max-width: 92%;
            background: white;
            padding: 35px;
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(0,0,0,.08);
        }

        .title {
            text-align: center;
            margin-bottom: 8px;
            font-size: 28px;
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #ddd;
            border-radius: 7px;
            font-size: 15px;
            outline: none;
        }

        input:focus {
            border-color: #ff6b00;
        }

        .error {
            padding: 12px;
            background: #ffebee;
            color: #c62828;
            border-radius: 7px;
            margin-bottom: 18px;
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            background: #ff6b00;
            color: white;
            font-size: 17px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn:hover {
            background: #e85d00;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #ff6b00;
            text-decoration: none;
        }

    </style>

</head>

<body>

<div class="login-box">

    <div class="title">
        🔐 Admin
    </div>

    <div class="subtitle">
        Convenience Store Management
    </div>

    <?php if ($error !== ''): ?>

        <div class="error">
            ❌
            <?= htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <div class="form-group">

            <label>Email</label>

            <input
                type="email"
                name="email"
                value="<?= htmlspecialchars(
                    $email,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                placeholder="admin@gmail.com"
                required
            >

        </div>

        <div class="form-group">

            <label>Mật khẩu</label>

            <input
                type="password"
                name="password"
                placeholder="Nhập mật khẩu"
                required
            >

        </div>

        <button
            type="submit"
            class="btn"
        >
            Đăng nhập quản trị
        </button>

    </form>

    <a
        href="../index.php"
        class="back"
    >
        ← Quay lại website
    </a>

</div>

</body>

</html>
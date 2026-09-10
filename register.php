<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/AuthController.php';

$auth = new AuthController($pdo);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $auth->register();

    if ($result['success']) {
        header('Location: login.php?registered=1');
        exit;
    }

    $errors = $result['errors'];
}

function old(string $key): string
{
    return htmlspecialchars(
        $_POST[$key] ?? '',
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Đăng ký - <?= SITE_NAME ?></title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background: #f5f6f8;
        }

        .register-container {
            width: 100%;
            max-width: 450px;
            background: #fff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin-top: 0;
            margin-bottom: 8px;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #ddd;
            border-radius: 7px;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #ff7a00;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 7px;
            background: #ff7a00;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.9;
        }

        .errors {
            background: #fff0f0;
            border: 1px solid #ffcaca;
            color: #c62828;
            padding: 12px 15px;
            border-radius: 7px;
            margin-bottom: 18px;
        }

        .errors ul {
            margin: 0;
            padding-left: 20px;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #ff7a00;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="register-container">

    <h1>Đăng ký</h1>

    <div class="subtitle">
        Tạo tài khoản <?= SITE_NAME ?>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label for="name">Họ và tên</label>

            <input
                type="text"
                id="name"
                name="name"
                value="<?= old('name') ?>"
                placeholder="Nhập họ và tên"
                required
            >
        </div>

        <div class="form-group">
            <label for="email">Email</label>

            <input
                type="email"
                id="email"
                name="email"
                value="<?= old('email') ?>"
                placeholder="example@gmail.com"
                required
            >
        </div>

        <div class="form-group">
            <label for="phone">Số điện thoại</label>

            <input
                type="tel"
                id="phone"
                name="phone"
                value="<?= old('phone') ?>"
                placeholder="0901234567"
            >
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu</label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Ít nhất 6 ký tự"
                required
            >
        </div>

        <div class="form-group">
            <label for="confirm_password">Xác nhận mật khẩu</label>

            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                placeholder="Nhập lại mật khẩu"
                required
            >
        </div>

        <button type="submit">
            Đăng ký
        </button>

    </form>

    <div class="login-link">
        Đã có tài khoản?
        <a href="login.php">Đăng nhập</a>
    </div>

</div>

</body>
</html>
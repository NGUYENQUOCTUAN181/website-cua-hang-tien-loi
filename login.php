<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/AuthController.php';

$auth = new AuthController($pdo);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $auth->login();

    if ($result['success']) {
        header('Location: index.php');
        exit;
    }

    $error = $result['message'];
}

$registered = isset($_GET['registered']) && $_GET['registered'] === '1';

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Đăng nhập - <?= SITE_NAME ?></title>

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

        .login-container {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        h1 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 8px;
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
            padding: 12px;
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

        .error {
            background: #fff0f0;
            color: #c62828;
            border: 1px solid #ffcaca;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 18px;
        }

        .success {
            background: #eefaf0;
            color: #2e7d32;
            border: 1px solid #b7dfba;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 18px;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: #ff7a00;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="login-container">

    <h1>Đăng nhập</h1>

    <div class="subtitle">
        Chào mừng bạn đến với <?= SITE_NAME ?>
    </div>

    <?php if ($registered): ?>

        <div class="success">
            Đăng ký thành công! Vui lòng đăng nhập.
        </div>

    <?php endif; ?>

    <?php if ($error !== ''): ?>

        <div class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <div class="form-group">

            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="example@gmail.com"
                value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                required
            >

        </div>

        <div class="form-group">

            <label for="password">
                Mật khẩu
            </label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Nhập mật khẩu"
                required
            >

        </div>

        <button type="submit">
            Đăng nhập
        </button>

    </form>

    <div class="register-link">
        Chưa có tài khoản?
        <a href="register.php">Đăng ký ngay</a>
    </div>

</div>

</body>

</html>
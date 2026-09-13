<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/AuthController.php';

$auth = new AuthController($pdo);

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = $auth->login();

    if ($result['success']) {

        /*
         * CUSTOMER → trang sản phẩm
         */
        header('Location: products.php');
        exit;

    } else {

        $error = $result['message'] ?? 'Đăng nhập thất bại.';
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

    <title>
        Đăng nhập - <?= htmlspecialchars(
            SITE_NAME,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </title>

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

            box-shadow:
                0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .logo {

            text-align: center;

            font-size: 30px;

            font-weight: bold;

            color: #ff6b00;

            margin-bottom: 8px;
        }

        .subtitle {

            text-align: center;

            color: #777;

            margin-bottom: 28px;
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

            margin-bottom: 18px;

            background: #ffebee;

            color: #c62828;

            border-radius: 7px;
        }

        .success {

            padding: 12px;

            margin-bottom: 18px;

            background: #e8f5e9;

            color: #2e7d32;

            border-radius: 7px;
        }

        .btn {

            width: 100%;

            padding: 14px;

            border: none;

            border-radius: 8px;

            background: #ff6b00;

            color: white;

            font-size: 17px;

            font-weight: bold;

            cursor: pointer;
        }

        .btn:hover {

            background: #e85d00;
        }

        .register {

            text-align: center;

            margin-top: 20px;
        }

        .register a {

            color: #ff6b00;

            text-decoration: none;

            font-weight: bold;
        }

        .admin-link {

            margin-top: 20px;

            padding-top: 18px;

            border-top: 1px solid #eee;

            text-align: center;
        }

        .admin-link a {

            color: #555;

            text-decoration: none;

            font-weight: bold;
        }

    </style>

</head>

<body>

<div class="login-box">

    <div class="logo">
        🏪 Convenience Store
    </div>

    <div class="subtitle">
        Đăng nhập tài khoản khách hàng
    </div>


    <?php if (
        $error !== ''
    ): ?>

        <div class="error">

            ❌

            <?= htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </div>

    <?php endif; ?>


    <?php if (
        isset($_GET['registered'])
    ): ?>

        <div class="success">

            ✅ Đăng ký thành công.
            Vui lòng đăng nhập.

        </div>

    <?php endif; ?>


    <form
        method="POST"
        action="login.php"
    >

        <div class="form-group">

            <label>
                Email
            </label>

            <input
                type="email"
                name="email"
                value="<?= htmlspecialchars(
                    $email,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                placeholder="example@gmail.com"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Mật khẩu
            </label>

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
            Đăng nhập
        </button>

    </form>


    <div class="register">

        Chưa có tài khoản?

        <a href="register.php">
            Đăng ký
        </a>

    </div>


    <div class="admin-link">

        🔐

        <a href="admin/login.php">
            Đăng nhập dành cho quản trị viên
        </a>

    </div>

</div>

</body>

</html>
<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/AuthController.php';

$auth = new AuthController($pdo);

$error = '';
$registeredMessage = '';

if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role_name'] ?? 'CUSTOMER';

    if (in_array($role, ['SUPER_ADMIN', 'ADMIN', 'STAFF'], true)) {
        header('Location: admin/index.php');
    } else {
        header('Location: index.php');
    }

    exit;
}

if (isset($_GET['registered']) && $_GET['registered'] === '1') {
    $registeredMessage = 'Đăng ký thành công! Hãy đăng nhập để tiếp tục.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Vui lòng nhập email và mật khẩu.';
    } else {
        try {
            $result = $auth->login($email, $password);

            if (!empty($result['success'])) {

                $role = $_SESSION['user']['role_name'] ?? 'CUSTOMER';

                if (in_array($role, ['SUPER_ADMIN', 'ADMIN', 'STAFF'], true)) {
                    header('Location: admin/index.php');
                } else {
                    header('Location: index.php');
                }

                exit;
            }

            $error = $result['message'] ?? 'Email hoặc mật khẩu không đúng.';
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Đăng nhập - Nhà Mình Mart</title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --primary: #ff6b00;
            --primary-dark: #e65d00;
            --text: #202938;
            --muted: #6b7280;
            --border: #e4e7ec;
            --danger: #c62828;
            --success: #18794e;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: Inter, Arial, Helvetica, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 10% 15%, rgba(255,107,0,.16), transparent 26%),
                radial-gradient(circle at 90% 85%, rgba(255,174,0,.13), transparent 28%),
                linear-gradient(135deg, #fff 0%, #f7f8fb 58%, #fff6ed 100%);
            overflow-x: hidden;
        }

        .bg-orb {
            position: fixed;
            border-radius: 999px;
            pointer-events: none;
            filter: blur(2px);
            opacity: .55;
            animation: float 8s ease-in-out infinite;
        }

        .orb-one {
            width: 230px;
            height: 230px;
            left: -70px;
            top: 8%;
            background: rgba(255,107,0,.10);
        }

        .orb-two {
            width: 180px;
            height: 180px;
            right: -45px;
            bottom: 12%;
            background: rgba(255,171,37,.12);
            animation-delay: -3s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate3d(0,0,0);
            }
            50% {
                transform: translate3d(10px,-16px,0);
            }
        }

        .login-card {
            position: relative;
            z-index: 2;
            width: min(100%, 470px);
            background: rgba(255,255,255,.95);
            border: 1px solid rgba(255,255,255,.92);
            border-radius: 24px;
            padding: 34px;
            box-shadow: 0 25px 70px rgba(28,35,49,.10);
            backdrop-filter: blur(14px);
        }

        .brand {
            text-align: center;
            margin-bottom: 24px;
        }

        .brand-mark {
            width: 62px;
            height: 62px;
            margin: 0 auto 12px;
            display: grid;
            place-items: center;
            border-radius: 18px;
            background: linear-gradient(135deg, #ff7a18, #ff5b00);
            color: white;
            font-size: 31px;
            box-shadow: 0 13px 28px rgba(255,107,0,.24);
        }

        .brand h1 {
            margin: 0;
            color: var(--primary);
            font-size: 34px;
            letter-spacing: -.8px;
        }

        .brand p {
            margin: 7px 0 0;
            color: var(--muted);
            font-size: 15px;
        }

        .back-home {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 19px;
            color: var(--muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 800;
        }

        .back-home:hover {
            color: var(--primary);
        }

        .message,
        .error {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 14px;
            line-height: 1.5;
        }

        .message {
            background: #ecfdf3;
            color: var(--success);
            border: 1px solid #b7ebcc;
        }

        .error {
            background: #ffebee;
            color: var(--danger);
            border: 1px solid #ffcdd2;
        }

        .form-group {
            margin-bottom: 17px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 900;
        }

        input {
            width: 100%;
            padding: 14px 15px;
            border: 1px solid var(--border);
            border-radius: 12px;
            outline: none;
            font: inherit;
            background: #fff;
            transition: .2s;
        }

        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(255,107,0,.10);
        }

        .submit {
            width: 100%;
            margin-top: 6px;
            border: 0;
            border-radius: 13px;
            padding: 15px;
            color: white;
            background: linear-gradient(135deg, #ff7a18, #ff5b00);
            font-size: 17px;
            font-weight: 950;
            cursor: pointer;
            box-shadow: 0 14px 26px rgba(255,107,0,.22);
            transition: .2s;
        }

        .submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 32px rgba(255,107,0,.28);
        }

        .footer-links {
            margin-top: 20px;
            text-align: center;
            color: var(--muted);
        }

        .footer-links a {
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 900;
        }

        .divider {
            height: 1px;
            margin: 20px 0;
            background: var(--border);
        }

        .admin-login {
            display: block;
            text-align: center;
            padding: 13px;
            border: 1px solid var(--border);
            border-radius: 12px;
            text-decoration: none;
            color: #475467;
            font-weight: 900;
            background: #fff;
            transition: .2s;
        }

        .admin-login:hover {
            border-color: #cfd4dc;
            background: #f9fafb;
        }

        @media (max-width: 520px) {
            .login-card {
                padding: 25px 20px;
                border-radius: 20px;
            }

            .brand h1 {
                font-size: 29px;
            }
        }
    </style>
</head>

<body>

<div class="bg-orb orb-one"></div>
<div class="bg-orb orb-two"></div>

<main class="login-card">

    <a href="index.php" class="back-home">
        ← Về trang chủ
    </a>

    <div class="brand">
        <div class="brand-mark">🏪</div>

        <h1>Nhà Mình Mart</h1>

        <p>Đăng nhập tài khoản khách hàng</p>
    </div>

    <?php if ($registeredMessage !== ''): ?>
        <div class="message">
            ✅ <?= htmlspecialchars($registeredMessage, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="error">
            ❌ <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">

        <div class="form-group">
            <label for="email">Email</label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="example@gmail.com"
                value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                autocomplete="email"
                required
            >
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu</label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Nhập mật khẩu"
                autocomplete="current-password"
                required
            >
        </div>

        <button type="submit" class="submit">
            🔐 Đăng nhập
        </button>

    </form>

    <div class="footer-links">
        Chưa có tài khoản?
        <a href="register.php">Đăng ký</a>
    </div>

    <div class="divider"></div>

    <a href="admin/login.php" class="admin-login">
        🛡️ Đăng nhập dành cho quản trị viên
    </a>

</main>

</body>
</html>

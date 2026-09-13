<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$error = '';
$success = '';

if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role_name'] ?? 'CUSTOMER';

    if (in_array($role, ['SUPER_ADMIN', 'ADMIN', 'STAFF'], true)) {
        header('Location: admin/index.php');
    } else {
        header('Location: index.php');
    }

    exit;
}

/*
|--------------------------------------------------------------------------
| BƯỚC 1: XÁC MINH EMAIL + SỐ ĐIỆN THOẠI
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['step'] ?? '') === 'verify') {

    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ.';
    } elseif (!preg_match('/^[0-9]{9,11}$/', $phone)) {
        $error = 'Số điện thoại phải gồm 9-11 chữ số.';
    } else {
        try {
            $stmt = $pdo->prepare(
                "SELECT id, name
                 FROM users
                 WHERE email = :email
                   AND phone = :phone
                   AND status = 'active'
                 LIMIT 1"
            );

            $stmt->execute([
                'email' => $email,
                'phone' => $phone,
            ]);

            $user = $stmt->fetch();

            if (!$user) {
                $error = 'Không tìm thấy tài khoản phù hợp với email và số điện thoại.';
            } else {
                $_SESSION['password_reset_user_id'] = (int) $user['id'];
                $_SESSION['password_reset_email'] = $email;

                header('Location: forgot_password.php?step=reset');
                exit;
            }
        } catch (Throwable $e) {
            $error = 'Không thể xử lý yêu cầu. Vui lòng thử lại.';
        }
    }
}

/*
|--------------------------------------------------------------------------
| BƯỚC 2: ĐẶT MẬT KHẨU MỚI
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['step'] ?? '') === 'reset') {

    $userId = (int) ($_SESSION['password_reset_user_id'] ?? 0);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($userId <= 0) {
        $error = 'Phiên xác minh đã hết. Vui lòng thực hiện lại.';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Xác nhận mật khẩu không khớp.';
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "UPDATE users
                 SET password = :password,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id
                 LIMIT 1"
            );

            $stmt->execute([
                'password' => $hash,
                'id' => $userId,
            ]);

            unset(
                $_SESSION['password_reset_user_id'],
                $_SESSION['password_reset_email']
            );

            header('Location: login.php?reset=1');
            exit;
        } catch (Throwable $e) {
            $error = 'Không thể cập nhật mật khẩu. Vui lòng thử lại.';
        }
    }
}

$resetMode =
    ($_GET['step'] ?? '') === 'reset'
    && !empty($_SESSION['password_reset_user_id']);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quên mật khẩu - Nhà Mình Mart</title>

    <style>
        * { box-sizing: border-box; }

        :root {
            --primary: #ff6b00;
            --primary-dark: #e45d00;
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
            font-family: Arial, Helvetica, sans-serif;
            background:
                radial-gradient(circle at 12% 15%, rgba(255,107,0,.15), transparent 27%),
                radial-gradient(circle at 88% 85%, rgba(255,170,0,.13), transparent 28%),
                linear-gradient(135deg, #fff 0%, #f7f8fb 58%, #fff6ed 100%);
            color: var(--text);
        }

        .card {
            width: min(100%, 480px);
            padding: 34px;
            background: rgba(255,255,255,.96);
            border: 1px solid #fff;
            border-radius: 24px;
            box-shadow: 0 25px 70px rgba(28,35,49,.10);
        }

        .brand {
            text-align: center;
            margin-bottom: 25px;
        }

        .brand-mark {
            width: 62px;
            height: 62px;
            margin: 0 auto 12px;
            display: grid;
            place-items: center;
            border-radius: 18px;
            background: linear-gradient(135deg, #ff7a18, #ff5b00);
            color: #fff;
            font-size: 31px;
        }

        h1 {
            margin: 0;
            color: var(--primary);
            font-size: 32px;
        }

        .brand p {
            margin: 8px 0 0;
            color: var(--muted);
        }

        .back {
            display: inline-block;
            margin-bottom: 18px;
            color: var(--muted);
            text-decoration: none;
            font-weight: 800;
            font-size: 14px;
        }

        .back:hover {
            color: var(--primary);
        }

        .step {
            margin-bottom: 20px;
            padding: 12px 14px;
            border-radius: 12px;
            background: #fff7ed;
            color: #9a3412;
            font-size: 13px;
            line-height: 1.5;
        }

        .message,
        .error {
            margin-bottom: 16px;
            padding: 12px 14px;
            border-radius: 12px;
            line-height: 1.5;
            font-size: 14px;
        }

        .error {
            background: #ffebee;
            color: var(--danger);
            border: 1px solid #ffcdd2;
        }

        .message {
            background: #ecfdf3;
            color: var(--success);
            border: 1px solid #b7ebcc;
        }

        .group {
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
        }

        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(255,107,0,.10);
        }

        button {
            width: 100%;
            border: 0;
            border-radius: 13px;
            padding: 15px;
            color: #fff;
            background: linear-gradient(135deg, #ff7a18, #ff5b00);
            font-size: 16px;
            font-weight: 900;
            cursor: pointer;
        }

        button:hover {
            filter: brightness(.97);
            transform: translateY(-1px);
        }

        .footer {
            margin-top: 19px;
            text-align: center;
            color: var(--muted);
            font-size: 14px;
        }

        .footer a {
            color: var(--primary-dark);
            font-weight: 900;
            text-decoration: none;
        }

        @media (max-width: 520px) {
            .card {
                padding: 25px 20px;
            }

            h1 {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>

<main class="card">

    <a href="login.php" class="back">← Quay lại đăng nhập</a>

    <div class="brand">
        <div class="brand-mark">🔐</div>
        <h1>Nhà Mình Mart</h1>
        <p>Khôi phục mật khẩu tài khoản</p>
    </div>

    <?php if ($error !== ''): ?>
        <div class="error">
            ❌ <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if (!$resetMode): ?>

        <div class="step">
            Bước 1/2: Nhập <strong>email + số điện thoại</strong> đã đăng ký để xác minh tài khoản.
        </div>

        <form method="POST">

            <input type="hidden" name="step" value="verify">

            <div class="group">
                <label for="email">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    placeholder="example@gmail.com"
                    autocomplete="email"
                    required
                >
            </div>

            <div class="group">
                <label for="phone">Số điện thoại</label>
                <input
                    id="phone"
                    name="phone"
                    type="text"
                    placeholder="0901234567"
                    inputmode="numeric"
                    required
                >
            </div>

            <button type="submit">
                🔎 Xác minh tài khoản
            </button>

        </form>

    <?php else: ?>

        <div class="step">
            Bước 2/2: Tạo mật khẩu mới cho tài khoản
            <strong><?= htmlspecialchars($_SESSION['password_reset_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>.
        </div>

        <form method="POST">

            <input type="hidden" name="step" value="reset">

            <div class="group">
                <label for="password">Mật khẩu mới</label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    minlength="6"
                    autocomplete="new-password"
                    placeholder="Ít nhất 6 ký tự"
                    required
                >
            </div>

            <div class="group">
                <label for="confirm_password">Xác nhận mật khẩu</label>

                <input
                    id="confirm_password"
                    name="confirm_password"
                    type="password"
                    minlength="6"
                    autocomplete="new-password"
                    placeholder="Nhập lại mật khẩu mới"
                    required
                >
            </div>

            <button type="submit">
                ✅ Đặt mật khẩu mới
            </button>

        </form>

    <?php endif; ?>

    <div class="footer">
        Nhớ mật khẩu rồi?
        <a href="login.php">Đăng nhập</a>
    </div>

</main>

</body>
</html>

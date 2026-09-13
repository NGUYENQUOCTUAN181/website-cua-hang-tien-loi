<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/AuthController.php';

$auth = new AuthController($pdo);

$error = '';
$name = '';
$email = '';
$phone = '';

if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $phone === '' || $password === '' || $confirmPassword === '') {
        $error = 'Vui lòng điền đầy đủ thông tin.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ.';
    } elseif (!preg_match('/^[0-9]{9,11}$/', $phone)) {
        $error = 'Số điện thoại phải gồm 9-11 chữ số.';
    } elseif (mb_strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Mật khẩu xác nhận không khớp.';
    } else {
        try {
            $result = $auth->register($name, $email, $phone, $password);

            if (!empty($result['success'])) {
                header('Location: login.php?registered=1');
                exit;
            }

            $error = $result['message'] ?? 'Không thể tạo tài khoản.';
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
<title>Đăng ký - Nhà Mình Mart</title>
<style>
*{box-sizing:border-box}
:root{--primary:#ff6b00;--primary-dark:#e65d00;--text:#202938;--muted:#6b7280;--border:#e4e7ec;--danger:#c62828}
body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;font-family:Inter,Arial,sans-serif;color:var(--text);background:radial-gradient(circle at 10% 15%,rgba(255,107,0,.15),transparent 26%),radial-gradient(circle at 90% 85%,rgba(255,174,0,.13),transparent 28%),linear-gradient(135deg,#fff 0%,#f7f8fb 58%,#fff6ed 100%);overflow-x:hidden}
.orb{position:fixed;border-radius:999px;pointer-events:none;animation:float 8s ease-in-out infinite}.one{width:230px;height:230px;left:-65px;top:8%;background:rgba(255,107,0,.09)}.two{width:180px;height:180px;right:-40px;bottom:10%;background:rgba(255,174,0,.11);animation-delay:-3s}
@keyframes float{0%,100%{transform:translate3d(0,0,0)}50%{transform:translate3d(10px,-15px,0)}}
.card{position:relative;z-index:2;width:min(100%,520px);background:rgba(255,255,255,.95);border:1px solid rgba(255,255,255,.92);border-radius:24px;padding:32px;box-shadow:0 25px 70px rgba(28,35,49,.10);backdrop-filter:blur(14px)}
.back{display:inline-flex;gap:6px;margin-bottom:18px;color:var(--muted);text-decoration:none;font-size:14px;font-weight:800}.back:hover{color:var(--primary)}
.brand{text-align:center;margin-bottom:23px}.mark{width:62px;height:62px;margin:0 auto 12px;display:grid;place-items:center;border-radius:18px;background:linear-gradient(135deg,#ff7a18,#ff5b00);color:#fff;font-size:31px;box-shadow:0 13px 28px rgba(255,107,0,.23)}
.brand h1{margin:0;color:var(--primary);font-size:34px;letter-spacing:-.8px}.brand p{margin:7px 0 0;color:var(--muted);font-size:15px}
.error{margin-bottom:16px;padding:12px 14px;border-radius:12px;background:#ffebee;color:var(--danger);border:1px solid #ffcdd2;font-size:14px;line-height:1.5}
.group{margin-bottom:15px}label{display:block;margin-bottom:7px;font-weight:900}
input{width:100%;padding:14px 15px;border:1px solid var(--border);border-radius:12px;outline:0;font:inherit;background:#fff;transition:.2s}input:focus{border-color:var(--primary);box-shadow:0 0 0 4px rgba(255,107,0,.10)}
.hint{margin-top:6px;color:#98a2b3;font-size:12px}
.submit{width:100%;margin-top:5px;border:0;border-radius:13px;padding:15px;color:#fff;background:linear-gradient(135deg,#ff7a18,#ff5b00);font-size:17px;font-weight:950;cursor:pointer;box-shadow:0 14px 26px rgba(255,107,0,.22);transition:.2s}.submit:hover{transform:translateY(-2px);box-shadow:0 18px 32px rgba(255,107,0,.28)}
.links{margin-top:19px;text-align:center;color:var(--muted)}.links a{color:var(--primary-dark);text-decoration:none;font-weight:900}
.note{margin-top:16px;padding:12px 14px;border-radius:12px;background:#fff8f1;border:1px solid #ffe0c5;color:#81501e;font-size:13px;line-height:1.5;text-align:center}
@media(max-width:560px){.card{padding:25px 20px;border-radius:20px}.brand h1{font-size:29px}}
</style>
</head>
<body>
<div class="orb one"></div><div class="orb two"></div>
<main class="card">
<a href="index.php" class="back">← Về trang chủ</a>
<div class="brand">
<div class="mark">🏪</div>
<h1>Nhà Mình Mart</h1>
<p>Tạo tài khoản khách hàng</p>
</div>
<?php if ($error !== ''): ?>
<div class="error">❌ <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<form method="POST" action="register.php" autocomplete="on">
<div class="group"><label for="name">Họ và tên</label><input type="text" id="name" name="name" placeholder="Nguyễn Văn A" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" autocomplete="name" required></div>
<div class="group"><label for="email">Email</label><input type="email" id="email" name="email" placeholder="example@gmail.com" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" autocomplete="email" required></div>
<div class="group"><label for="phone">Số điện thoại</label><input type="tel" id="phone" name="phone" placeholder="0901234567" value="<?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?>" inputmode="numeric" autocomplete="tel" required></div>
<div class="group"><label for="password">Mật khẩu</label><input type="password" id="password" name="password" placeholder="Ít nhất 6 ký tự" autocomplete="new-password" minlength="6" required><div class="hint">🔐 Mật khẩu tối thiểu 6 ký tự.</div></div>
<div class="group"><label for="confirm_password">Xác nhận mật khẩu</label><input type="password" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu" autocomplete="new-password" minlength="6" required></div>
<button type="submit" class="submit">✨ Đăng ký tài khoản</button>
</form>
<div class="links">Đã có tài khoản? <a href="login.php">Đăng nhập</a></div>
<div class="note">🛍️ Tạo tài khoản để mua hàng, theo dõi đơn và đánh giá sản phẩm tại Nhà Mình Mart.</div>
</main>
</body>
</html>

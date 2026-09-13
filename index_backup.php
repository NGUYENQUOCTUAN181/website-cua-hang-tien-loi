<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$user = $_SESSION['user'] ?? null;

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= SITE_NAME ?></title>
</head>

<body>

    <h1><?= SITE_NAME ?></h1>

    <?php if ($user): ?>

        <h2>Xin chào, <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>! 👋</h2>

        <p>
            Bạn đã đăng nhập thành công.
        </p>

        <p>
            Email:
            <?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>
        </p>

        <p>
            Quyền:
            <strong>
                <?= htmlspecialchars($user['role_name'], ENT_QUOTES, 'UTF-8') ?>
            </strong>
        </p>

        <p>
            <a href="logout.php">Đăng xuất</a>
        </p>

    <?php else: ?>

        <p>Bạn chưa đăng nhập.</p>

        <p>
            <a href="login.php">Đăng nhập</a>
            |
            <a href="register.php">Đăng ký</a>
        </p>

    <?php endif; ?>

</body>

</html>
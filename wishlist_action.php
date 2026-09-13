<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user']['id'];
$productId = (int) ($_POST['product_id'] ?? $_GET['product_id'] ?? 0);
$action = $_POST['action'] ?? $_GET['action'] ?? 'toggle';

if ($productId <= 0) {
    header('Location: products.php');
    exit;
}

try {
    $productStmt = $pdo->prepare(
        "SELECT id
         FROM products
         WHERE id = :id
           AND status = 'active'
         LIMIT 1"
    );
    $productStmt->execute(['id' => $productId]);

    if (!$productStmt->fetchColumn()) {
        header('Location: products.php');
        exit;
    }

    $existsStmt = $pdo->prepare(
        "SELECT id
         FROM wishlists
         WHERE user_id = :user_id
           AND product_id = :product_id
         LIMIT 1"
    );
    $existsStmt->execute([
        'user_id' => $userId,
        'product_id' => $productId,
    ]);

    $wishlistId = $existsStmt->fetchColumn();

    if ($action === 'remove') {
        if ($wishlistId) {
            $stmt = $pdo->prepare(
                "DELETE FROM wishlists
                 WHERE id = :id
                   AND user_id = :user_id
                 LIMIT 1"
            );
            $stmt->execute([
                'id' => (int) $wishlistId,
                'user_id' => $userId,
            ]);
        }
    } elseif ($action === 'add') {
        if (!$wishlistId) {
            $stmt = $pdo->prepare(
                "INSERT INTO wishlists (user_id, product_id)
                 VALUES (:user_id, :product_id)"
            );
            $stmt->execute([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
        }
    } else {
        if ($wishlistId) {
            $stmt = $pdo->prepare(
                "DELETE FROM wishlists
                 WHERE id = :id
                   AND user_id = :user_id
                 LIMIT 1"
            );
            $stmt->execute([
                'id' => (int) $wishlistId,
                'user_id' => $userId,
            ]);
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO wishlists (user_id, product_id)
                 VALUES (:user_id, :product_id)"
            );
            $stmt->execute([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
        }
    }
} catch (PDOException $e) {
    // Không để lỗi DB làm lộ chi tiết hệ thống ra phía người dùng.
}

/*
 * Quay về trang trước nếu có.
 * Chỉ chấp nhận URL cùng host để tránh open redirect.
 */
$redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? '';

if ($redirect !== '') {
    $parts = parse_url($redirect);
    $safeRedirect =
        is_array($parts)
        && (!isset($parts['host']) || $parts['host'] === ($_SERVER['HTTP_HOST'] ?? ''))
        && (!isset($parts['scheme']) || $parts['scheme'] === '');

    if ($safeRedirect) {
        header('Location: ' . $redirect);
        exit;
    }
}

header('Location: wishlist.php');
exit;

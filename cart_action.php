<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/CartController.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user']['id'];

$action = $_POST['action'] ?? '';

$cartController = new CartController($pdo);

if ($action === 'add') {

    $productId = (int) ($_POST['product_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 1);

    $success = $cartController->add(
        $userId,
        $productId,
        $quantity
    );

    if ($success) {
        header('Location: cart.php?added=1');
    } else {
        header('Location: product_detail.php?id=' . $productId . '&error=cart');
    }

    exit;
}


if ($action === 'update') {

    $cartItemId = (int) ($_POST['cart_item_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 1);

    $success = $cartController->update(
        $userId,
        $cartItemId,
        $quantity
    );

    if ($success) {
        header('Location: cart.php?updated=1');
    } else {
        header('Location: cart.php?error=update');
    }

    exit;
}


if ($action === 'remove') {

    $cartItemId = (int) ($_POST['cart_item_id'] ?? 0);

    $cartController->remove(
        $userId,
        $cartItemId
    );

    header('Location: cart.php?removed=1');
    exit;
}


header('Location: cart.php');
exit;
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? SITE_NAME) ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8f9fa;
            color: #333333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1 0 auto;
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #0d6efd !important;
        }
        .badge-cart {
            font-size: 0.75rem;
            transform: translate(-20%, -30%);
        }
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
        }
        .btn-primary-custom {
            background-color: #0d6efd;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
        }
        .price-text {
            color: #dc3545;
            font-weight: 700;
        }
        .timeline {
            position: relative;
            padding-left: 2rem;
            list-style: none;
        }
        .timeline:before {
            content: '';
            position: absolute;
            left: 7px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .timeline-marker {
            position: absolute;
            left: -2rem;
            top: 2px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #adb5bd;
            border: 3px solid #fff;
        }
        .timeline-item.active .timeline-marker {
            background: #198754;
            box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.2);
        }
    </style>
</head>
<body>

<!-- Navbar điều hướng chính -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top shadow-sm py-2">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>">
            <i class="fa-solid fa-store fs-4"></i>
            <span><?= SITE_NAME ?></span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link fw-medium" href="<?= BASE_URL ?>"><i class="fa-solid fa-bag-shopping me-1"></i> Mua sắm</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-medium" href="<?= BASE_URL ?>?act=orders"><i class="fa-solid fa-receipt me-1"></i> Tra cứu đơn hàng</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <!-- Nút Giỏ hàng -->
                <?php 
                    $cartQty = CartController::getCartTotalQuantity();
                ?>
                <a href="<?= BASE_URL ?>?act=cart" class="btn btn-outline-primary position-relative rounded-pill px-3">
                    <i class="fa-solid fa-cart-shopping me-1"></i> Giỏ hàng
                    <?php if ($cartQty > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger badge-cart">
                            <?= $cartQty ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>
</nav>

<main class="py-4">
    <div class="container">
        <!-- Flash Message Thông Báo -->
        <?php $flash = getFlash(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <?php if ($flash['type'] === 'success'): ?>
                        <i class="fa-solid fa-circle-check fs-5 me-2"></i>
                    <?php elseif ($flash['type'] === 'danger'): ?>
                        <i class="fa-solid fa-circle-exclamation fs-5 me-2"></i>
                    <?php else: ?>
                        <i class="fa-solid fa-circle-info fs-5 me-2"></i>
                    <?php endif; ?>
                    <div><?= $flash['message'] ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

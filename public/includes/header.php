<?php
require_once __DIR__ . '/../../includes/functions.php';
$navCategories = getCategories(true);
$currentSlug = $_GET['category'] ?? '';
$keyword = $_GET['q'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? e($pageTitle) . ' - SwiftMart' : 'SwiftMart - Cửa hàng tiện lợi' ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>

<header class="site-header">
  <div class="container">
    <a href="<?= BASE_URL ?>index.php" class="logo">SwiftMart</a>

    <nav class="main-nav">
      <?php foreach ($navCategories as $cat): ?>
        <a href="<?= BASE_URL ?>category.php?category=<?= e($cat['slug']) ?>"
           class="<?= $currentSlug === $cat['slug'] ? 'active' : '' ?>">
          <?= e($cat['name']) ?>
        </a>
      <?php endforeach; ?>
      <a href="<?= BASE_URL ?>category.php?deals=1">Ưu đãi</a>
    </nav>

    <form class="search-form" action="<?= BASE_URL ?>category.php" method="get">
      <input type="text" name="q" placeholder="Tìm sản phẩm..." value="<?= e($keyword) ?>">
      <button type="submit" aria-label="Tìm kiếm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
          <circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
      </button>
    </form>

    <div class="header-icons">
      <a href="<?= BASE_URL ?>wishlist.php" aria-label="Yêu thích">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"></path></svg>
      </a>
      <a href="<?= BASE_URL ?>notifications.php" aria-label="Thông báo">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.7 21a2 2 0 0 1-3.4 0"></path></svg>
        <?php $unread = countUnreadNotifications($_SESSION['user_id'] ?? null); ?>
        <?php if ($unread > 0): ?><span class="badge-count"><?= $unread ?></span><?php endif; ?>
      </a>
      <a href="<?= BASE_URL ?>account.php" aria-label="Tài khoản">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c0-4 4-6 8-6s8 2 8 6"></path></svg>
      </a>
      <a href="<?= BASE_URL ?>cart.php" aria-label="Giỏ hàng">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.6 13.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6L23 6H6"></path></svg>
        <span class="badge-count" id="cart-count">0</span>
      </a>
    </div>
  </div>
</header>

<main class="container">

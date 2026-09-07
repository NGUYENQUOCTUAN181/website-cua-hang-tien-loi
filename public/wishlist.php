<?php
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = 'Sản phẩm yêu thích';

$loggedIn = !empty($_SESSION['user_id']);
$wishlistItems = $loggedIn ? getWishlistProducts($_SESSION['user_id']) : [];

include __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="section-head"><h2>Sản phẩm yêu thích</h2></div>

  <?php if (!$loggedIn): ?>
    <div class="empty-state">
      Vui lòng đăng nhập để xem danh sách yêu thích của bạn.<br><br>
      <a href="<?= BASE_URL ?>login.php" class="btn btn-primary">Đăng nhập</a>
    </div>
  <?php elseif ($wishlistItems): ?>
    <div class="product-grid">
      <?php foreach ($wishlistItems as $product): include __DIR__ . '/includes/product-card.php'; endforeach; ?>
    </div>
  <?php else: ?>
    <div class="empty-state">
      Bạn chưa có sản phẩm yêu thích nào.<br><br>
      <a href="<?= BASE_URL ?>category.php" class="btn btn-primary">Khám phá sản phẩm</a>
    </div>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

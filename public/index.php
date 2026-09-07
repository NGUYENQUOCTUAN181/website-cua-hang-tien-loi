<?php
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = 'Trang chủ';

$categories = getCategories(true);
$flashSale  = getFlashSaleProducts(4);
$featured   = getFeaturedProducts(8);
$newArrivals = getNewArrivals(4);

include __DIR__ . '/includes/header.php';
?>

<!-- Banner khuyến mãi -->
<section class="hero-banner">
  <span class="hero-tag">FLASH SALE</span>
  <h1>Mua sắm nhanh trong vài giây.</h1>
  <p>Giảm đến 40% cho đồ dùng gia đình và đồ ăn vặt mỗi ngày. Giao hàng nhanh, đảm bảo.</p>
  <a href="<?= BASE_URL ?>category.php?deals=1" class="btn btn-accent">Mua ngay</a>
  <div class="countdown" id="flash-countdown" data-end="<?= date('c', strtotime('+3 hours 14 minutes')) ?>">
    <span>Kết thúc trong</span>
    03:14:45
  </div>
</section>

<!-- Danh mục sản phẩm -->
<section class="section">
  <div class="section-head">
    <h2>Danh mục</h2>
    <a href="<?= BASE_URL ?>category.php">Xem tất cả</a>
  </div>
  <div class="category-grid">
    <?php foreach (array_slice($categories, 0, 3) as $cat): ?>
      <a href="<?= BASE_URL ?>category.php?category=<?= e($cat['slug']) ?>" class="category-card">
        <img src="<?= $cat['image'] ? resolveImageUrl($cat['image']) : 'https://placehold.co/400x300/EEE/999?text=' . urlencode($cat['name']) ?>" alt="<?= e($cat['name']) ?>">
        <div class="overlay"></div>
        <div class="label"><?= e($cat['name']) ?></div>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- Flash sale -->
<?php if ($flashSale): ?>
<section class="section">
  <div class="section-head">
    <h2>Flash Sale</h2>
    <a href="<?= BASE_URL ?>category.php?deals=1">Xem tất cả</a>
  </div>
  <div class="product-grid">
    <?php foreach ($flashSale as $product): include __DIR__ . '/includes/product-card.php'; endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- Sản phẩm nổi bật -->
<?php if ($featured): ?>
<section class="section">
  <div class="section-head">
    <h2>Sản phẩm nổi bật</h2>
    <a href="<?= BASE_URL ?>category.php?sort=bestseller">Xem tất cả</a>
  </div>
  <div class="product-grid">
    <?php foreach ($featured as $product): include __DIR__ . '/includes/product-card.php'; endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- Sản phẩm mới -->
<section class="section">
  <div class="section-head">
    <h2>New Arrivals</h2>
    <a href="<?= BASE_URL ?>category.php?sort=newest">Xem tất cả</a>
  </div>
  <div class="product-grid">
    <?php foreach ($newArrivals as $product): include __DIR__ . '/includes/product-card.php'; endforeach; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

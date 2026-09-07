<?php
/**
 * Expects $product (array from `products` table) in scope.
 * Optional: $product['avg_rating'] if available from getProductsList().
 */
$hasSale = !empty($product['sale_price']);
$finalPrice = $hasSale ? $product['sale_price'] : $product['price'];
$img = getPrimaryImage($product['id']);
$rating = $product['avg_rating'] ?? null;
?>
<div class="product-card">
  <a href="<?= BASE_URL ?>product.php?slug=<?= e($product['slug']) ?>">
    <div class="thumb">
      <?php if ($hasSale): ?>
        <span class="tag tag-sale">-<?= round((1 - $product['sale_price'] / $product['price']) * 100) ?>%</span>
      <?php elseif (!empty($product['is_featured'])): ?>
        <span class="tag tag-bestseller">Bán chạy</span>
      <?php endif; ?>
      <img src="<?= e($img) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
    </div>
  </a>
  <button class="wishlist-btn" data-product-id="<?= $product['id'] ?>" onclick="toggleWishlist(<?= $product['id'] ?>, this)" aria-label="Thêm vào yêu thích">♥</button>
  <div class="info">
    <a href="<?= BASE_URL ?>product.php?slug=<?= e($product['slug']) ?>">
      <p class="name"><?= e($product['name']) ?></p>
    </a>
    <?php if ($rating !== null): ?>
      <div class="meta"><span class="stars"><?= str_repeat('★', round($rating)) . str_repeat('☆', 5 - round($rating)) ?></span></div>
    <?php endif; ?>
    <div class="price-row">
      <div>
        <?php if ($hasSale): ?>
          <span class="price-old"><?= formatPrice($product['price']) ?></span>
          <span class="price on-sale"><?= formatPrice($finalPrice) ?></span>
        <?php else: ?>
          <span class="price"><?= formatPrice($finalPrice) ?></span>
        <?php endif; ?>
      </div>
      <button class="add-btn" onclick="addToCart(<?= $product['id'] ?>)" aria-label="Thêm vào giỏ">+</button>
    </div>
  </div>
</div>

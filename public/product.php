<?php
require_once __DIR__ . '/../includes/functions.php';

$slug = $_GET['slug'] ?? '';
$product = getProductBySlug($slug);

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Không tìm thấy sản phẩm';
    include __DIR__ . '/includes/header.php';
    echo '<div class="empty-state">Sản phẩm không tồn tại hoặc đã bị gỡ bỏ.</div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

// Xử lý gửi đánh giá (yêu cầu đăng nhập)
$reviewMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (empty($_SESSION['user_id'])) {
        $reviewMessage = 'Vui lòng đăng nhập để đánh giá sản phẩm.';
    } else {
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        if ($rating >= 1 && $rating <= 5 && $comment !== '') {
            addReview($product['id'], $_SESSION['user_id'], $rating, $comment);
            $reviewMessage = 'Cảm ơn bạn đã đánh giá!';
        } else {
            $reviewMessage = 'Vui lòng chọn số sao và nhập nội dung đánh giá.';
        }
    }
}

$images = getProductImages($product['id']);
if (!$images) $images = [['image_url' => '', 'is_primary' => 1]];
$avgRating = getAverageRating($product['id']);
$reviews = getProductReviews($product['id']);
$related = getRelatedProducts($product['category_id'], $product['id'], 4);
$hasSale = !empty($product['sale_price']);
$finalPrice = $hasSale ? $product['sale_price'] : $product['price'];
$inWishlist = isInWishlist($_SESSION['user_id'] ?? null, $product['id']);

$pageTitle = $product['name'];
include __DIR__ . '/includes/header.php';
?>

<div class="product-detail">
  <!-- Gallery -->
  <div>
    <div class="gallery-main">
      <img id="main-image" src="<?= resolveImageUrl($images[0]['image_url']) ?>" alt="<?= e($product['name']) ?>">
    </div>
    <?php if (count($images) > 1): ?>
    <div class="gallery-thumbs">
      <?php foreach ($images as $i => $img): ?>
        <img src="<?= resolveImageUrl($img['image_url']) ?>"
             class="<?= $i === 0 ? 'active' : '' ?>"
             onclick="document.getElementById('main-image').src=this.src; document.querySelectorAll('.gallery-thumbs img').forEach(el=>el.classList.remove('active')); this.classList.add('active');">
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- Thông tin sản phẩm -->
  <div>
    <h1 class="pd-title"><?= e($product['name']) ?></h1>
    <div class="pd-rating">
      <span class="stars"><?= str_repeat('★', round($avgRating['avg_rating'])) . str_repeat('☆', 5 - round($avgRating['avg_rating'])) ?></span>
      <span><?= number_format($avgRating['avg_rating'], 1) ?> (<?= $avgRating['total'] ?> đánh giá)</span>
    </div>

    <div class="pd-price-row">
      <?php if ($hasSale): ?>
        <span class="price-old" style="font-size:16px;"><?= formatPrice($product['price']) ?></span>
        <span class="pd-price" style="color:var(--danger);"><?= formatPrice($finalPrice) ?></span>
      <?php else: ?>
        <span class="pd-price"><?= formatPrice($finalPrice) ?></span>
      <?php endif; ?>
    </div>

    <?php if ($product['stock'] > 0): ?>
      <p class="pd-stock in">Còn hàng — <?= $product['stock'] ?> sản phẩm</p>
    <?php else: ?>
      <p class="pd-stock out">Hết hàng</p>
    <?php endif; ?>

    <div class="qty-box">
      <button type="button" onclick="stepQty(-1)">−</button>
      <input type="number" id="qty-input" value="1" min="1" max="<?= $product['stock'] ?>">
      <button type="button" onclick="stepQty(1)">+</button>
    </div>

    <div class="pd-actions">
      <button class="btn btn-primary" onclick="addToCart(<?= $product['id'] ?>, document.getElementById('qty-input').value)" <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>Thêm vào giỏ</button>
      <button class="btn btn-outline <?= $inWishlist ? 'active' : '' ?>" onclick="toggleWishlist(<?= $product['id'] ?>, this)">♥ Yêu thích</button>
    </div>

    <div class="pd-desc">
      <?= nl2br(e($product['description'])) ?>
    </div>
  </div>
</div>

<!-- Đánh giá & bình luận -->
<section class="section">
  <div class="section-head"><h2>Đánh giá sản phẩm</h2></div>

  <?php if ($reviewMessage): ?>
    <p style="color:var(--primary); font-weight:600;"><?= e($reviewMessage) ?></p>
  <?php endif; ?>

  <?php if ($reviews): ?>
    <?php foreach ($reviews as $rv): ?>
      <div class="review-item">
        <div class="review-head">
          <span><?= e($rv['user_name']) ?></span>
          <span class="stars"><?= str_repeat('★', $rv['rating']) . str_repeat('☆', 5 - $rv['rating']) ?></span>
        </div>
        <p style="margin:8px 0 0; color:var(--muted);"><?= e($rv['comment']) ?></p>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p style="color:var(--muted);">Chưa có đánh giá nào cho sản phẩm này.</p>
  <?php endif; ?>

  <form class="review-form" method="post" style="margin-top:24px;">
    <div class="star-input" id="star-input">
      <?php for ($s = 1; $s <= 5; $s++): ?>
        <span class="star" data-value="<?= $s ?>" onclick="setRating(<?= $s ?>)">★</span>
      <?php endfor; ?>
    </div>
    <input type="hidden" name="rating" id="rating-value" value="0">
    <textarea name="comment" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..." required></textarea>
    <button type="submit" name="submit_review" class="btn btn-primary" style="margin-top:12px;">Gửi đánh giá</button>
  </form>
</section>

<!-- Sản phẩm liên quan -->
<?php if ($related): ?>
<section class="section">
  <div class="section-head"><h2>Sản phẩm liên quan</h2></div>
  <div class="product-grid">
    <?php foreach ($related as $product): include __DIR__ . '/includes/product-card.php'; endforeach; ?>
  </div>
</section>
<?php endif; ?>

<script>
function setRating(value) {
  document.getElementById('rating-value').value = value;
  document.querySelectorAll('#star-input .star').forEach(function (el) {
    el.classList.toggle('selected', el.dataset.value <= value);
  });
}
function stepQty(delta) {
  var input = document.getElementById('qty-input');
  var val = Math.max(1, (parseInt(input.value) || 1) + delta);
  input.value = val;
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

<?php
require_once __DIR__ . '/../includes/functions.php';

$categorySlug = $_GET['category'] ?? '';
$keyword      = trim($_GET['q'] ?? '');
$brandId      = $_GET['brand'] ?? '';
$priceRange   = $_GET['price'] ?? '';   // 'under5' | '5-10' | '10-20' | ''
$minRating    = $_GET['rating'] ?? '';
$sort         = $_GET['sort'] ?? 'popularity';
$page         = max(1, (int)($_GET['page'] ?? 1));
$perPage      = 12;

$category = $categorySlug ? getCategoryBySlug($categorySlug) : null;
$subCategories = $category ? getSubCategories($category['id']) : [];
$brands = getBrands();

$filters = [];
if ($category) $filters['category_id'] = $category['id'];
if ($brandId)  $filters['brand_id'] = $brandId;
if ($keyword)  $filters['keyword'] = $keyword;
if ($minRating) $filters['min_rating'] = $minRating;

switch ($priceRange) {
  case 'under20k': $filters['max_price'] = 20000; break;
  case '20-50k':   $filters['min_price'] = 20000; $filters['max_price'] = 50000; break;
  case 'over50k':  $filters['min_price'] = 50000; break;
}

$result = getProductsList($filters, $sort, $page, $perPage);
$products = $result['items'];
$total = $result['total'];
$totalPages = max(1, ceil($total / $perPage));

$pageTitle = $keyword ? 'Kết quả tìm kiếm: ' . $keyword : ($category['name'] ?? 'Tất cả sản phẩm');

function buildQuery($overrides) {
    return http_build_query(array_merge($_GET, $overrides));
}

include __DIR__ . '/includes/header.php';
?>

<div class="listing-layout">
  <!-- Sidebar: lọc -->
  <aside>
    <div class="filter-box">
      <h3>Danh mục</h3>
      <label class="filter-option">
        <input type="checkbox" onclick="location.href='?<?= buildQuery(['category' => '', 'page' => 1]) ?>'" <?= !$category ? 'checked' : '' ?>>
        Tất cả
      </label>
      <?php foreach (getCategories(true) as $cat): ?>
        <label class="filter-option">
          <input type="checkbox" onclick="location.href='?<?= buildQuery(['category' => $cat['slug'], 'page' => 1]) ?>'" <?= ($category['slug'] ?? '') === $cat['slug'] ? 'checked' : '' ?>>
          <?= e($cat['name']) ?>
        </label>
      <?php endforeach; ?>
    </div>

    <?php if ($subCategories): ?>
    <div class="filter-box">
      <h3>Danh mục con</h3>
      <?php foreach ($subCategories as $sub): ?>
        <label class="filter-option">
          <input type="checkbox" onclick="location.href='?<?= buildQuery(['category' => $sub['slug'], 'page' => 1]) ?>'">
          <?= e($sub['name']) ?>
        </label>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="filter-box">
      <h3>Khoảng giá</h3>
      <?php $priceOptions = ['under20k' => 'Dưới 20.000đ', '20-50k' => '20.000đ - 50.000đ', 'over50k' => 'Trên 50.000đ']; ?>
      <?php foreach ($priceOptions as $key => $label): ?>
        <label class="filter-option">
          <input type="radio" name="price" onclick="location.href='?<?= buildQuery(['price' => $key, 'page' => 1]) ?>'" <?= $priceRange === $key ? 'checked' : '' ?>>
          <?= $label ?>
        </label>
      <?php endforeach; ?>
    </div>

    <div class="filter-box">
      <h3>Thương hiệu</h3>
      <?php foreach ($brands as $brand): ?>
        <label class="filter-option">
          <input type="checkbox" onclick="location.href='?<?= buildQuery(['brand' => $brand['id'], 'page' => 1]) ?>'" <?= (string)$brandId === (string)$brand['id'] ? 'checked' : '' ?>>
          <?= e($brand['name']) ?>
        </label>
      <?php endforeach; ?>
    </div>

    <div class="filter-box">
      <h3>Đánh giá</h3>
      <?php for ($r = 4; $r >= 1; $r--): ?>
        <label class="filter-option">
          <input type="radio" name="rating" onclick="location.href='?<?= buildQuery(['rating' => $r, 'page' => 1]) ?>'" <?= (string)$minRating === (string)$r ? 'checked' : '' ?>>
          <span class="stars"><?= str_repeat('★', $r) . str_repeat('☆', 5 - $r) ?></span> & Up
        </label>
      <?php endfor; ?>
    </div>
  </aside>

  <!-- Danh sách sản phẩm -->
  <div>
    <div class="listing-toolbar">
      <span>Hiển thị <strong><?= $total ?></strong> sản phẩm</span>
      <form method="get" id="sort-form">
        <?php foreach ($_GET as $k => $v) if ($k !== 'sort') echo '<input type="hidden" name="' . e($k) . '" value="' . e($v) . '">'; ?>
        <select name="sort" onchange="document.getElementById('sort-form').submit()">
          <option value="popularity" <?= $sort === 'popularity' ? 'selected' : '' ?>>Phổ biến</option>
          <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
          <option value="bestseller" <?= $sort === 'bestseller' ? 'selected' : '' ?>>Bán chạy</option>
          <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
          <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
        </select>
      </form>
    </div>

    <?php if ($products): ?>
      <div class="product-grid">
        <?php foreach ($products as $product): include __DIR__ . '/includes/product-card.php'; endforeach; ?>
      </div>

      <!-- Phân trang -->
      <?php if ($totalPages > 1): ?>
      <div class="pagination">
        <a href="?<?= buildQuery(['page' => max(1, $page - 1)]) ?>">‹</a>
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
          <a href="?<?= buildQuery(['page' => $p]) ?>" class="<?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
        <?php endfor; ?>
        <a href="?<?= buildQuery(['page' => min($totalPages, $page + 1)]) ?>">›</a>
      </div>
      <?php endif; ?>
    <?php else: ?>
      <div class="empty-state">Không tìm thấy sản phẩm phù hợp.</div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

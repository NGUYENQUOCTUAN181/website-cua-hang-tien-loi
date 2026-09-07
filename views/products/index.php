<?php
$pageTitle = "Trang Chủ - " . SITE_NAME;
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Banner Chào Mừng -->
<div class="p-4 p-md-5 mb-4 rounded-4 text-white bg-primary bg-gradient shadow-sm">
    <div class="col-md-8 px-0">
        <h1 class="display-5 fw-bold"><i class="fa-solid fa-basket-shopping me-2"></i> Cửa Hàng Tiện Lợi Online</h1>
        <p class="lead my-3">Mua sắm tiện lợi, giao hàng nhanh chóng trong 30 phút. Đầy đủ các mặt hàng thực phẩm, nước uống và đồ dùng thiết yếu.</p>
        <div class="d-flex gap-2">
            <a href="#productList" class="btn btn-warning btn-lg rounded-pill fw-bold text-dark px-4">
                Mua Sắm Ngay <i class="fa-solid fa-arrow-down ms-1"></i>
            </a>
            <a href="<?= BASE_URL ?>?act=cart" class="btn btn-outline-light btn-lg rounded-pill px-4">
                Xem Giỏ Hàng <i class="fa-solid fa-cart-shopping ms-1"></i>
            </a>
        </div>
    </div>
</div>

<!-- Danh sách Sản Phẩm -->
<div id="productList" class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-fire text-danger me-2"></i> Sản Phẩm Nổi Bật</h3>
            <p class="text-muted mb-0">Chọn sản phẩm thêm vào giỏ hàng để trải nghiệm tính năng của Thành Viên 3</p>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $p): 
                $hasSale = !empty($p['sale_price']) && (float)$p['sale_price'] > 0 && (float)$p['sale_price'] < (float)$p['price'];
                $outOfStock = (int)$p['stock'] <= 0;
            ?>
                <div class="col">
                    <div class="card h-100 card-custom bg-white position-relative">
                        <!-- Badge giảm giá nếu có -->
                        <?php if ($hasSale): 
                            $percent = round((((float)$p['price'] - (float)$p['sale_price']) / (float)$p['price']) * 100);
                        ?>
                            <span class="position-absolute top-0 start-0 badge bg-danger m-2 px-2 py-1 rounded-pill">
                                -<?= $percent ?>%
                            </span>
                        <?php endif; ?>

                        <img src="<?= htmlspecialchars($p['image']) ?>" 
                             class="card-img-top p-2 rounded" 
                             alt="<?= htmlspecialchars($p['name']) ?>" 
                             style="height: 200px; object-fit: cover;">

                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title fw-bold text-dark text-truncate" title="<?= htmlspecialchars($p['name']) ?>">
                                <?= htmlspecialchars($p['name']) ?>
                            </h6>
                            <p class="card-text text-muted small flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= htmlspecialchars($p['description'] ?? '') ?>
                            </p>

                            <div class="d-flex align-items-baseline gap-2 mb-2">
                                <span class="price-text fs-5">
                                    <?= formatPrice($p['effective_price']) ?>
                                </span>
                                <?php if ($hasSale): ?>
                                    <span class="text-muted text-decoration-line-through small">
                                        <?= formatPrice($p['price']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <small class="text-muted">
                                    <i class="fa-solid fa-boxes-stacked me-1"></i> Kho: 
                                    <strong class="<?= $outOfStock ? 'text-danger' : 'text-success' ?>">
                                        <?= (int)$p['stock'] ?>
                                    </strong>
                                </small>
                            </div>

                            <!-- Nút Thêm vào giỏ -->
                            <form action="<?= BASE_URL ?>?act=add-to-cart" method="POST" class="mt-auto">
                                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <?php if ($outOfStock): ?>
                                    <button type="button" class="btn btn-secondary w-100 rounded-pill fw-semibold" disabled>
                                        <i class="fa-solid fa-ban me-1"></i> Tạm hết hàng
                                    </button>
                                <?php else: ?>
                                    <button type="submit" class="btn btn-outline-primary w-100 rounded-pill fw-semibold">
                                        <i class="fa-solid fa-cart-plus me-1"></i> Thêm vào giỏ
                                    </button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">Chưa có sản phẩm nào trong hệ thống. Vui lòng chạy file script SQL <code>member3_patch_and_seed.sql</code> để đổ dữ liệu mẫu.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

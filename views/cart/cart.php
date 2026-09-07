<?php
$pageTitle = "Giỏ Hàng - " . SITE_NAME;
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="row g-4">
    <!-- Tiêu đề trang -->
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Giỏ hàng của bạn</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0"><i class="fa-solid fa-cart-shopping text-primary me-2"></i> Giỏ Hàng</h2>
    </div>

    <?php if (empty($cart)): ?>
        <!-- Giao diện khi giỏ hàng trống -->
        <div class="col-12">
            <div class="card card-custom p-5 text-center bg-white">
                <div class="mb-4">
                    <i class="fa-solid fa-cart-arrow-down text-muted" style="font-size: 5rem;"></i>
                </div>
                <h4 class="fw-bold text-dark">Giỏ hàng của bạn đang trống</h4>
                <p class="text-muted mb-4">Hãy dạo một vòng cửa hàng và chọn những món đồ bạn yêu thích nhé!</p>
                <div>
                    <a href="<?= BASE_URL ?>" class="btn btn-primary btn-primary-custom px-4 py-2">
                        <i class="fa-solid fa-arrow-left me-2"></i> Khám Phá Sản Phẩm Ngay
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Cột Trái: Bảng danh sách sản phẩm trong giỏ -->
        <div class="col-lg-8">
            <div class="card card-custom bg-white p-4">
                <form action="<?= BASE_URL ?>?act=update-cart" method="POST" id="cartForm">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="min-width: 250px;">Sản phẩm</th>
                                    <th scope="col" class="text-center">Đơn giá</th>
                                    <th scope="col" class="text-center" style="width: 140px;">Số lượng</th>
                                    <th scope="col" class="text-end">Thành tiền</th>
                                    <th scope="col" class="text-center" style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart as $id => $item): 
                                    $itemSubtotal = $item['price'] * $item['quantity'];
                                ?>
                                    <tr>
                                        <!-- Thông tin sản phẩm & Ảnh -->
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= htmlspecialchars($item['image']) ?>" 
                                                     alt="<?= htmlspecialchars($item['name']) ?>" 
                                                     class="rounded border" 
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0 fw-semibold text-dark"><?= htmlspecialchars($item['name']) ?></h6>
                                                    <small class="text-muted">Mã SP: #<?= $item['id'] ?></small>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Đơn giá -->
                                        <td class="text-center fw-medium">
                                            <?= formatPrice($item['price']) ?>
                                        </td>

                                        <!-- Số lượng (Bộ nút Tăng / Giảm) -->
                                        <td class="text-center">
                                            <div class="input-group input-group-sm">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        onclick="decrementQty(<?= $id ?>)">
                                                    <i class="fa-solid fa-minus"></i>
                                                </button>
                                                <input type="number" 
                                                       name="quantity[<?= $id ?>]" 
                                                       id="qty_<?= $id ?>" 
                                                       value="<?= (int)$item['quantity'] ?>" 
                                                       min="0" 
                                                       class="form-control text-center fw-bold" 
                                                       style="max-width: 60px;">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        onclick="incrementQty(<?= $id ?>)">
                                                    <i class="fa-solid fa-plus"></i>
                                                </button>
                                            </div>
                                        </td>

                                        <!-- Thành tiền từng món -->
                                        <td class="text-end fw-bold text-danger">
                                            <?= formatPrice($itemSubtotal) ?>
                                        </td>

                                        <!-- Nút xóa món -->
                                        <td class="text-center">
                                            <a href="<?= BASE_URL ?>?act=delete-cart&id=<?= $id ?>" 
                                               class="btn btn-sm btn-outline-danger border-0" 
                                               title="Xóa món này" 
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?');">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Các nút thao tác với giỏ -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4 pt-3 border-top">
                        <a href="<?= BASE_URL ?>" class="btn btn-outline-secondary rounded-pill">
                            <i class="fa-solid fa-arrow-left me-1"></i> Tiếp tục mua hàng
                        </a>
                        <div class="d-flex gap-2">
                            <a href="<?= BASE_URL ?>?act=clear-cart" 
                               class="btn btn-outline-danger rounded-pill" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa sạch toàn bộ giỏ hàng?');">
                                <i class="fa-solid fa-trash me-1"></i> Xóa sạch giỏ
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-3">
                                <i class="fa-solid fa-rotate me-1"></i> Cập nhật giỏ hàng
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cột Phải: Áp dụng Voucher & Tóm tắt tổng tiền thanh toán -->
        <div class="col-lg-4">
            <!-- Box Áp Dụng Mã Giảm Giá (Voucher) -->
            <div class="card card-custom bg-white p-4 mb-3">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-ticket text-warning me-2"></i> Khuyến Mãi / Voucher</h5>
                
                <?php if (!empty($voucher)): ?>
                    <!-- Nếu đã áp dụng mã thành công -->
                    <div class="alert alert-success d-flex justify-content-between align-items-center mb-0">
                        <div>
                            <div class="fw-bold"><i class="fa-solid fa-tag me-1"></i> <?= htmlspecialchars($voucher['code']) ?></div>
                            <small class="text-success"><?= htmlspecialchars($voucher['description'] ?: 'Đã áp dụng mã giảm giá') ?></small>
                        </div>
                        <a href="<?= BASE_URL ?>?act=remove-voucher" class="btn btn-sm btn-outline-danger" title="Gỡ mã">
                            <i class="fa-solid fa-xmark"></i> Gỡ
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Form nhập mã giảm giá -->
                    <form action="<?= BASE_URL ?>?act=apply-voucher" method="POST">
                        <div class="input-group">
                            <input type="text" 
                                   name="voucher_code" 
                                   class="form-control text-uppercase" 
                                   placeholder="Nhập mã giảm giá..." 
                                   required>
                            <button class="btn btn-warning text-dark fw-semibold" type="submit">
                                Áp dụng
                            </button>
                        </div>
                        <div class="form-text mt-2">
                            <i class="fa-solid fa-circle-info me-1"></i> Gợi ý mã test: 
                            <span class="badge bg-light text-dark border">CHAOBANMOI</span>, 
                            <span class="badge bg-light text-dark border">GIAM20K</span>,
                            <span class="badge bg-light text-dark border">FREESHIP</span>
                        </div>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Box Tóm Tắt Thanh Toán -->
            <div class="card card-custom bg-white p-4">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i> Tóm Tắt Đơn Hàng</h5>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tạm tính:</span>
                    <span class="fw-semibold"><?= formatPrice($subtotal) ?></span>
                </div>

                <?php if ($discount > 0): ?>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Giảm giá (Voucher):</span>
                        <span class="fw-semibold">-<?= formatPrice($discount) ?></span>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Phí giao hàng:</span>
                    <span class="text-success fw-semibold">Miễn phí</span>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fw-bold fs-5">Tổng cộng:</span>
                    <span class="price-text fs-4"><?= formatPrice($total) ?></span>
                </div>

                <a href="<?= BASE_URL ?>?act=checkout" class="btn btn-success btn-lg w-100 rounded-pill fw-bold py-2 shadow-sm">
                    Tiến Hành Thanh Toán <i class="fa-solid fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Tăng số lượng sản phẩm
function incrementQty(productId) {
    const input = document.getElementById('qty_' + productId);
    if (input) {
        input.value = parseInt(input.value || 0) + 1;
    }
}

// Giảm số lượng sản phẩm
function decrementQty(productId) {
    const input = document.getElementById('qty_' + productId);
    if (input && parseInt(input.value) > 0) {
        input.value = parseInt(input.value) - 1;
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

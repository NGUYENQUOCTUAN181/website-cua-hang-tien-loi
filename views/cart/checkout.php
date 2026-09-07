<?php
$pageTitle = "Thanh Toán Đơn Hàng - " . SITE_NAME;
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="row g-4">
    <!-- Breadcrumb -->
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?act=cart" class="text-decoration-none">Giỏ hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thanh toán</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0"><i class="fa-solid fa-credit-card text-success me-2"></i> Thanh Toán & Đặt Hàng</h2>
    </div>

    <!-- Form Submit Đặt Hàng -->
    <form action="<?= BASE_URL ?>?act=process-checkout" method="POST" class="col-12" id="checkoutForm">
        <div class="row g-4">
            <!-- CỘT 1: THÔNG TIN GIAO HÀNG & PHƯƠNG THỨC THANH TOÁN -->
            <div class="col-lg-7">
                <!-- Khối 1: Thông tin người nhận -->
                <div class="card card-custom bg-white p-4 mb-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">
                        <i class="fa-solid fa-location-dot text-danger me-2"></i> Thông Tin Giao Hàng
                    </h5>

                    <div class="row g-3">
                        <!-- Họ và tên -->
                        <div class="col-md-6">
                            <label for="customer_name" class="form-label fw-semibold">Họ và tên người nhận <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="customer_name" 
                                   name="customer_name" 
                                   value="<?= htmlspecialchars($user['name'] ?? '') ?>" 
                                   placeholder="Ví dụ: Nguyễn Văn A" 
                                   required>
                        </div>

                        <!-- Số điện thoại -->
                        <div class="col-md-6">
                            <label for="customer_phone" class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" 
                                   class="form-control" 
                                   id="customer_phone" 
                                   name="customer_phone" 
                                   value="<?= htmlspecialchars($user['phone'] ?? ($_SESSION['customer_phone_lookup'] ?? '')) ?>" 
                                   placeholder="Ví dụ: 0912345678" 
                                   pattern="[0-9]{9,11}" 
                                   required>
                        </div>

                        <!-- Email -->
                        <div class="col-12">
                            <label for="customer_email" class="form-label fw-semibold">Địa chỉ Email</label>
                            <input type="email" 
                                   class="form-control" 
                                   id="customer_email" 
                                   name="customer_email" 
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>" 
                                   placeholder="email@example.com (Dùng nhận thông báo đơn hàng)">
                        </div>

                        <!-- Địa chỉ chi tiết -->
                        <div class="col-12">
                            <label for="shipping_address" class="form-label fw-semibold">Địa chỉ nhận hàng chi tiết <span class="text-danger">*</span></label>
                            <textarea class="form-control" 
                                      id="shipping_address" 
                                      name="shipping_address" 
                                      rows="3" 
                                      placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố..." 
                                      required></textarea>
                        </div>

                        <!-- Ghi chú đơn hàng -->
                        <div class="col-12">
                            <label for="note" class="form-label fw-semibold">Ghi chú giao hàng (Tùy chọn)</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="note" 
                                   name="note" 
                                   placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi đến...">
                        </div>
                    </div>
                </div>

                <!-- Khối 2: Phương thức thanh toán -->
                <div class="card card-custom bg-white p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">
                        <i class="fa-solid fa-wallet text-primary me-2"></i> Phương Thức Thanh Toán
                    </h5>

                    <div class="d-flex flex-column gap-3">
                        <!-- Lựa chọn 1: COD -->
                        <div class="form-check p-3 border rounded-3 bg-light">
                            <input class="form-check-input mt-1" 
                                   type="radio" 
                                   name="payment_method" 
                                   id="payment_cod" 
                                   value="COD" 
                                   checked 
                                   onchange="toggleBankingInfo(false)">
                            <label class="form-check-label d-flex align-items-center justify-content-between w-100 cursor-pointer" for="payment_cod">
                                <div>
                                    <span class="fw-bold d-block text-dark">Thanh toán khi nhận hàng (COD)</span>
                                    <small class="text-muted">Bạn chỉ thanh toán tiền mặt khi nhân viên giao hàng đến nơi.</small>
                                </div>
                                <i class="fa-solid fa-hand-holding-dollar fs-3 text-success"></i>
                            </label>
                        </div>

                        <!-- Lựa chọn 2: Chuyển khoản ngân hàng -->
                        <div class="form-check p-3 border rounded-3 bg-light">
                            <input class="form-check-input mt-1" 
                                   type="radio" 
                                   name="payment_method" 
                                   id="payment_banking" 
                                   value="BANKING" 
                                   onchange="toggleBankingInfo(true)">
                            <label class="form-check-label d-flex align-items-center justify-content-between w-100 cursor-pointer" for="payment_banking">
                                <div>
                                    <span class="fw-bold d-block text-dark">Chuyển khoản Ngân hàng (VietQR / Banking)</span>
                                    <small class="text-muted">Chuyển khoản nhanh qua tài khoản ngân hàng hoặc quét mã QR.</small>
                                </div>
                                <i class="fa-solid fa-building-columns fs-3 text-primary"></i>
                            </label>
                        </div>

                        <!-- Thông tin chuyển khoản (Ẩn/Hiện) -->
                        <div id="bankingDetails" class="alert alert-info border-info mt-2" style="display: none;">
                            <h6 class="fw-bold text-primary mb-2"><i class="fa-solid fa-qrcode me-1"></i> Thông Tin Tài Khoản Nhận Thanh Toán:</h6>
                            <ul class="mb-2 list-unstyled small">
                                <li><strong>Ngân hàng:</strong> MB Bank (Ngân hàng Quân Đội)</li>
                                <li><strong>Số tài khoản:</strong> <code>0988889999</code></li>
                                <li><strong>Chủ tài khoản:</strong> CỬA HÀNG TIỆN LỢI</li>
                                <li><strong>Cú pháp chuyển khoản:</strong> [Số điện thoại] - DH</li>
                            </ul>
                            <small class="text-muted fst-italic">* Sau khi bấm Đặt hàng, hệ thống sẽ tạo mã đơn hàng cụ thể để bạn đối soát.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CỘT 2: TÓM TẮT ĐƠN HÀNG (ORDER SUMMARY) -->
            <div class="col-lg-5">
                <div class="card card-custom bg-white p-4 sticky-top" style="top: 80px;">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fa-solid fa-basket-shopping text-warning me-2"></i> Tóm Tắt Đơn Hàng
                        </h5>
                        <a href="<?= BASE_URL ?>?act=cart" class="btn btn-sm btn-outline-secondary">Sửa giỏ hàng</a>
                    </div>

                    <!-- Danh sách các món -->
                    <div class="cart-items-summary mb-3" style="max-height: 280px; overflow-y: auto;">
                        <?php foreach ($cart as $item): ?>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" 
                                         alt="<?= htmlspecialchars($item['name']) ?>" 
                                         class="rounded border" 
                                         style="width: 45px; height: 45px; object-fit: cover;">
                                    <div>
                                        <div class="fw-semibold small text-dark"><?= htmlspecialchars($item['name']) ?></div>
                                        <small class="text-muted">SL: <?= (int)$item['quantity'] ?> &times; <?= formatPrice($item['price']) ?></small>
                                    </div>
                                </div>
                                <span class="fw-bold small text-dark">
                                    <?= formatPrice($item['price'] * $item['quantity']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Tính toán chi phí -->
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tạm tính:</span>
                        <span class="fw-semibold"><?= formatPrice($subtotal) ?></span>
                    </div>

                    <?php if ($discount > 0 && !empty($voucher)): ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>
                                <i class="fa-solid fa-tag me-1"></i> Mã giảm (<strong><?= htmlspecialchars($voucher['code']) ?></strong>):
                            </span>
                            <span class="fw-semibold">-<?= formatPrice($discount) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Phí vận chuyển:</span>
                        <span class="text-success fw-semibold">0 ₫ (Miễn phí)</span>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold fs-5">Tổng thanh toán:</span>
                        <span class="price-text fs-3"><?= formatPrice($total) ?></span>
                    </div>

                    <!-- Nút Xác Nhận Đặt Hàng -->
                    <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold py-3 shadow">
                        <i class="fa-solid fa-check-circle me-2"></i> Xác Nhận Đặt Hàng
                    </button>

                    <div class="text-center text-muted small mt-3">
                        <i class="fa-solid fa-shield-halved text-success me-1"></i> Thông tin đơn hàng được bảo mật an toàn 100%
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Chuyển đổi hiển thị hướng dẫn Banking khi người dùng chọn phương thức thanh toán
function toggleBankingInfo(isBanking) {
    const box = document.getElementById('bankingDetails');
    if (box) {
        box.style.display = isBanking ? 'block' : 'none';
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

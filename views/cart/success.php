<?php
$pageTitle = "Đặt Hàng Thành Công - " . SITE_NAME;
require_once __DIR__ . '/../layouts/header.php';

$orderStatusText = [
    'pending'   => 'Chờ xác nhận',
    'confirmed' => 'Đã xác nhận',
    'shipping'  => 'Đang giao hàng',
    'completed' => 'Đã hoàn thành',
    'cancelled' => 'Đã hủy'
];
$statusKey = $order['status'] ?? $order['order_status'] ?? 'pending';
$statusName = $orderStatusText[$statusKey] ?? $statusKey;
$isBanking = in_array(strtoupper($order['payment_method']), ['BANKING', 'BANK_TRANSFER']);
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Card Thông Báo Thành Công -->
        <div class="card card-custom bg-white p-4 p-md-5 text-center mb-4">
            <div class="mb-3">
                <i class="fa-solid fa-circle-check text-success" style="font-size: 4.5rem;"></i>
            </div>
            <h2 class="fw-bold text-dark">Đặt Hàng Thành Công!</h2>
            <p class="text-muted fs-5 mb-3">
                Cảm ơn bạn đã tin tưởng và ủng hộ <strong><?= SITE_NAME ?></strong>.
            </p>
            <div class="d-inline-block bg-light border rounded-pill px-4 py-2 mx-auto mb-3">
                <span class="text-muted">Mã đơn hàng của bạn:</span>
                <strong class="text-primary fs-5 ms-2">#<?= htmlspecialchars($order['order_code']) ?></strong>
            </div>
            <p class="text-secondary small mb-0">
                Trạng thái: <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill"><?= $statusName ?></span>
            </p>
        </div>

        <?php if ($isBanking): ?>
            <!-- Hướng Dẫn Chuyển Khoản Ngân Hàng (Nếu chọn Banking) -->
            <div class="card card-custom bg-white p-4 mb-4 border-primary">
                <h5 class="fw-bold text-primary mb-3">
                    <i class="fa-solid fa-qrcode me-2"></i> Thông Tin Chuyển Khoản Thanh Toán
                </h5>
                <div class="row align-items-center g-3">
                    <div class="col-md-5 text-center">
                        <!-- VietQR động tạo ảnh QR với số tài khoản, số tiền và nội dung mã đơn hàng -->
                        <?php 
                            $vietQrUrl = "https://img.vietqr.io/image/MB-0988889999-compact2.png?amount=" . (int)$order['total'] . "&addInfo=" . urlencode($order['order_code']) . "&accountName=CUA+HANG+TIEN+LOI";
                        ?>
                        <img src="<?= $vietQrUrl ?>" alt="Mã QR Chuyển Khoản" class="img-fluid rounded border shadow-sm" style="max-height: 240px;">
                        <div class="small text-muted mt-1">Mở app Ngân hàng để quét mã QR</div>
                    </div>
                    <div class="col-md-7">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Ngân hàng:</span>
                                <strong>MB Bank (Ngân hàng Quân Đội)</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Số tài khoản:</span>
                                <strong class="text-primary fs-6">0988889999</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Chủ tài khoản:</span>
                                <strong>CỬA HÀNG TIỆN LỢI</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Số tiền cần chuyển:</span>
                                <strong class="text-danger fs-5"><?= formatPrice($order['total'] ?? $order['final_amount']) ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Nội dung chuyển khoản:</span>
                                <strong class="badge bg-secondary fs-6"><?= htmlspecialchars($order['order_code']) ?></strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Thông Tin Đơn Hàng & Người Nhận -->
        <div class="card card-custom bg-white p-4 mb-4">
            <h5 class="fw-bold mb-3 border-bottom pb-2">
                <i class="fa-solid fa-list-check text-primary me-2"></i> Chi Tiết Đơn Hàng
            </h5>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <p class="mb-1 text-muted small">Người nhận:</p>
                    <p class="fw-bold mb-0"><?= htmlspecialchars($order['receiver_name'] ?? $order['customer_name']) ?></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1 text-muted small">Số điện thoại:</p>
                    <p class="fw-bold mb-0"><?= htmlspecialchars($order['receiver_phone'] ?? $order['customer_phone']) ?></p>
                </div>
                <div class="col-12">
                    <p class="mb-1 text-muted small">Địa chỉ nhận hàng:</p>
                    <p class="fw-bold mb-0"><?= htmlspecialchars($order['shipping_address']) ?></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1 text-muted small">Phương thức thanh toán:</p>
                    <p class="fw-bold mb-0">
                        <?= $isBanking ? 'Chuyển khoản ngân hàng' : 'Thanh toán khi nhận hàng (COD)' ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1 text-muted small">Thời gian đặt:</p>
                    <p class="fw-bold mb-0"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                </div>
            </div>

            <!-- Bảng sản phẩm đã mua -->
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-center" style="width: 100px;">Đơn giá</th>
                            <th class="text-center" style="width: 80px;">SL</th>
                            <th class="text-end" style="width: 120px;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderItems as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?= htmlspecialchars($item['image']) ?>" 
                                             class="rounded border" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                        <span class="fw-semibold text-dark"><?= htmlspecialchars($item['product_name']) ?></span>
                                    </div>
                                </td>
                                <td class="text-center"><?= formatPrice($item['price']) ?></td>
                                <td class="text-center"><?= (int)$item['quantity'] ?></td>
                                <td class="text-end fw-bold"><?= formatPrice($item['subtotal']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end text-muted">Tạm tính:</td>
                            <td class="text-end fw-semibold"><?= formatPrice($order['subtotal'] ?? $order['total_amount']) ?></td>
                        </tr>
                        <?php if ((float)($order['discount'] ?? $order['discount_amount']) > 0): ?>
                            <tr>
                                <td colspan="3" class="text-end text-success">
                                    Giảm giá (<?= htmlspecialchars($order['voucher_code'] ?? 'Voucher') ?>):
                                </td>
                                <td class="text-end text-success fw-semibold">
                                    -<?= formatPrice($order['discount'] ?? $order['discount_amount']) ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold fs-5">Tổng thanh toán:</td>
                            <td class="text-end fw-bold text-danger fs-5">
                                <?= formatPrice($order['total'] ?? $order['final_amount']) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Các nút điều hướng -->
            <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
                <a href="<?= BASE_URL ?>?act=order-detail&id=<?= $order['id'] ?>" class="btn btn-outline-primary px-4 rounded-pill">
                    <i class="fa-solid fa-eye me-1"></i> Theo dõi trạng thái đơn hàng
                </a>
                <a href="<?= BASE_URL ?>" class="btn btn-primary px-4 rounded-pill">
                    <i class="fa-solid fa-bag-shopping me-1"></i> Tiếp tục mua hàng
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

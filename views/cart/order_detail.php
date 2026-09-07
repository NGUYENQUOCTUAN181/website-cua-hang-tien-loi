<?php
$pageTitle = "Chi Tiết Đơn Hàng #" . ($order['order_code'] ?? '') . " - " . SITE_NAME;
require_once __DIR__ . '/../layouts/header.php';

$statusKey = $order['status'] ?? $order['order_status'] ?? 'pending';

// Thứ tự các bước trong tiến trình đơn hàng
$steps = [
    'pending'   => ['label' => 'Đã đặt hàng', 'icon' => 'fa-clipboard-list', 'desc' => 'Đơn hàng đang chờ nhân viên xác nhận'],
    'confirmed' => ['label' => 'Đã xác nhận', 'icon' => 'fa-circle-check', 'desc' => 'Cửa hàng đã chuẩn bị hàng đóng gói'],
    'shipping'  => ['label' => 'Đang giao hàng', 'icon' => 'fa-truck', 'desc' => 'Shipper đang trên đường giao đến bạn'],
    'completed' => ['label' => 'Giao thành công', 'icon' => 'fa-box-open', 'desc' => 'Đơn hàng đã được nhận và thanh toán']
];

$stepKeys = array_keys($steps);
$currentIndex = array_search($statusKey, $stepKeys);
if ($currentIndex === false && $statusKey !== 'cancelled') {
    $currentIndex = 0;
}
?>

<div class="row g-4">
    <!-- Header Điều hướng -->
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?act=orders" class="text-decoration-none">Lịch sử đơn hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Đơn #<?= htmlspecialchars($order['order_code']) ?></li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h2 class="fw-bold mb-1">
                    Đơn Hàng #<?= htmlspecialchars($order['order_code']) ?>
                </h2>
                <small class="text-muted">
                    Thời gian đặt: <?= date('d/m/Y H:i:s', strtotime($order['created_at'])) ?>
                </small>
            </div>

            <!-- Nút Hủy Đơn Hàng (Chỉ bật khi status = 'pending') -->
            <?php if ($statusKey === 'pending'): ?>
                <button type="button" class="btn btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="fa-solid fa-ban me-1"></i> Hủy đơn hàng này
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- KHỐI THEO DÕI TIẾN TRÌNH TRẠNG THÁI ĐƠN HÀNG (TIMELINE / TRACKING) -->
    <div class="col-12">
        <div class="card card-custom bg-white p-4">
            <h5 class="fw-bold mb-4 border-bottom pb-2">
                <i class="fa-solid fa-route text-primary me-2"></i> Theo Dõi Trạng Thái Đơn Hàng
            </h5>

            <?php if ($statusKey === 'cancelled'): ?>
                <div class="alert alert-danger d-flex align-items-center mb-0">
                    <i class="fa-solid fa-circle-xmark fs-2 me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Đơn hàng này đã bị hủy</h6>
                        <small><?= htmlspecialchars($order['note'] ?: 'Đơn hàng đã được hủy và hoàn trả tồn kho.') ?></small>
                    </div>
                </div>
            <?php else: ?>
                <!-- Tiến trình các bước -->
                <div class="row text-center position-relative g-3">
                    <?php foreach ($steps as $k => $info): 
                        $stepIndex = array_search($k, $stepKeys);
                        $isPassed = $stepIndex <= $currentIndex;
                        $isCurrent = $stepIndex === $currentIndex;
                    ?>
                        <div class="col-6 col-md-3">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-2 shadow-sm 
                                    <?= $isPassed ? ($isCurrent ? 'bg-primary text-white ring' : 'bg-success text-white') : 'bg-light text-muted border' ?>" 
                                     style="width: 50px; height: 50px; font-size: 1.25rem;">
                                    <i class="fa-solid <?= $info['icon'] ?>"></i>
                                </div>
                                <span class="fw-bold small <?= $isPassed ? 'text-dark' : 'text-muted' ?>">
                                    <?= $info['label'] ?>
                                </span>
                                <small class="text-secondary d-none d-md-block" style="font-size: 0.75rem;">
                                    <?= $info['desc'] ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Cột Trái: Danh sách sản phẩm trong đơn -->
    <div class="col-lg-8">
        <div class="card card-custom bg-white p-4">
            <h5 class="fw-bold mb-3 border-bottom pb-2">
                <i class="fa-solid fa-box text-warning me-2"></i> Sản Phẩm Đã Mua
            </h5>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-center">Đơn giá</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderItems as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= htmlspecialchars($item['image']) ?>" 
                                             class="rounded border" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 fw-semibold text-dark"><?= htmlspecialchars($item['product_name']) ?></h6>
                                            <small class="text-muted">Mã SP: #<?= $item['product_id'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center"><?= formatPrice($item['price']) ?></td>
                                <td class="text-center fw-bold"><?= (int)$item['quantity'] ?></td>
                                <td class="text-end fw-bold text-danger"><?= formatPrice($item['subtotal']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tổng kết tiền đơn hàng -->
            <div class="row justify-content-end mt-4">
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Tạm tính:</span>
                            <span class="fw-semibold"><?= formatPrice($order['subtotal'] ?? $order['total_amount']) ?></span>
                        </li>
                        <?php if ((float)($order['discount'] ?? $order['discount_amount']) > 0): ?>
                            <li class="list-group-item d-flex justify-content-between px-0 text-success">
                                <span>Voucher giảm giá (<?= htmlspecialchars($order['voucher_code']) ?>):</span>
                                <span class="fw-semibold">-<?= formatPrice($order['discount'] ?? $order['discount_amount']) ?></span>
                            </li>
                        <?php endif; ?>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Phí vận chuyển:</span>
                            <span class="text-success fw-semibold">0 ₫</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0 border-top pt-2">
                            <span class="fw-bold fs-5">Tổng tiền thanh toán:</span>
                            <span class="price-text fs-4"><?= formatPrice($order['total'] ?? $order['final_amount']) ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Cột Phải: Thông tin người nhận & Thanh toán -->
    <div class="col-lg-4">
        <div class="card card-custom bg-white p-4 mb-4">
            <h5 class="fw-bold mb-3 border-bottom pb-2">
                <i class="fa-solid fa-user text-primary me-2"></i> Người Nhận Hàng
            </h5>
            <p class="mb-2"><strong>Họ tên:</strong> <?= htmlspecialchars($order['receiver_name'] ?? $order['customer_name']) ?></p>
            <p class="mb-2"><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['receiver_phone'] ?? $order['customer_phone']) ?></p>
            <?php if (!empty($order['customer_email'])): ?>
                <p class="mb-2"><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
            <?php endif; ?>
            <p class="mb-0"><strong>Địa chỉ giao:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
        </div>

        <div class="card card-custom bg-white p-4">
            <h5 class="fw-bold mb-3 border-bottom pb-2">
                <i class="fa-solid fa-credit-card text-success me-2"></i> Thanh Toán & Ghi Chú
            </h5>
            <p class="mb-2">
                <strong>Phương thức:</strong> 
                <span class="badge bg-secondary"><?= htmlspecialchars($order['payment_method']) ?></span>
            </p>
            <p class="mb-2">
                <strong>Thanh toán:</strong> 
                <span class="badge <?= ($order['payment_status'] === 'paid') ? 'bg-success' : 'bg-warning text-dark' ?>">
                    <?= ($order['payment_status'] === 'paid') ? 'Đã thanh toán' : 'Chưa thanh toán' ?>
                </span>
            </p>
            <?php if (!empty($order['note'])): ?>
                <p class="mb-0"><strong>Ghi chú:</strong> <span class="text-muted"><?= htmlspecialchars($order['note']) ?></span></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Xác Nhận Hủy Đơn Hàng -->
<?php if ($statusKey === 'pending'): ?>
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= BASE_URL ?>?act=cancel-order" method="POST" class="modal-content">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-danger" id="cancelModalLabel">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Xác Nhận Hủy Đơn Hàng
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Bạn có chắc chắn muốn hủy đơn hàng <strong>#<?= htmlspecialchars($order['order_code']) ?></strong> không?</p>
                <div class="mb-3">
                    <label for="reason" class="form-label fw-semibold">Lý do hủy đơn:</label>
                    <select name="reason" id="reason" class="form-select" required>
                        <option value="Tôi muốn đổi địa chỉ giao hàng">Tôi muốn đổi địa chỉ giao hàng</option>
                        <option value="Tôi muốn thêm/bớt sản phẩm">Tôi muốn thêm/bớt sản phẩm</option>
                        <option value="Tôi tìm thấy giá rẻ hơn ở nơi khác">Tôi tìm thấy giá rẻ hơn ở nơi khác</option>
                        <option value="Tôi đổi ý không muốn mua nữa">Tôi đổi ý không muốn mua nữa</option>
                        <option value="Lý do khác">Lý do khác</option>
                    </select>
                </div>
                <small class="text-muted">* Sau khi hủy, sản phẩm sẽ được tự động hoàn lại tồn kho cho người khác mua.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-danger rounded-pill px-4">Xác Nhận Hủy</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

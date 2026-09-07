<?php
$pageTitle = "Lịch Sử Đơn Hàng - " . SITE_NAME;
require_once __DIR__ . '/../layouts/header.php';

$statusBadges = [
    'pending'   => '<span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="fa-solid fa-clock me-1"></i> Chờ xác nhận</span>',
    'confirmed' => '<span class="badge bg-info text-dark px-3 py-2 rounded-pill"><i class="fa-solid fa-clipboard-check me-1"></i> Đã xác nhận</span>',
    'shipping'  => '<span class="badge bg-primary px-3 py-2 rounded-pill"><i class="fa-solid fa-truck-fast me-1"></i> Đang giao</span>',
    'completed' => '<span class="badge bg-success px-3 py-2 rounded-pill"><i class="fa-solid fa-circle-check me-1"></i> Đã hoàn thành</span>',
    'cancelled' => '<span class="badge bg-danger px-3 py-2 rounded-pill"><i class="fa-solid fa-circle-xmark me-1"></i> Đã hủy</span>'
];
?>

<div class="row g-4">
    <!-- Tiêu đề & Thanh tra cứu -->
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Lịch sử đơn hàng</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h2 class="fw-bold mb-0"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Lịch Sử Mua Hàng</h2>
            
            <!-- Khung tìm kiếm đơn hàng theo SĐT / Email -->
            <form action="<?= BASE_URL ?>" method="GET" class="d-flex gap-2">
                <input type="hidden" name="act" value="orders">
                <div class="input-group">
                    <input type="text" 
                           name="phone" 
                           class="form-control" 
                           placeholder="Nhập SĐT hoặc Email để tra cứu..." 
                           value="<?= htmlspecialchars($searchPhone ?? '') ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Tra cứu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách đơn hàng -->
    <div class="col-12">
        <?php if (empty($orders)): ?>
            <div class="card card-custom bg-white p-5 text-center">
                <div class="mb-3 text-muted">
                    <i class="fa-solid fa-folder-open" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold text-dark">Chưa tìm thấy đơn hàng nào</h5>
                <p class="text-muted mb-4">
                    <?= !empty($searchPhone) ? 'Không có đơn hàng nào khớp với thông tin "' . htmlspecialchars($searchPhone) . '".' : 'Hãy nhập số điện thoại hoặc email bạn đã dùng khi đặt hàng ở thanh tìm kiếm phía trên để tra cứu.' ?>
                </p>
                <div>
                    <a href="<?= BASE_URL ?>" class="btn btn-primary rounded-pill px-4">
                        <i class="fa-solid fa-bag-shopping me-1"></i> Mua sắm ngay
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="card card-custom bg-white p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã đơn</th>
                                <th>Ngày đặt</th>
                                <th>Người nhận & Địa chỉ</th>
                                <th class="text-center">Thanh toán</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-end">Tổng tiền</th>
                                <th class="text-center" style="width: 130px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $ord): 
                                $statusKey = $ord['status'] ?? $ord['order_status'] ?? 'pending';
                                $badge = $statusBadges[$statusKey] ?? "<span class='badge bg-secondary'>{$statusKey}</span>";
                            ?>
                                <tr>
                                    <td>
                                        <strong class="text-primary">#<?= htmlspecialchars($ord['order_code']) ?></strong>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($ord['receiver_name'] ?? $ord['customer_name']) ?></div>
                                        <small class="text-muted d-block text-truncate" style="max-width: 250px;">
                                            <?= htmlspecialchars($ord['shipping_address']) ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">
                                            <?= htmlspecialchars($ord['payment_method']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?= $badge ?>
                                    </td>
                                    <td class="text-end fw-bold text-danger">
                                        <?= formatPrice($ord['total'] ?? $ord['final_amount']) ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= BASE_URL ?>?act=order-detail&id=<?= $ord['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fa-solid fa-eye me-1"></i> Chi tiết
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

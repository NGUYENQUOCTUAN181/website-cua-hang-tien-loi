<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';
require_once __DIR__ . '/../models/AdminVoucher.php';

$voucherModel = new AdminVoucher($pdo);

$action = $_POST['action'] ?? '';


if (
    $action === 'create' ||
    $action === 'update'
) {

    $code = strtoupper(trim(
        $_POST['code'] ?? ''
    ));

    $description = trim(
        $_POST['description'] ?? ''
    );

    $discountType =
        $_POST['discount_type'] ?? '';

    $discountValue =
        (float) ($_POST['discount_value'] ?? 0);

    $minOrderValue =
        (float) ($_POST['min_order_value'] ?? 0);

    $maxDiscountRaw =
        trim($_POST['max_discount'] ?? '');

    $maxDiscount =
        $maxDiscountRaw === ''
            ? null
            : (float) $maxDiscountRaw;

    $quantity =
        (int) ($_POST['quantity'] ?? 0);

    $startDate =
        $_POST['start_date'] ?? '';

    $endDate =
        $_POST['end_date'] ?? '';

    $status =
        $_POST['status'] ?? 'active';


    if ($code === '') {
        die('Mã voucher không được để trống.');
    }

    if (!preg_match(
        '/^[A-Z0-9_-]{3,50}$/',
        $code
    )) {
        die(
            'Mã voucher chỉ được chứa A-Z, 0-9, _ hoặc -.'
        );
    }

    if (!in_array(
        $discountType,
        ['percent', 'fixed'],
        true
    )) {
        die('Loại giảm giá không hợp lệ.');
    }

    if ($discountValue <= 0) {
        die('Giá trị giảm phải lớn hơn 0.');
    }

    if (
        $discountType === 'percent' &&
        $discountValue > 100
    ) {
        die(
            'Giảm phần trăm không được lớn hơn 100%.'
        );
    }

    if ($minOrderValue < 0) {
        die('Đơn tối thiểu không hợp lệ.');
    }

    if (
        $maxDiscount !== null &&
        $maxDiscount < 0
    ) {
        die('Giảm tối đa không hợp lệ.');
    }

    if ($quantity <= 0) {
        die('Số lượng voucher phải lớn hơn 0.');
    }

    if (!in_array(
        $status,
        ['active', 'inactive'],
        true
    )) {
        die('Trạng thái không hợp lệ.');
    }

    if ($startDate === '' || $endDate === '') {
        die('Vui lòng nhập thời gian voucher.');
    }

    if (
        strtotime($endDate) <=
        strtotime($startDate)
    ) {
        die(
            'Thời gian kết thúc phải sau thời gian bắt đầu.'
        );
    }


    if ($action === 'update') {

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            die('ID voucher không hợp lệ.');
        }

        $voucherModel->update(
            $id,
            $code,
            $description,
            $discountType,
            $discountValue,
            $minOrderValue,
            $maxDiscount,
            $quantity,
            $startDate,
            $endDate,
            $status
        );

    } else {

        $voucherModel->create(
            $code,
            $description,
            $discountType,
            $discountValue,
            $minOrderValue,
            $maxDiscount,
            $quantity,
            $startDate,
            $endDate,
            $status
        );
    }

    header(
        'Location: vouchers.php?success=1'
    );

    exit;
}


if ($action === 'delete') {

    $role =
        $_SESSION['user']['role_name'] ?? '';

    if (!in_array(
        $role,
        ['SUPER_ADMIN', 'ADMIN'],
        true
    )) {
        http_response_code(403);
        die(
            'Bạn không có quyền ngừng voucher.'
        );
    }

    $id =
        (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        die('ID voucher không hợp lệ.');
    }

    $voucherModel->delete($id);

    header(
        'Location: vouchers.php?deleted=1'
    );

    exit;
}


header('Location: vouchers.php');
exit;
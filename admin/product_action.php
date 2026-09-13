<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';
require_once __DIR__ . '/../models/AdminProduct.php';

$productModel = new AdminProduct($pdo);

$action = $_POST['action'] ?? '';


// ============================================================
// CREATE / UPDATE
// ============================================================

if (
    $action === 'create' ||
    $action === 'update'
) {

    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $brandIdRaw = $_POST['brand_id'] ?? '';
    $brandId = $brandIdRaw === ''
        ? null
        : (int) $brandIdRaw;

    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $price = (float) ($_POST['price'] ?? 0);

    $salePriceRaw = trim(
        $_POST['sale_price'] ?? ''
    );

    $salePrice = $salePriceRaw === ''
        ? null
        : (float) $salePriceRaw;

    $stock = (int) ($_POST['stock'] ?? 0);

    $status = $_POST['status'] ?? 'active';


    // --------------------------------------------------------
    // VALIDATION
    // --------------------------------------------------------

    if ($categoryId <= 0) {
        die('Danh mục không hợp lệ.');
    }

    if ($name === '') {
        die('Tên sản phẩm không được để trống.');
    }

    if ($price < 0) {
        die('Giá sản phẩm không hợp lệ.');
    }

    if (
        $salePrice !== null &&
        $salePrice < 0
    ) {
        die('Giá sale không hợp lệ.');
    }

    if (
        $salePrice !== null &&
        $salePrice > $price
    ) {
        die('Giá sale không được lớn hơn giá gốc.');
    }

    if ($stock < 0) {
        die('Tồn kho không hợp lệ.');
    }

    if (!in_array(
        $status,
        ['active', 'inactive'],
        true
    )) {
        die('Trạng thái không hợp lệ.');
    }


    // --------------------------------------------------------
    // AUTO SLUG
    // --------------------------------------------------------

    if ($slug === '') {

        $slug = strtolower(
            trim(
                preg_replace(
                    '/[^a-z0-9]+/i',
                    '-',
                    iconv(
                        'UTF-8',
                        'ASCII//TRANSLIT',
                        $name
                    )
                ),
                '-'
            )
        );

    }


    // --------------------------------------------------------
    // UPDATE
    // --------------------------------------------------------

    if ($action === 'update') {

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            die('ID sản phẩm không hợp lệ.');
        }

        $productModel->update(
            $id,
            $categoryId,
            $brandId,
            $name,
            $slug,
            $description,
            $price,
            $salePrice,
            $stock,
            $status
        );

        header(
            'Location: products.php?success=updated'
        );

        exit;
    }


    // --------------------------------------------------------
    // CREATE
    // --------------------------------------------------------

    $productModel->create(
        $categoryId,
        $brandId,
        $name,
        $slug,
        $description,
        $price,
        $salePrice,
        $stock,
        $status
    );

    header(
        'Location: products.php?success=created'
    );

    exit;
}


// ============================================================
// SOFT DELETE
// ============================================================

if ($action === 'delete') {

    $role = $_SESSION['user']['role_name'] ?? '';

    if (!in_array(
        $role,
        ['SUPER_ADMIN', 'ADMIN'],
        true
    )) {
        http_response_code(403);
        die(
            'Bạn không có quyền ngừng bán sản phẩm.'
        );
    }

    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        die('ID sản phẩm không hợp lệ.');
    }

    $productModel->delete($id);

    header(
        'Location: products.php?deleted=1'
    );

    exit;
}


header('Location: products.php');
exit;
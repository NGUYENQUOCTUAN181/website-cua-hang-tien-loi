<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';
require_once __DIR__ . '/../models/AdminBrand.php';

$brandModel = new AdminBrand($pdo);

$action = $_POST['action'] ?? '';

if (
    $action === 'create' ||
    $action === 'update'
) {

    $name = trim($_POST['name'] ?? '');

    $description =
        trim($_POST['description'] ?? '');

    $status =
        $_POST['status'] ?? 'active';

    if ($name === '') {
        die('Tên thương hiệu không được để trống.');
    }

    if (!in_array(
        $status,
        ['active', 'inactive'],
        true
    )) {
        die('Trạng thái không hợp lệ.');
    }

    if ($action === 'update') {

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            die('ID thương hiệu không hợp lệ.');
        }

        $brandModel->update(
            $id,
            $name,
            $description,
            $status
        );

    } else {

        $brandModel->create(
            $name,
            $description,
            $status
        );
    }

    header('Location: brands.php?success=1');
    exit;
}


if ($action === 'delete') {

    $role = $_SESSION['user']['role_name'] ?? '';

    if (!in_array(
        $role,
        ['SUPER_ADMIN', 'ADMIN'],
        true
    )) {
        http_response_code(403);
        die('Bạn không có quyền ngừng thương hiệu.');
    }

    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        die('ID thương hiệu không hợp lệ.');
    }

    $brandModel->delete($id);

    header('Location: brands.php?deleted=1');
    exit;
}

header('Location: brands.php');
exit;
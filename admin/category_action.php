<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';
require_once __DIR__ . '/../models/AdminCategory.php';

$categoryModel = new AdminCategory($pdo);

$action = $_POST['action'] ?? '';

if (
    $action === 'create' ||
    $action === 'update'
) {

    $parentIdRaw = $_POST['parent_id'] ?? '';

    $parentId = $parentIdRaw === ''
        ? null
        : (int) $parentIdRaw;

    $name = trim($_POST['name'] ?? '');

    $description =
        trim($_POST['description'] ?? '');

    $status =
        $_POST['status'] ?? 'active';

    if ($name === '') {
        die('Tên danh mục không được để trống.');
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
            die('ID danh mục không hợp lệ.');
        }

        if (
            $parentId !== null &&
            $parentId === $id
        ) {
            die('Danh mục không thể là cha của chính nó.');
        }

        $categoryModel->update(
            $id,
            $parentId,
            $name,
            $description,
            $status
        );

    } else {

        $categoryModel->create(
            $parentId,
            $name,
            $description,
            $status
        );
    }

    header('Location: categories.php?success=1');
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
        die('Bạn không có quyền ngừng danh mục.');
    }

    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        die('ID danh mục không hợp lệ.');
    }

    $categoryModel->delete($id);

    header('Location: categories.php?deleted=1');
    exit;
}

header('Location: categories.php');
exit;
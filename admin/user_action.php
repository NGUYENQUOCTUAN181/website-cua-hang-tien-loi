<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';
require_once __DIR__ . '/../models/AdminUser.php';

$userModel = new AdminUser($pdo);

$action = $_POST['action'] ?? '';

$currentRole = $_SESSION['user']['role_name'] ?? '';

if (!in_array(
    $currentRole,
    ['SUPER_ADMIN', 'ADMIN'],
    true
)) {
    http_response_code(403);
    die('Bạn không có quyền quản lý người dùng.');
}


$userId = (int) ($_POST['user_id'] ?? 0);

if ($userId <= 0) {
    die('ID người dùng không hợp lệ.');
}


/*
|--------------------------------------------------------------------------
| ĐỔI ROLE
|--------------------------------------------------------------------------
*/

if ($action === 'role') {

    $roleId = (int) ($_POST['role_id'] ?? 0);

    if ($roleId <= 0) {
        die('Role không hợp lệ.');
    }

    /*
     * ADMIN không được nâng user lên SUPER_ADMIN.
     */
    $targetRole = null;

    $roles = $userModel->getRoles();

    foreach ($roles as $role) {

        if ((int) $role['id'] === $roleId) {
            $targetRole = $role['name'];
            break;
        }
    }

    if (!$targetRole) {
        die('Không tìm thấy role.');
    }

    if (
        $currentRole === 'ADMIN' &&
        $targetRole === 'SUPER_ADMIN'
    ) {
        http_response_code(403);
        die(
            'ADMIN không có quyền cấp SUPER_ADMIN.'
        );
    }

    /*
     * ADMIN không được sửa tài khoản SUPER_ADMIN.
     */
    $targetUser = $userModel->findById($userId);

    if (!$targetUser) {
        die('Không tìm thấy người dùng.');
    }

    if (
        $currentRole === 'ADMIN' &&
        $targetUser['role_name'] === 'SUPER_ADMIN'
    ) {
        http_response_code(403);
        die(
            'ADMIN không được sửa tài khoản SUPER_ADMIN.'
        );
    }

    $userModel->updateRole(
        $userId,
        $roleId
    );

    header(
        'Location: users.php?success=1'
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| KHÓA / MỞ KHÓA
|--------------------------------------------------------------------------
*/

if ($action === 'status') {

    $status = $_POST['status'] ?? '';

    if (!in_array(
        $status,
        ['active', 'locked'],
        true
    )) {
        die('Trạng thái không hợp lệ.');
    }

    /*
     * Không được tự khóa chính mình.
     */
    if (
        $userId ===
        (int) $_SESSION['user']['id']
    ) {
        die(
            'Bạn không thể tự khóa tài khoản của mình.'
        );
    }

    $targetUser =
        $userModel->findById($userId);

    if (!$targetUser) {
        die('Không tìm thấy người dùng.');
    }

    /*
     * ADMIN không được khóa SUPER_ADMIN.
     */
    if (
        $currentRole === 'ADMIN' &&
        $targetUser['role_name'] === 'SUPER_ADMIN'
    ) {
        http_response_code(403);
        die(
            'ADMIN không được khóa SUPER_ADMIN.'
        );
    }

    $userModel->updateStatus(
        $userId,
        $status
    );

    header(
        'Location: users.php?success=1'
    );

    exit;
}


header('Location: users.php');
exit;
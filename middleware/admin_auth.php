<?php

require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

$allowedRoles = [
    'SUPER_ADMIN',
    'ADMIN',
    'STAFF'
];

$userRole = $_SESSION['user']['role_name'] ?? '';

if (!in_array($userRole, $allowedRoles, true)) {
    http_response_code(403);
    die('Bạn không có quyền truy cập khu vực quản trị.');
}
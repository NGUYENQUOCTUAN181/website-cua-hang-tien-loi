<?php
require_once __DIR__ . '/../../includes/functions.php';
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['user_id'];

if (!empty($data['all'])) {
    markAllNotificationsRead($userId);
    echo json_encode(['success' => true]);
    exit;
}

$notificationId = (int)($data['id'] ?? 0);
if ($notificationId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Thiếu id.']);
    exit;
}

markNotificationRead($notificationId, $userId);
echo json_encode(['success' => true]);

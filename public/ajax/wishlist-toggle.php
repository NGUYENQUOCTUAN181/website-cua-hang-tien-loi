<?php
require_once __DIR__ . '/../../includes/functions.php';
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để dùng chức năng yêu thích.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$productId = (int)($data['product_id'] ?? 0);

if ($productId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Thiếu product_id.']);
    exit;
}

$result = toggleWishlistItem($_SESSION['user_id'], $productId);
echo json_encode(['success' => true, 'action' => $result['action']]);

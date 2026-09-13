<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/admin_auth.php';

$reviewId =
    (int) ($_POST['review_id'] ?? 0);

$status =
    $_POST['status'] ?? '';

$allowedStatuses = [
    'pending',
    'approved',
    'hidden'
];

if ($reviewId <= 0) {
    die('Review không hợp lệ.');
}

if (!in_array(
    $status,
    $allowedStatuses,
    true
)) {
    die('Trạng thái review không hợp lệ.');
}

$sql = "UPDATE reviews
        SET
            status = :status,
            updated_at = NOW()
        WHERE id = :id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    'id' => $reviewId,
    'status' => $status
]);

header(
    'Location: reviews.php?success=1'
);

exit;
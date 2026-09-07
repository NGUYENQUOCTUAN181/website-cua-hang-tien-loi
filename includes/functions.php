<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

function formatPrice($price) {
    return number_format((float)$price, 0, ',', '.') . 'đ';
}

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function getCategories($parentOnly = true) {
    global $pdo;
    $sql = "SELECT * FROM categories WHERE status = 1";
    if ($parentOnly) $sql .= " AND parent_id IS NULL";
    $sql .= " ORDER BY name ASC";
    return $pdo->query($sql)->fetchAll();
}

function getSubCategories($parentId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? AND status = 1 ORDER BY name ASC");
    $stmt->execute([$parentId]);
    return $stmt->fetchAll();
}

function getCategoryBySlug($slug) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    return $stmt->fetch() ?: null;
}

function getBrands() {
    global $pdo;
    return $pdo->query("SELECT * FROM brands ORDER BY name ASC")->fetchAll();
}

function getBanners($position = 'home_slider') {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM banners WHERE status = 1 AND position = ?
                            AND (start_date IS NULL OR start_date <= CURDATE())
                            AND (end_date IS NULL OR end_date >= CURDATE())
                            ORDER BY sort_order ASC");
    $stmt->execute([$position]);
    return $stmt->fetchAll();
}

function resolveImageUrl($path) {
    if (!$path) return BASE_URL . 'assets/images/no-image.png';
    if (preg_match('#^https?://#i', $path)) return $path;
    return UPLOAD_URL . '../' . $path;
}

function getPrimaryImage($productId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ?
                            ORDER BY is_primary DESC, sort_order ASC LIMIT 1");
    $stmt->execute([$productId]);
    $row = $stmt->fetch();
    return resolveImageUrl($row ? $row['image_url'] : null);
}

function getProductImages($productId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ?
                            ORDER BY is_primary DESC, sort_order ASC");
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}

function getAverageRating($productId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COALESCE(AVG(rating),0) AS avg_rating, COUNT(*) AS total
                            FROM reviews WHERE product_id = ? AND status = 1");
    $stmt->execute([$productId]);
    return $stmt->fetch();
}

function getFeaturedProducts($limit = 8) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE status = 1 AND is_featured = 1
                            ORDER BY created_at DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getNewArrivals($limit = 8) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE status = 1
                            ORDER BY created_at DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getFlashSaleProducts($limit = 8) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE status = 1 AND sale_price IS NOT NULL
                            ORDER BY (price - sale_price) DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getRelatedProducts($categoryId, $excludeId, $limit = 4) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = :cat AND id != :id AND status = 1
                            ORDER BY sold_count DESC LIMIT :limit");
    $stmt->bindValue(':cat', $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getProductBySlug($slug) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ? AND status = 1 LIMIT 1");
    $stmt->execute([$slug]);
    return $stmt->fetch() ?: null;
}

function getProductReviews($productId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT r.*, u.name AS user_name FROM reviews r
                            JOIN users u ON u.id = r.user_id
                            WHERE r.product_id = ? AND r.status = 1
                            ORDER BY r.created_at DESC");
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}

function addReview($productId, $userId, $rating, $comment) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO reviews (product_id, user_id, rating, comment, status)
                            VALUES (?, ?, ?, ?, 1)");
    return $stmt->execute([$productId, $userId, $rating, $comment]);
}

function isInWishlist($userId, $productId) {
    global $pdo;
    if (!$userId) return false;
    $stmt = $pdo->prepare("SELECT 1 FROM wishlists WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    return (bool)$stmt->fetch();
}

/* ================= WISHLIST ================= */

function getWishlistProducts($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.* FROM wishlists w
                            JOIN products p ON p.id = w.product_id
                            WHERE w.user_id = ? ORDER BY w.created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Bật/tắt 1 sản phẩm trong wishlist của user.
 * Trả về ['action' => 'added'|'removed']
 */
function toggleWishlistItem($userId, $productId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    $row = $stmt->fetch();

    if ($row) {
        $del = $pdo->prepare("DELETE FROM wishlists WHERE id = ?");
        $del->execute([$row['id']]);
        return ['action' => 'removed'];
    }

    $ins = $pdo->prepare("INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)");
    $ins->execute([$userId, $productId]);
    return ['action' => 'added'];
}

/* ================= NOTIFICATIONS ================= */

function getUserNotifications($userId, $limit = 30) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ?
                            ORDER BY created_at DESC LIMIT :limit");
    $stmt->bindValue(1, $userId);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function countUnreadNotifications($userId) {
    global $pdo;
    if (!$userId) return 0;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$userId]);
    return (int)$stmt->fetchColumn();
}

function markNotificationRead($notificationId, $userId) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    return $stmt->execute([$notificationId, $userId]);
}

function markAllNotificationsRead($userId) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
    return $stmt->execute([$userId]);
}

/** Tạo thông báo mới cho user — dùng khi có khuyến mãi hoặc đơn hàng đổi trạng thái */
function createNotification($userId, $title, $message = null) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)");
    return $stmt->execute([$userId, $title, $message]);
}

/* ================= CONTACT / HỖ TRỢ ================= */

function submitContactMessage($name, $email, $subject, $message, $userId = null) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO contact_messages (user_id, name, email, subject, message)
                            VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$userId, $name, $email, $subject, $message]);
}

/**
 * Danh sách sản phẩm có lọc / sắp xếp / phân trang
 * $filters: category_id, brand_id, min_price, max_price, min_rating, keyword
 * $sort: price_asc | price_desc | newest | bestseller | popularity
 */
function getProductsList($filters = [], $sort = 'popularity', $page = 1, $perPage = 12) {
    global $pdo;
    $where = ["p.status = 1"];
    $params = [];

    if (!empty($filters['category_id'])) {
        $where[] = "p.category_id = :category_id";
        $params[':category_id'] = $filters['category_id'];
    }
    if (!empty($filters['brand_id'])) {
        $where[] = "p.brand_id = :brand_id";
        $params[':brand_id'] = $filters['brand_id'];
    }
    if (isset($filters['min_price']) && $filters['min_price'] !== '') {
        $where[] = "COALESCE(p.sale_price, p.price) >= :min_price";
        $params[':min_price'] = $filters['min_price'];
    }
    if (isset($filters['max_price']) && $filters['max_price'] !== '') {
        $where[] = "COALESCE(p.sale_price, p.price) <= :max_price";
        $params[':max_price'] = $filters['max_price'];
    }
    if (!empty($filters['keyword'])) {
        $where[] = "p.name LIKE :keyword";
        $params[':keyword'] = '%' . $filters['keyword'] . '%';
    }

    $having = '';
    if (!empty($filters['min_rating'])) {
        $having = "HAVING avg_rating >= :min_rating";
        $params[':min_rating'] = $filters['min_rating'];
    }

    switch ($sort) {
        case 'price_asc':  $orderBy = 'final_price ASC'; break;
        case 'price_desc': $orderBy = 'final_price DESC'; break;
        case 'newest':     $orderBy = 'p.created_at DESC'; break;
        case 'bestseller': $orderBy = 'p.sold_count DESC'; break;
        default:           $orderBy = 'p.is_featured DESC, p.sold_count DESC';
    }

    $whereSql = implode(' AND ', $where);

    $sql = "SELECT p.*, COALESCE(p.sale_price, p.price) AS final_price,
                   COALESCE(AVG(r.rating), 0) AS avg_rating
            FROM products p
            LEFT JOIN reviews r ON r.product_id = p.id AND r.status = 1
            WHERE $whereSql
            GROUP BY p.id
            $having
            ORDER BY $orderBy
            LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) $stmt->bindValue($key, $val);
    $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)(($page - 1) * $perPage), PDO::PARAM_INT);
    $stmt->execute();
    $items = $stmt->fetchAll();

    $countSql = "SELECT COUNT(*) FROM (
                    SELECT p.id, COALESCE(AVG(r.rating),0) AS avg_rating
                    FROM products p
                    LEFT JOIN reviews r ON r.product_id = p.id AND r.status = 1
                    WHERE $whereSql
                    GROUP BY p.id
                    $having
                 ) t";
    $countStmt = $pdo->prepare($countSql);
    foreach ($params as $key => $val) $countStmt->bindValue($key, $val);
    $countStmt->execute();
    $total = (int)$countStmt->fetchColumn();

    return ['items' => $items, 'total' => $total];
}

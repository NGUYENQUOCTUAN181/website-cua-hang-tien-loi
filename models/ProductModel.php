<?php
/**
 * Model: Quản lý Sản phẩm (ProductModel)
 * Module: Thành Viên 3 - Client
 */

require_once __DIR__ . '/../config/database.php';

class ProductModel {
    private $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    /**
     * Lấy chi tiết một sản phẩm theo ID
     * Bao gồm thông tin giá gốc, giá khuyến mãi, tồn kho và ảnh sản phẩm
     *
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $sql = "SELECT p.*, 
                       COALESCE(pi.image_url, 'https://placehold.co/400x300?text=No+Image') AS image
                FROM products p
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                WHERE p.id = :id AND p.status = 'active'
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => (int)$id]);
        $product = $stmt->fetch();

        if ($product) {
            // Xác định giá bán thực tế (ưu tiên sale_price nếu có giá khuyến mãi hợp lệ)
            $product['effective_price'] = $this->calculateEffectivePrice($product);
        }

        return $product;
    }

    /**
     * Lấy danh sách tất cả sản phẩm đang hoạt động để hiển thị mua sắm
     *
     * @return array
     */
    public function getAllActive() {
        $sql = "SELECT p.*, 
                       COALESCE(pi.image_url, 'https://placehold.co/400x300?text=Product') AS image
                FROM products p
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                WHERE p.status = 'active'
                ORDER BY p.id DESC";

        $stmt = $this->db->query($sql);
        $products = $stmt->fetchAll();

        foreach ($products as &$item) {
            $item['effective_price'] = $this->calculateEffectivePrice($item);
        }

        return $products;
    }

    /**
     * Tính giá bán áp dụng: nếu có sale_price > 0 và nhỏ hơn price thì lấy sale_price
     *
     * @param array $product
     * @return float
     */
    public function calculateEffectivePrice($product) {
        $price = (float)$product['price'];
        $salePrice = isset($product['sale_price']) && $product['sale_price'] !== null ? (float)$product['sale_price'] : 0;

        if ($salePrice > 0 && $salePrice < $price) {
            return $salePrice;
        }

        return $price;
    }

    /**
     * Kiểm tra nhanh số lượng tồn kho của một sản phẩm
     *
     * @param int $id
     * @param int $requestedQty
     * @return bool
     */
    public function isStockAvailable($id, $requestedQty) {
        $sql = "SELECT stock FROM products WHERE id = :id AND status = 'active' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => (int)$id]);
        $stock = $stmt->fetchColumn();

        if ($stock === false) {
            return false;
        }

        return (int)$stock >= (int)$requestedQty;
    }
}

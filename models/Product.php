<?php

class Product
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy danh sách sản phẩm
     */
    public function getAll(): array
    {
        $sql = "SELECT
                    p.*,
                    c.name AS category_name,
                    b.name AS brand_name
                FROM products p
                LEFT JOIN categories c
                    ON p.category_id = c.id
                LEFT JOIN brands b
                    ON p.brand_id = b.id
                WHERE p.status = 'active'
                ORDER BY p.created_at DESC";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    /**
     * Lấy sản phẩm theo ID
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT
                    p.*,
                    c.name AS category_name,
                    b.name AS brand_name
                FROM products p
                LEFT JOIN categories c
                    ON p.category_id = c.id
                LEFT JOIN brands b
                    ON p.brand_id = b.id
                WHERE p.id = :id
                AND p.status = 'active'
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $product = $stmt->fetch();

        return $product ?: null;
    }

    /**
     * Tìm kiếm sản phẩm
     */
    public function search(string $keyword): array
    {
        $sql = "SELECT
                    p.*,
                    c.name AS category_name,
                    b.name AS brand_name
                FROM products p
                LEFT JOIN categories c
                    ON p.category_id = c.id
                LEFT JOIN brands b
                    ON p.brand_id = b.id
                WHERE p.status = 'active'
                AND (
                    p.name LIKE :keyword
                    OR p.description LIKE :keyword
                    OR b.name LIKE :keyword
                )
                ORDER BY p.created_at DESC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'keyword' => '%' . $keyword . '%'
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Lấy sản phẩm theo danh mục
     */
    public function getByCategory(int $categoryId): array
    {
        $sql = "SELECT
                    p.*,
                    c.name AS category_name,
                    b.name AS brand_name
                FROM products p
                LEFT JOIN categories c
                    ON p.category_id = c.id
                LEFT JOIN brands b
                    ON p.brand_id = b.id
                WHERE p.category_id = :category_id
                AND p.status = 'active'
                ORDER BY p.created_at DESC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'category_id' => $categoryId
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Lấy sản phẩm nổi bật / mới nhất
     */
    public function getLatest(int $limit = 8): array
    {
        $limit = max(1, min($limit, 50));

        $sql = "SELECT
                    p.*,
                    c.name AS category_name,
                    b.name AS brand_name
                FROM products p
                LEFT JOIN categories c
                    ON p.category_id = c.id
                LEFT JOIN brands b
                    ON p.brand_id = b.id
                WHERE p.status = 'active'
                ORDER BY p.created_at DESC
                LIMIT $limit";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    /**
     * Đếm tổng số sản phẩm
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*)
                FROM products
                WHERE status = 'active'";

        return (int) $this->pdo
            ->query($sql)
            ->fetchColumn();
    }

    /**
     * Lấy sản phẩm có tồn kho
     */
    public function isInStock(int $id, int $quantity = 1): bool
    {
        $sql = "SELECT stock
                FROM products
                WHERE id = :id
                AND status = 'active'
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $stock = $stmt->fetchColumn();

        return $stock !== false && (int) $stock >= $quantity;
    }
    public function getPrimaryImage(int $productId): ?string
{
    $sql = "SELECT image_url
            FROM product_images
            WHERE product_id = :product_id
            AND is_primary = 1
            LIMIT 1";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        'product_id' => $productId
    ]);

    $image = $stmt->fetchColumn();

    return $image ?: null;
}
/**
 * Lấy tất cả hình ảnh của sản phẩm
 */
public function getImages(int $productId): array
{
    $sql = "SELECT id, image_url, is_primary
            FROM product_images
            WHERE product_id = :product_id
            ORDER BY is_primary DESC, id ASC";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        'product_id' => $productId
    ]);

    return $stmt->fetchAll();
}
} 
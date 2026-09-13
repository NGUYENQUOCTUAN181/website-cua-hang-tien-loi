<?php

class AdminProduct
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy danh sách sản phẩm
     */
    public function getAll(
        string $keyword = '',
        int $categoryId = 0
    ): array {
        $sql = "SELECT
                    p.*,
                    c.name AS category_name,
                    b.name AS brand_name
                FROM products p
                LEFT JOIN categories c
                    ON p.category_id = c.id
                LEFT JOIN brands b
                    ON p.brand_id = b.id
                WHERE 1 = 1";

        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (
                        p.name LIKE :keyword
                        OR p.slug LIKE :keyword
                        OR b.name LIKE :keyword
                      )";

            $params['keyword'] = '%' . $keyword . '%';
        }

        if ($categoryId > 0) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        $sql .= " ORDER BY p.id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Lấy sản phẩm theo ID
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT *
                FROM products
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $product = $stmt->fetch();

        return $product ?: null;
    }

    /**
     * Thêm sản phẩm
     */
    public function create(
        int $categoryId,
        ?int $brandId,
        string $name,
        string $slug,
        string $description,
        float $price,
        ?float $salePrice,
        int $stock,
        string $status
    ): bool {
        $sql = "INSERT INTO products (
                    category_id,
                    brand_id,
                    name,
                    slug,
                    description,
                    price,
                    sale_price,
                    stock,
                    status,
                    created_at,
                    updated_at
                ) VALUES (
                    :category_id,
                    :brand_id,
                    :name,
                    :slug,
                    :description,
                    :price,
                    :sale_price,
                    :stock,
                    :status,
                    NOW(),
                    NOW()
                )";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'price' => $price,
            'sale_price' => $salePrice,
            'stock' => $stock,
            'status' => $status
        ]);
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update(
        int $id,
        int $categoryId,
        ?int $brandId,
        string $name,
        string $slug,
        string $description,
        float $price,
        ?float $salePrice,
        int $stock,
        string $status
    ): bool {
        $sql = "UPDATE products
                SET
                    category_id = :category_id,
                    brand_id = :brand_id,
                    name = :name,
                    slug = :slug,
                    description = :description,
                    price = :price,
                    sale_price = :sale_price,
                    stock = :stock,
                    status = :status,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'price' => $price,
            'sale_price' => $salePrice,
            'stock' => $stock,
            'status' => $status
        ]);
    }

    /**
     * Xóa mềm sản phẩm
     *
     * Không DELETE vật lý để tránh làm hỏng
     * dữ liệu đơn hàng cũ.
     */
    public function delete(int $id): bool
    {
        $sql = "UPDATE products
                SET
                    status = 'inactive',
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id
        ]);
    }
}
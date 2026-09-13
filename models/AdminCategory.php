<?php

class AdminCategory
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(
        string $keyword = '',
        string $status = ''
    ): array {
        $sql = "SELECT
                    c.*,
                    p.name AS parent_name
                FROM categories c
                LEFT JOIN categories p
                    ON c.parent_id = p.id
                WHERE 1 = 1";

        $params = [];

        if ($keyword !== '') {
            $sql .= " AND c.name LIKE :keyword";
            $params['keyword'] = '%' . $keyword . '%';
        }

        if (
            $status !== '' &&
            in_array($status, ['active', 'inactive'], true)
        ) {
            $sql .= " AND c.status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY
                    c.parent_id IS NOT NULL ASC,
                    c.name ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT *
                FROM categories
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $category = $stmt->fetch();

        return $category ?: null;
    }

    public function create(
        ?int $parentId,
        string $name,
        string $description,
        string $status
    ): bool {
        $sql = "INSERT INTO categories (
                    parent_id,
                    name,
                    description,
                    status,
                    created_at,
                    updated_at
                ) VALUES (
                    :parent_id,
                    :name,
                    :description,
                    :status,
                    NOW(),
                    NOW()
                )";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'parent_id' => $parentId,
            'name' => $name,
            'description' => $description,
            'status' => $status
        ]);
    }

    public function update(
        int $id,
        ?int $parentId,
        string $name,
        string $description,
        string $status
    ): bool {
        $sql = "UPDATE categories
                SET
                    parent_id = :parent_id,
                    name = :name,
                    description = :description,
                    status = :status,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'parent_id' => $parentId,
            'name' => $name,
            'description' => $description,
            'status' => $status
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "UPDATE categories
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
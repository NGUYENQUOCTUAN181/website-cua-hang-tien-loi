<?php

class AdminBrand
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

        $sql = "SELECT *
                FROM brands
                WHERE 1 = 1";

        $params = [];

        if ($keyword !== '') {

            $sql .= " AND (
                        name LIKE :keyword
                        OR description LIKE :keyword
                      )";

            $params['keyword'] =
                '%' . $keyword . '%';
        }

        if (
            $status !== '' &&
            in_array(
                $status,
                ['active', 'inactive'],
                true
            )
        ) {

            $sql .= " AND status = :status";

            $params['status'] = $status;
        }

        $sql .= " ORDER BY name ASC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT *
                FROM brands
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $brand = $stmt->fetch();

        return $brand ?: null;
    }

    public function create(
        string $name,
        string $description,
        string $status
    ): bool {

        $sql = "INSERT INTO brands (
                    name,
                    description,
                    status,
                    created_at
                ) VALUES (
                    :name,
                    :description,
                    :status,
                    NOW()
                )";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'name' => $name,
            'description' => $description,
            'status' => $status
        ]);
    }

    public function update(
        int $id,
        string $name,
        string $description,
        string $status
    ): bool {

        $sql = "UPDATE brands
                SET
                    name = :name,
                    description = :description,
                    status = :status
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'status' => $status
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "UPDATE brands
                SET status = 'inactive'
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id
        ]);
    }
}
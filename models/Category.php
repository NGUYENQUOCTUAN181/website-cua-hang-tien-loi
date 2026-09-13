<?php

class Category
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(): array
    {
        $sql = "SELECT *
                FROM categories
                ORDER BY name ASC";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function getParentCategories(): array
    {
        $sql = "SELECT *
                FROM categories
                WHERE parent_id IS NULL
                ORDER BY name ASC";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function getChildren(int $parentId): array
    {
        $sql = "SELECT *
                FROM categories
                WHERE parent_id = :parent_id
                ORDER BY name ASC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'parent_id' => $parentId
        ]);

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
}
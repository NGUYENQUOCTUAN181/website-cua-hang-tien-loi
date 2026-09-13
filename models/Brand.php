<?php

class Brand
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(): array
    {
        $sql = "SELECT *
                FROM brands
                ORDER BY name ASC";

        return $this->pdo->query($sql)->fetchAll();
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
}
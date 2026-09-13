<?php

class AdminVoucher
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
                FROM vouchers
                WHERE 1 = 1";

        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (
                        code LIKE :keyword
                        OR description LIKE :keyword
                      )";

            $params['keyword'] = '%' . $keyword . '%';
        }

        if (
            $status !== '' &&
            in_array($status, ['active', 'inactive'], true)
        ) {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT *
                FROM vouchers
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $id
        ]);

        $voucher = $stmt->fetch();

        return $voucher ?: null;
    }

    public function create(
        string $code,
        string $description,
        string $discountType,
        float $discountValue,
        float $minOrderValue,
        ?float $maxDiscount,
        int $quantity,
        string $startDate,
        string $endDate,
        string $status
    ): bool {
        $sql = "INSERT INTO vouchers (
                    code,
                    description,
                    discount_type,
                    discount_value,
                    min_order_value,
                    max_discount,
                    quantity,
                    used_quantity,
                    start_date,
                    end_date,
                    status,
                    created_at
                ) VALUES (
                    :code,
                    :description,
                    :discount_type,
                    :discount_value,
                    :min_order_value,
                    :max_discount,
                    :quantity,
                    0,
                    :start_date,
                    :end_date,
                    :status,
                    NOW()
                )";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'code' => strtoupper($code),
            'description' => $description,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'min_order_value' => $minOrderValue,
            'max_discount' => $maxDiscount,
            'quantity' => $quantity,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status
        ]);
    }

    public function update(
        int $id,
        string $code,
        string $description,
        string $discountType,
        float $discountValue,
        float $minOrderValue,
        ?float $maxDiscount,
        int $quantity,
        string $startDate,
        string $endDate,
        string $status
    ): bool {
        $sql = "UPDATE vouchers
                SET
                    code = :code,
                    description = :description,
                    discount_type = :discount_type,
                    discount_value = :discount_value,
                    min_order_value = :min_order_value,
                    max_discount = :max_discount,
                    quantity = :quantity,
                    start_date = :start_date,
                    end_date = :end_date,
                    status = :status
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'code' => strtoupper($code),
            'description' => $description,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'min_order_value' => $minOrderValue,
            'max_discount' => $maxDiscount,
            'quantity' => $quantity,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "UPDATE vouchers
                SET status = 'inactive'
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id
        ]);
    }
}
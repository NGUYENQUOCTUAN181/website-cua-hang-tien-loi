<?php

class Voucher
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Tìm voucher hợp lệ để hiển thị/kiểm tra
     */
    public function findValid(string $code): ?array
    {
        $sql = "SELECT *
                FROM vouchers
                WHERE code = :code
                AND status = 'active'
                AND start_date <= NOW()
                AND end_date >= NOW()
                AND used_quantity < quantity
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'code' => strtoupper(trim($code))
        ]);

        $voucher = $stmt->fetch();

        return $voucher ?: null;
    }

    /**
     * Tìm và khóa voucher trong transaction
     */
    public function findValidForUpdate(
        string $code
    ): ?array {
        $sql = "SELECT *
                FROM vouchers
                WHERE code = :code
                AND status = 'active'
                AND start_date <= NOW()
                AND end_date >= NOW()
                AND used_quantity < quantity
                LIMIT 1
                FOR UPDATE";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'code' => strtoupper(trim($code))
        ]);

        $voucher = $stmt->fetch();

        return $voucher ?: null;
    }

    /**
     * Tính số tiền giảm
     */
    public function calculateDiscount(
        array $voucher,
        float $orderValue
    ): float {
        $minOrderValue =
            (float) $voucher['min_order_value'];

        if ($orderValue < $minOrderValue) {
            throw new RuntimeException(
                'Đơn hàng chưa đạt giá trị tối thiểu để sử dụng voucher.'
            );
        }

        if (
            $voucher['discount_type'] === 'percent'
        ) {
            $discount =
                $orderValue *
                ((float) $voucher['discount_value'] / 100);
        } else {
            $discount =
                (float) $voucher['discount_value'];
        }

        if (
            $voucher['max_discount'] !== null
            &&
            $voucher['max_discount'] !== ''
        ) {
            $discount = min(
                $discount,
                (float) $voucher['max_discount']
            );
        }

        return min(
            $discount,
            $orderValue
        );
    }
}
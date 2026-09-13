<?php

class Review
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /*
    |--------------------------------------------------------------------------
    | KIỂM TRA USER ĐÃ MUA SẢN PHẨM
    |--------------------------------------------------------------------------
    */

    public function hasPurchased(
        int $userId,
        int $productId
    ): bool {

        $sql = "SELECT oi.id
                FROM order_items oi

                INNER JOIN orders o
                    ON o.id = oi.order_id

                WHERE o.user_id = :user_id
                AND oi.product_id = :product_id
                AND o.status = 'completed'

                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId
        ]);

        return (bool) $stmt->fetchColumn();
    }

    /*
    |--------------------------------------------------------------------------
    | KIỂM TRA USER ĐÃ REVIEW
    |--------------------------------------------------------------------------
    */

    public function findByUserProduct(
        int $userId,
        int $productId
    ): ?array {

        $sql = "SELECT *
                FROM reviews
                WHERE user_id = :user_id
                AND product_id = :product_id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId
        ]);

        $review = $stmt->fetch();

        return $review ?: null;
    }

    /*
    |--------------------------------------------------------------------------
    | TẠO REVIEW
    |--------------------------------------------------------------------------
    */

    public function create(
        int $userId,
        int $productId,
        int $rating,
        string $comment
    ): bool {

        if ($rating < 1 || $rating > 5) {
            throw new InvalidArgumentException(
                'Rating phải từ 1 đến 5 sao.'
            );
        }

        $comment = trim($comment);

        if ($comment === '') {
            throw new InvalidArgumentException(
                'Vui lòng nhập nội dung đánh giá.'
            );
        }

        $sql = "INSERT INTO reviews (
                    product_id,
                    user_id,
                    rating,
                    comment,
                    status,
                    created_at,
                    updated_at
                ) VALUES (
                    :product_id,
                    :user_id,
                    :rating,
                    :comment,
                    'pending',
                    NOW(),
                    NOW()
                )";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'product_id' => $productId,
            'user_id' => $userId,
            'rating' => $rating,
            'comment' => $comment
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CẬP NHẬT REVIEW CỦA CHÍNH USER
    |--------------------------------------------------------------------------
    */

    public function updateByUser(
        int $reviewId,
        int $userId,
        int $rating,
        string $comment
    ): bool {

        if ($rating < 1 || $rating > 5) {
            throw new InvalidArgumentException(
                'Rating phải từ 1 đến 5 sao.'
            );
        }

        $comment = trim($comment);

        if ($comment === '') {
            throw new InvalidArgumentException(
                'Vui lòng nhập nội dung đánh giá.'
            );
        }

        $sql = "UPDATE reviews
                SET
                    rating = :rating,
                    comment = :comment,
                    status = 'pending',
                    updated_at = NOW()
                WHERE id = :id
                AND user_id = :user_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $reviewId,
            'user_id' => $userId,
            'rating' => $rating,
            'comment' => $comment
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | REVIEW ĐÃ DUYỆT
    |--------------------------------------------------------------------------
    */

    public function getApprovedByProduct(
        int $productId
    ): array {

        $sql = "SELECT
                    r.id,
                    r.product_id,
                    r.user_id,
                    r.rating,
                    r.comment,
                    r.created_at,
                    u.name AS user_name

                FROM reviews r

                INNER JOIN users u
                    ON u.id = r.user_id

                WHERE r.product_id = :product_id
                AND r.status = 'approved'

                ORDER BY r.created_at DESC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'product_id' => $productId
        ]);

        return $stmt->fetchAll();
    }

    /*
    |--------------------------------------------------------------------------
    | ĐIỂM TRUNG BÌNH
    |--------------------------------------------------------------------------
    */

    public function getAverageRating(
        int $productId
    ): float {

        $sql = "SELECT
                    COALESCE(
                        AVG(rating),
                        0
                    )

                FROM reviews

                WHERE product_id = :product_id
                AND status = 'approved'";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'product_id' => $productId
        ]);

        return round(
            (float) $stmt->fetchColumn(),
            1
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TỔNG REVIEW
    |--------------------------------------------------------------------------
    */

    public function getReviewCount(
        int $productId
    ): int {

        $sql = "SELECT COUNT(*)
                FROM reviews

                WHERE product_id = :product_id
                AND status = 'approved'";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'product_id' => $productId
        ]);

        return (int) $stmt->fetchColumn();
    }
}
<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/ReviewController.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user']['id'];

$action = $_POST['action'] ?? '';

$productId = (int) ($_POST['product_id'] ?? 0);

if ($productId <= 0) {
    die('Sản phẩm không hợp lệ.');
}

if ($action === '') {
    header(
        'Location: product_detail.php?id=' .
        $productId
    );
    exit;
}

$reviewController =
    new ReviewController($pdo);

try {

    /*
    |--------------------------------------------------------------------------
    | KIỂM TRA ĐÃ MUA
    |--------------------------------------------------------------------------
    */

    if (
        !$reviewController->hasPurchased(
            $userId,
            $productId
        )
    ) {

        throw new RuntimeException(
            'Bạn cần mua và nhận sản phẩm thành công trước khi đánh giá.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    if ($action === 'create') {

        $rating =
            (int) ($_POST['rating'] ?? 0);

        $comment =
            trim($_POST['comment'] ?? '');

        /*
         * Không cho review trùng sản phẩm
         */
        $existing =
            $reviewController->getExistingReview(
                $userId,
                $productId
            );

        if ($existing) {

            throw new RuntimeException(
                'Bạn đã đánh giá sản phẩm này.'
            );
        }

        $reviewController->create(
            $userId,
            $productId,
            $rating,
            $comment
        );

        header(
            'Location: product_detail.php?id=' .
            $productId .
            '&review=success'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    if ($action === 'update') {

        $reviewId =
            (int) ($_POST['review_id'] ?? 0);

        $rating =
            (int) ($_POST['rating'] ?? 0);

        $comment =
            trim($_POST['comment'] ?? '');

        if ($reviewId <= 0) {
            throw new RuntimeException(
                'Review không hợp lệ.'
            );
        }

        $reviewController->update(
            $reviewId,
            $userId,
            $rating,
            $comment
        );

        header(
            'Location: product_detail.php?id=' .
            $productId .
            '&review=updated'
        );

        exit;
    }


    throw new RuntimeException(
        'Thao tác review không hợp lệ.'
    );

} catch (Throwable $e) {

    header(
        'Location: product_detail.php?id=' .
        $productId .
        '&review_error=' .
        urlencode($e->getMessage())
    );

    exit;
}
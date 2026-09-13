<?php

require_once __DIR__ . '/../models/Product.php';

class ProductController
{
    private Product $productModel;

    public function __construct(PDO $pdo)
    {
        $this->productModel = new Product($pdo);
    }

    public function index(): array
    {
        $keyword = trim($_GET['keyword'] ?? '');
        $categoryId = (int) ($_GET['category_id'] ?? 0);

        if ($keyword !== '') {
            $products = $this->productModel->search($keyword);
        } elseif ($categoryId > 0) {
            $products = $this->productModel->getByCategory($categoryId);
        } else {
            $products = $this->productModel->getAll();
        }

        return [
            'products' => $products,
            'keyword' => $keyword,
            'category_id' => $categoryId
        ];
    }

    public function getPrimaryImage(int $productId): ?string
    {
        return $this->productModel->getPrimaryImage($productId);
    }

    public function detail(int $productId): ?array
    {
        $product = $this->productModel->findById($productId);

        if (!$product) {
            return null;
        }

        $product['images'] = $this->productModel->getImages($productId);

        return $product;
    }
}
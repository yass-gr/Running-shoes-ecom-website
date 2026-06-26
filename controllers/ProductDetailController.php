<?php

class ProductDetailController
{
    public function index(): void
    {
        $id = (int) ($_GET["id"] ?? 0);
        if ($id <= 0) {
            http_response_code(404);
            echo "Product not found";
            return;
        }

        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../models/product.php";
        require_once __DIR__ . "/../models/product_variant.php";
        require_once __DIR__ . "/../models/product_img.php";

        $product = (new Product($pdo))->findById($id);
        if (!$product) {
            http_response_code(404);
            echo "Product not found";
            return;
        }

        $variants = (new ProductVariant($pdo))->findByProduct($id);
        $images = (new ProductImg($pdo))->findByVariantProduct($id);

        $salePct = (float) ($product["sale"] ?? 0);
        $salePrice = $salePct > 0 ? $product["base_price"] * (1 - $salePct / 100) : null;

        require __DIR__ . "/../views/product-detail.php";
    }
}

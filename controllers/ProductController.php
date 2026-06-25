<?php

class ProductController
{
    public function index(): void
    {
        $route = $_GET["route"] ?? "shop-all";

        switch ($route) {
            case "product":
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
                require __DIR__ . "/../views/product-detail.php";
                break;

            case "shop-all":
                require __DIR__ . "/../views/shop-all.php";
                break;

            case "mens":
                require __DIR__ . "/../views/mens.php";
                break;

            case "womens":
                require __DIR__ . "/../views/womens.php";
                break;

            case "sale":
                require __DIR__ . "/../views/sale.php";
                break;

            case "new-arrivals":
                require __DIR__ . "/../views/new-arrivals.php";
                break;

            default:
                http_response_code(404);
                require __DIR__ . "/../views/404.php";
                break;
        }
    }
}

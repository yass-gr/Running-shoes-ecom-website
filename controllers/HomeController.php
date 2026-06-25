<?php

class HomeController
{
    public function index(): void
    {
        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../models/product.php";
        require_once __DIR__ . "/../models/product_variant.php";

        $productModel = new Product($pdo);
        $variantModel = new ProductVariant($pdo);

        $dbProducts = $productModel->getTopSellers(30);

        $carousel1 = [];
        $carousel2 = [];
        foreach ($dbProducts as $i => $p) {
            $variants = $variantModel->findByProduct($p["id"]);
            $first = $variants[0] ?? [];

            $item = [
                "id" => $p["id"],
                "name" => $p["name"],
                "price" => $p["base_price"],
                "image" => $first["thumbnail"] ?? "",
                "color" => $first["color"] ?? "",
                "colorcode" => "",
            ];

            if ($i < 20) {
                $carousel1[] = $item;
            } else {
                $carousel2[] = $item;
            }
        }

        require __DIR__ . "/../views/home.php";
    }
}

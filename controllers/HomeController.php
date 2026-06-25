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

        $all = $productModel->findAll();
        usort($all, fn($a, $b) => strtotime($b["created_at"]) - strtotime($a["created_at"]));

        $newArrivals = [];
        foreach (array_slice($all, 0, 30) as $p) {
            $variants = $variantModel->findByProduct($p["id"]);
            $first = $variants[0] ?? [];
            $img = $first["thumbnail"] ?? "";

            $newArrivals[] = [
                "id" => $p["id"],
                "name" => $p["name"],
                "price" => $p["base_price"],
                "image" => $img,
                "color" => $first["color"] ?? "",
            ];
        }

        require __DIR__ . "/../views/home.php";
    }
}

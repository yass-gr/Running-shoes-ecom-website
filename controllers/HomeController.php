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
        $newest = array_slice($all, 0, 30);

        $newArrivals = [];
        foreach ($newest as $p) {
            $variants = $variantModel->findByProduct($p["id"]);
            $first = $variants[0] ?? [];

            $newArrivals[] = [
                "id" => $p["id"],
                "name" => $p["name"],
                "fullName" => $p["name"],
                "masterName" => $p["name"],
                "price" => $p["base_price"],
                "image" => $first["thumbnail"] ?? "",
                "color" => $first["color"] ?? "",
                "colorName" => $first["color"] ?? "",
                "colorcode" => "",
            ];
        }

        require __DIR__ . "/../views/home.php";
    }
}

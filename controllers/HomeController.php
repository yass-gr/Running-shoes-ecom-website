<?php

class HomeController
{
    public function index(): void
    {
        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../models/product.php";
        require_once __DIR__ . "/../models/category.php";
        require_once __DIR__ . "/../models/product_img.php";

        $productModel = new Product($pdo);
        $categoryModel = new Category($pdo);
        $imgModel = new ProductImg($pdo);

        $newArrivals = $productModel->getTopSellers(8);
        $categories = $categoryModel->findAll();

        $newArrivalsImgs = [];
        foreach ($newArrivals as $p) {
            $img = $imgModel->findByVariantProduct($p["id"]);
            $newArrivalsImgs[$p["id"]] = $img[0]["url"] ?? null;
        }

        require __DIR__ . "/../views/home.php";
    }
}

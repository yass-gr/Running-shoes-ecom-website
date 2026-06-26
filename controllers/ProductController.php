<?php

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/product.php";
require_once __DIR__ . "/../models/product_variant.php";
require_once __DIR__ . "/../models/product_img.php";

class ProductController
{
    private Product $productModel;
    private ProductVariant $variantModel;
    private ProductImg $imgModel;

    public function __construct()
    {
        $this->productModel = new Product($pdo);
        $this->variantModel = new ProductVariant($pdo);
        $this->imgModel = new ProductImg($pdo);
    }

    public function shopAll(): void
    {
        $products = $this->productModel->findAll();
        $perPage = 24;
        $totalPages = (int) ceil(count($products) / $perPage);
        $currentPage = max(1, min((int) ($_GET["page"] ?? 1), $totalPages));
        $pageProducts = array_slice($products, ($currentPage - 1) * $perPage, $perPage);
        $productCount = count($products);

        require __DIR__ . "/../views/shop-all.php";
    }

    public function search(): void
    {
        $query = trim($_GET["q"] ?? "");

        if ($query !== "") {
            $products = $this->productModel->search($query);
        } else {
            $products = $this->productModel->findAll();
            shuffle($products);
            $products = array_slice($products, 0, 10);
        }

        $perPage = 24;
        $totalPages = (int) ceil(count($products) / $perPage);
        $currentPage = max(1, min((int) ($_GET["page"] ?? 1), $totalPages));
        $pageProducts = array_slice($products, ($currentPage - 1) * $perPage, $perPage);
        $productCount = count($products);

        require __DIR__ . "/../views/search.php";
    }

    public function mens(): void
    {
        $products = $this->productModel->findByGender("Men");
        $perPage = 24;
        $totalPages = (int) ceil(count($products) / $perPage);
        $currentPage = max(1, min((int) ($_GET["page"] ?? 1), $totalPages));
        $pageProducts = array_slice($products, ($currentPage - 1) * $perPage, $perPage);
        $productCount = count($products);

        require __DIR__ . "/../views/mens.php";
    }

    public function womens(): void
    {
        $products = $this->productModel->findByGender("Women");
        $perPage = 24;
        $totalPages = (int) ceil(count($products) / $perPage);
        $currentPage = max(1, min((int) ($_GET["page"] ?? 1), $totalPages));
        $pageProducts = array_slice($products, ($currentPage - 1) * $perPage, $perPage);
        $productCount = count($products);

        require __DIR__ . "/../views/womens.php";
    }

    public function newArrivals(): void
    {
        $products = $this->productModel->findAll();
        $perPage = 24;
        $totalPages = (int) ceil(count($products) / $perPage);
        $currentPage = max(1, min((int) ($_GET["page"] ?? 1), $totalPages));
        $pageProducts = array_slice($products, ($currentPage - 1) * $perPage, $perPage);
        $productCount = count($products);

        require __DIR__ . "/../views/new-arrivals.php";
    }

    public function sale(): void
    {
        $products = $this->productModel->findAll();
        $perPage = 24;
        $totalPages = (int) ceil(count($products) / $perPage);
        $currentPage = max(1, min((int) ($_GET["page"] ?? 1), $totalPages));
        $pageProducts = array_slice($products, ($currentPage - 1) * $perPage, $perPage);
        $productCount = count($products);

        require __DIR__ . "/../views/sale.php";
    }
}

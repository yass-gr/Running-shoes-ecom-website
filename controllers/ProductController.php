<?php

class ProductController
{
    private function getProductModel(): Product
    {
        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../models/product.php";
        return new Product($pdo);
    }

    public function shopAll(): void
    {
        $productModel = $this->getProductModel();
        $products = $productModel->findAll();
        $perPage = 24;
        $totalPages = (int) ceil(count($products) / $perPage);
        $currentPage = max(1, min((int) ($_GET["page"] ?? 1), $totalPages));
        $pageProducts = array_slice($products, ($currentPage - 1) * $perPage, $perPage);
        $productCount = count($products);

        require __DIR__ . "/../views/shop-all.php";
    }

    public function search(): void
    {
        $productModel = $this->getProductModel();
        $query = trim($_GET["q"] ?? "");

        if ($query !== "") {
            $products = $productModel->search($query);
        } else {
            $products = $productModel->findAll();
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
        $productModel = $this->getProductModel();
        $products = $productModel->findByGender("Men");
        $perPage = 24;
        $totalPages = (int) ceil(count($products) / $perPage);
        $currentPage = max(1, min((int) ($_GET["page"] ?? 1), $totalPages));
        $pageProducts = array_slice($products, ($currentPage - 1) * $perPage, $perPage);
        $productCount = count($products);

        require __DIR__ . "/../views/mens.php";
    }

    public function womens(): void
    {
        $productModel = $this->getProductModel();
        $products = $productModel->findByGender("Women");
        $perPage = 24;
        $totalPages = (int) ceil(count($products) / $perPage);
        $currentPage = max(1, min((int) ($_GET["page"] ?? 1), $totalPages));
        $pageProducts = array_slice($products, ($currentPage - 1) * $perPage, $perPage);
        $productCount = count($products);

        require __DIR__ . "/../views/womens.php";
    }

    public function newArrivals(): void
    {
        $productModel = $this->getProductModel();
        $products = $productModel->findAll();
        $perPage = 24;
        $totalPages = (int) ceil(count($products) / $perPage);
        $currentPage = max(1, min((int) ($_GET["page"] ?? 1), $totalPages));
        $pageProducts = array_slice($products, ($currentPage - 1) * $perPage, $perPage);
        $productCount = count($products);

        require __DIR__ . "/../views/new-arrivals.php";
    }

    public function sale(): void
    {
        $productModel = $this->getProductModel();
        $products = $productModel->findAll();
        $perPage = 24;
        $totalPages = (int) ceil(count($products) / $perPage);
        $currentPage = max(1, min((int) ($_GET["page"] ?? 1), $totalPages));
        $pageProducts = array_slice($products, ($currentPage - 1) * $perPage, $perPage);
        $productCount = count($products);

        require __DIR__ . "/../views/sale.php";
    }
}

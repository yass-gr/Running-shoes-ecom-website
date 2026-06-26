<?php

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/product.php";
require_once __DIR__ . "/../utils/helpers.php";

class ProductController
{
    private function model(): Product
    {
        global $pdo;
        return new Product($pdo);
    }

    public function shopAll(): void
    {
        $products = $this->model()->findAll();
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
            $products = $this->model()->search($query);
        } else {
            $products = $this->model()->findAll();
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
        $products = $this->model()->findByGender("Men");
        $perPage = 24;
        $totalPages = (int) ceil(count($products) / $perPage);
        $currentPage = max(1, min((int) ($_GET["page"] ?? 1), $totalPages));
        $pageProducts = array_slice($products, ($currentPage - 1) * $perPage, $perPage);
        $productCount = count($products);

        require __DIR__ . "/../views/mens.php";
    }

    public function womens(): void
    {
        $products = $this->model()->findByGender("Women");
        $perPage = 24;
        $totalPages = (int) ceil(count($products) / $perPage);
        $currentPage = max(1, min((int) ($_GET["page"] ?? 1), $totalPages));
        $pageProducts = array_slice($products, ($currentPage - 1) * $perPage, $perPage);
        $productCount = count($products);

        require __DIR__ . "/../views/womens.php";
    }

    public function newArrivals(): void
    {
        $products = $this->model()->findAll();
        $perPage = 24;
        $totalPages = (int) ceil(count($products) / $perPage);
        $currentPage = max(1, min((int) ($_GET["page"] ?? 1), $totalPages));
        $pageProducts = array_slice($products, ($currentPage - 1) * $perPage, $perPage);
        $productCount = count($products);

        require __DIR__ . "/../views/new-arrivals.php";
    }

    public function sale(): void
    {
        $products = $this->model()->findAll();
        $perPage = 24;
        $totalPages = (int) ceil(count($products) / $perPage);
        $currentPage = max(1, min((int) ($_GET["page"] ?? 1), $totalPages));
        $pageProducts = array_slice($products, ($currentPage - 1) * $perPage, $perPage);
        $productCount = count($products);

        require __DIR__ . "/../views/sale.php";
    }
}

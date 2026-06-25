<?php

class AdminController
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (($_SESSION["user_role"] ?? "") !== "admin") {
            http_response_code(403);
            require __DIR__ . "/../views/admin.php";
            return;
        }

        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../models/product.php";
        require_once __DIR__ . "/../models/order.php";
        require_once __DIR__ . "/../models/user.php";
        require_once __DIR__ . "/../models/product_variant.php";

        $productModel = new Product($pdo);
        $orderModel = new Order($pdo);
        $userModel = new User($pdo);
        $variantModel = new ProductVariant($pdo);

        $allProducts = $productModel->findAll();
        $allOrders = $orderModel->findByStatus("pending");

        $stats = [
            "total_products" => count($allProducts),
            "total_orders" => count($orderModel->findByStatus("pending")) + count($orderModel->findByStatus("shipped")) + count($orderModel->findByStatus("delivered")),
            "total_users" => count($userModel->findAll()),
            "pending_orders" => count($orderModel->findByStatus("pending")),
        ];

        $allUsers = $userModel->findAll();

        // Calculate total orders across all statuses
        $totalOrders = 0;
        foreach (["pending", "shipped", "delivered", "cancelled"] as $status) {
            $totalOrders += count($orderModel->findByStatus($status));
        }
        $stats["total_orders"] = $totalOrders;

        $recentOrders = $orderModel->findByStatus("pending");
        usort($recentOrders, fn($a, $b) => strtotime($b["created_at"]) - strtotime($a["created_at"]));
        $recentOrders = array_slice($recentOrders, 0, 10);

        $lowStock = $variantModel->findLowStock();

        require __DIR__ . "/../views/admin.php";
    }
}

<?php

class DeliveryController
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (($_SESSION["user_role"] ?? "") !== "delivery_guy") {
            http_response_code(403);
            echo "Access denied. Delivery personnel only.";
            return;
        }

        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../models/order.php";

        $orderModel = new Order($pdo);
        $deliveryGuyId = (int) $_SESSION["user_id"];

        $assignedOrders = $orderModel->findByDeliveryGuy($deliveryGuyId);

        require __DIR__ . "/../views/delivery.php";
    }

    public function markDelivered(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (($_SESSION["user_role"] ?? "") !== "delivery_guy") {
            http_response_code(403);
            return;
        }

        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../models/order.php";

        $orderId = (int) ($_POST["order_id"] ?? 0);
        if ($orderId > 0) {
            $orderModel = new Order($pdo);
            $orderModel->markDelivered($orderId);
        }

        header("Location: ?route=delivery");
        exit;
    }
}

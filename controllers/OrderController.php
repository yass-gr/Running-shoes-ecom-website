<?php

class OrderController
{
    public function index(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->placeOrder();
        } else {
            $this->showCheckout();
        }
    }

    private function showCheckout(): void
    {
        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../models/city.php";
        require_once __DIR__ . "/../models/product.php";
        require_once __DIR__ . "/../models/product_variant.php";

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $cart = $_SESSION["cart"] ?? [];
        $productModel = new Product($pdo);
        $variantModel = new ProductVariant($pdo);

        $cartItems = [];
        foreach ($cart as $productId => $data) {
            $product = $productModel->findById((int) $productId);
            if (!$product) continue;

            $variants = $variantModel->findByProduct((int) $productId);
            $first = $variants[0] ?? [];

            $cartItems[] = [
                "product_id" => $productId,
                "name" => $product["name"],
                "price" => $product["base_price"],
                "image" => $first["thumbnail"] ?? "",
                "quantity" => $data["quantity"] ?? 1,
            ];
        }

        $cityModel = new City($pdo);
        $cities = $cityModel->findAll();

        require __DIR__ . "/../views/checkout.php";
    }

    private function placeOrder(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["user_id"])) {
            header("Location: ?route=login");
            exit;
        }

        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../models/order.php";
        require_once __DIR__ . "/../models/product.php";
        require_once __DIR__ . "/../models/product_variant.php";

        $cart = $_SESSION["cart"] ?? [];
        if (empty($cart)) {
            header("Location: ?route=cart");
            exit;
        }

        $userId = (int) $_SESSION["user_id"];
        $cityId = (int) ($_POST["city_id"] ?? 0);
        $orderModel = new Order($pdo);
        $productModel = new Product($pdo);
        $variantModel = new ProductVariant($pdo);

        $subtotal = 0;
        $items = [];
        foreach ($cart as $productId => $data) {
            $product = $productModel->findById((int) $productId);
            if (!$product) continue;

            $qty = (int) ($data["quantity"] ?? 1);
            $price = (float) $product["base_price"];
            $subtotal += $price * $qty;

            $variants = $variantModel->findByProduct((int) $productId);
            $variantId = $variants[0]["id"] ?? 0;

            $items[] = [
                "variant_id" => $variantId,
                "quantity" => $qty,
                "price" => $price,
            ];
        }

        try {
            $pdo->beginTransaction();

            $orderId = $orderModel->create($userId, $cityId, $subtotal);

            foreach ($items as $item) {
                $orderModel->addItem($orderId, $item["variant_id"], $item["quantity"], $item["price"]);
                $productModel->incrementSales((int) $productId, $item["quantity"]);
                $variantModel->decrementStock($item["variant_id"], $item["quantity"]);
            }

            $pdo->commit();

            $_SESSION["cart"] = [];

            header("Location: ?route=account&order_placed=1");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            header("Location: ?route=checkout&error=1");
            exit;
        }
    }
}

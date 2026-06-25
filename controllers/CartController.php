<?php

class CartController
{
    public function index(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $action = $_POST["action"] ?? "";
            if ($action === "add") {
                $this->add();
            } elseif ($action === "remove") {
                $this->remove();
            } elseif ($action === "update") {
                $this->update();
            }
        } else {
            $this->show();
        }
    }

    private function show(): void
    {
        require_once __DIR__ . "/../config/database.php";
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
            $thumb = $first["thumbnail"] ?? "";

            $cartItems[] = [
                "product_id" => $productId,
                "name" => $product["name"],
                "price" => $product["base_price"],
                "image" => $thumb,
                "quantity" => $data["quantity"] ?? 1,
            ];
        }

        require __DIR__ . "/../views/cart.php";
    }

    private function add(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $productId = (int) ($_POST["product_id"] ?? 0);
        if ($productId <= 0) {
            header("Location: ?route=shop-all");
            exit;
        }

        if (!isset($_SESSION["cart"])) {
            $_SESSION["cart"] = [];
        }

        if (isset($_SESSION["cart"][$productId])) {
            $_SESSION["cart"][$productId]["quantity"]++;
        } else {
            $_SESSION["cart"][$productId] = ["quantity" => 1];
        }

        header("Location: ?route=cart");
        exit;
    }

    private function remove(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $productId = (int) ($_POST["product_id"] ?? 0);
        unset($_SESSION["cart"][$productId]);

        header("Location: ?route=cart");
        exit;
    }

    private function update(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $productId = (int) ($_POST["product_id"] ?? 0);
        $quantity = max(1, (int) ($_POST["quantity"] ?? 1));

        if (isset($_SESSION["cart"][$productId])) {
            $_SESSION["cart"][$productId]["quantity"] = $quantity;
        }

        header("Location: ?route=cart");
        exit;
    }
}

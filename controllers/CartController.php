<?php

class CartController
{
    public function index(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->add();
        } else {
            require __DIR__ . "/../views/cart.php";
        }
    }

    private function add(): void
    {
        // TODO: add item to cart
    }
}

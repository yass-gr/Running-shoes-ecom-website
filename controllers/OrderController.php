<?php

class OrderController
{
    public function index(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->placeOrder();
        } else {
            require __DIR__ . "/../views/checkout.php";
        }
    }

    private function placeOrder(): void
    {
        // TODO: place order
    }
}

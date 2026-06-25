<?php

require_once __DIR__ . "/controllers/HomeController.php";
require_once __DIR__ . "/controllers/ProductController.php";
require_once __DIR__ . "/controllers/ShopAllController.php";
require_once __DIR__ . "/controllers/MensController.php";
require_once __DIR__ . "/controllers/WomensController.php";
require_once __DIR__ . "/controllers/SaleController.php";
require_once __DIR__ . "/controllers/NewArrivalsController.php";
require_once __DIR__ . "/controllers/CartController.php";
require_once __DIR__ . "/controllers/OrderController.php";
require_once __DIR__ . "/controllers/AuthController.php";
require_once __DIR__ . "/controllers/AdminController.php";
require_once __DIR__ . "/controllers/DeliveryController.php";

$route = $_GET["route"] ?? "home";

switch ($route) {
    case "home":
        $controller = new HomeController();
        $controller->index();
        break;

    case "shop-all":
        $controller = new ShopAllController();
        $controller->index();
        break;

    case "mens":
        $controller = new MensController();
        $controller->index();
        break;

    case "womens":
        $controller = new WomensController();
        $controller->index();
        break;

    case "sale":
        $controller = new SaleController();
        $controller->index();
        break;

    case "new-arrivals":
        $controller = new NewArrivalsController();
        $controller->index();
        break;

    case "product":
        $controller = new ProductController();
        $controller->index();
        break;

    case "cart":
        $controller = new CartController();
        $controller->index();
        break;

    case "checkout":
        $controller = new OrderController();
        $controller->index();
        break;

    case "login":
    case "register":
    case "logout":
    case "account":
        $controller = new AuthController();
        $controller->index();
        break;

    case "admin":
        $controller = new AdminController();
        $controller->index();
        break;

    case "delivery":
        $controller = new DeliveryController();
        $controller->index();
        break;

    default:
        http_response_code(404);
        require __DIR__ . "/views/404.php";
        break;
}

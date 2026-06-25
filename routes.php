<?php

require_once __DIR__ . "/controllers/HomeController.php";
require_once __DIR__ . "/controllers/ProductController.php";
require_once __DIR__ . "/controllers/CartController.php";
require_once __DIR__ . "/controllers/OrderController.php";
require_once __DIR__ . "/controllers/AuthController.php";
require_once __DIR__ . "/controllers/AdminController.php";

$route = $_GET["route"] ?? "home";

switch ($route) {
    case "home":
        $controller = new HomeController();
        $controller->index();
        break;

    case "shop-all":
    case "mens":
    case "womens":
    case "sale":
    case "new-arrivals":
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

    default:
        http_response_code(404);
        require __DIR__ . "/views/404.php";
        break;
}

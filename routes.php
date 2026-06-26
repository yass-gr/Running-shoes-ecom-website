<?php

require_once __DIR__ . "/utils/helpers.php";

$route = $_GET["route"] ?? "";

switch ($route) {
    case "shop-all":
        require_once __DIR__ . "/controllers/ShopAllController.php";
        (new ShopAllController())->index();
        break;

    case "mens":
        require_once __DIR__ . "/controllers/MensController.php";
        (new MensController())->index();
        break;

    case "womens":
        require_once __DIR__ . "/controllers/WomensController.php";
        (new WomensController())->index();
        break;

    case "new-arrivals":
        require_once __DIR__ . "/controllers/NewArrivalsController.php";
        (new NewArrivalsController())->index();
        break;

    case "sale":
        require_once __DIR__ . "/controllers/SaleController.php";
        (new SaleController())->index();
        break;

    case "home":
        require_once __DIR__ . "/controllers/HomeController.php";
        (new HomeController())->index();
        break;

    case "search":
        require_once __DIR__ . "/controllers/SearchController.php";
        (new SearchController())->index();
        break;

    case "product":
        require_once __DIR__ . "/controllers/ProductDetailController.php";
        (new ProductDetailController())->index();
        break;

    case "cart":
        require_once __DIR__ . "/controllers/CartController.php";
        (new CartController())->index();
        break;

    case "checkout":
        require_once __DIR__ . "/controllers/OrderController.php";
        (new OrderController())->index();
        break;

    case "login":
    case "register":
    case "logout":
    case "account":
        require_once __DIR__ . "/controllers/AuthController.php";
        (new AuthController())->index();
        break;

    default:
        require_once __DIR__ . "/controllers/HomeController.php";
        (new HomeController())->index();
        break;
}

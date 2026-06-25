<?php

class AuthController
{
    public function index(): void
    {
        $route = $_GET["route"] ?? "login";

        switch ($route) {
            case "login":
                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    $this->login();
                } else {
                    require __DIR__ . "/../views/login.php";
                }
                break;

            case "register":
                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    $this->register();
                } else {
                    require __DIR__ . "/../views/register.php";
                }
                break;

            case "logout":
                $this->logout();
                break;

            case "account":
                require __DIR__ . "/../views/account.php";
                break;

            default:
                http_response_code(404);
                require __DIR__ . "/../views/404.php";
                break;
        }
    }

    private function login(): void
    {
        // TODO: handle login
    }

    private function register(): void
    {
        // TODO: handle register
    }

    private function logout(): void
    {
        // TODO: handle logout
        header("Location: /");
        exit;
    }
}

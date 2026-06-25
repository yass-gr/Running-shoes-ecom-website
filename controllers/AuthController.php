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
                $this->account();
                break;

            default:
                http_response_code(404);
                require __DIR__ . "/../views/404.php";
                break;
        }
    }

    private function login(): void
    {
        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../models/user.php";

        $email = $_POST["email"] ?? "";
        $password = $_POST["password"] ?? "";

        $userModel = new User($pdo);
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user["password"])) {
            $error = "Invalid email or password.";
            require __DIR__ . "/../views/login.php";
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION["user_id"] = (int) $user["id"];
        $_SESSION["user_email"] = $user["email"];
        $_SESSION["user_role"] = $user["role"];
        $_SESSION["user_name"] = $user["first_name"] . " " . $user["last_name"];

        header("Location: ?route=account");
        exit;
    }

    private function register(): void
    {
        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../models/user.php";

        $firstName = $_POST["first_name"] ?? "";
        $lastName = $_POST["last_name"] ?? "";
        $email = $_POST["email"] ?? "";
        $password = $_POST["password"] ?? "";

        if ($firstName === "" || $lastName === "" || $email === "" || $password === "") {
            $error = "All fields are required.";
            require __DIR__ . "/../views/register.php";
            return;
        }

        $userModel = new User($pdo);

        $existing = $userModel->findByEmail($email);
        if ($existing) {
            $error = "An account with this email already exists.";
            require __DIR__ . "/../views/register.php";
            return;
        }

        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $userId = $userModel->create($firstName, $lastName, $email, $hashed);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION["user_id"] = $userId;
        $_SESSION["user_email"] = $email;
        $_SESSION["user_role"] = "user";
        $_SESSION["user_name"] = $firstName . " " . $lastName;

        header("Location: ?route=account");
        exit;
    }

    private function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();
        header("Location: /");
        exit;
    }

    private function account(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["user_id"])) {
            header("Location: ?route=login");
            exit;
        }

        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../models/user.php";
        require_once __DIR__ . "/../models/order.php";

        $userId = (int) $_SESSION["user_id"];
        $userModel = new User($pdo);
        $orderModel = new Order($pdo);

        $user = $userModel->findById($userId);
        $orders = $orderModel->findByClient($userId);

        require __DIR__ . "/../views/account.php";
    }
}

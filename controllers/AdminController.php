<?php

class AdminController
{
    public function index(): void
    {
        // TODO: check admin auth
        require __DIR__ . "/../views/admin.php";
    }
}

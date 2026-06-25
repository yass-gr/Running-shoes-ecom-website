<?php

class HomeController
{
    public function index(): void
    {
        require_once __DIR__ . "/../config/database.php";

        $newArrivals = $pdo->query(
            "SELECT p.id, p.name, p.base_price AS price, pv.color,
                    COALESCE(pi.thumbnail, pi.top_view, pi.side_view, pi.pair_view, '') AS image
             FROM Products p
             JOIN Product_variants pv ON pv.product_id = p.id
             LEFT JOIN Product_img pi ON pi.id = pv.product_img_id
             GROUP BY p.id
             ORDER BY p.created_at DESC, p.id DESC
             LIMIT 30"
        )->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . "/../views/home.php";
    }
}

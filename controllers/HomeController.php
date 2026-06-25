<?php

class HomeController
{
    public function index(): void
    {
        require_once __DIR__ . "/../config/database.php";

        $rows = $pdo->query(
            "SELECT p.id, p.name, p.base_price AS price, pv.color,
                    COALESCE(pi.thumbnail, pi.top_view, pi.side_view, pi.pair_view, '') AS image
             FROM Products p
             JOIN Product_variants pv ON pv.product_id = p.id
             LEFT JOIN Product_img pi ON pi.id = pv.product_img_id
             GROUP BY p.id
             ORDER BY p.created_at DESC, p.id DESC
             LIMIT 30"
        )->fetchAll(PDO::FETCH_ASSOC);

        $carousel1Slides = "";
        $carousel2Cards = "";
        $initialName = "";
        $initialPrice = "";

        foreach ($rows as $i => $r) {
            $name = htmlspecialchars($r["name"], ENT_QUOTES);
            $price = htmlspecialchars(number_format((float) $r["price"], 2), ENT_QUOTES);
            $image = htmlspecialchars($r["image"], ENT_QUOTES);
            $color = htmlspecialchars($r["color"], ENT_QUOTES);

            $slide = "<div data-name=\"$name\" data-price=\"$price\"><img src=\"$image\" alt=\"$name\"></div>";
            $card = "<div class=\"card\"><img src=\"$image\" alt=\"$name\"><div class=\"info\"><p class=\"name\">$name</p><p class=\"cName\">$color</p><p class=\"price\">\$$price</p><div class=\"hue\"></div></div><span class=\"badge\">NEW</span></div>";

            if ($i < 20) {
                $carousel1Slides .= $slide;
                if ($i === 0) {
                    $initialName = $name;
                    $initialPrice = $price;
                }
            } else {
                $carousel2Cards .= $card;
            }
        }

        require __DIR__ . "/../views/home.php";
    }
}

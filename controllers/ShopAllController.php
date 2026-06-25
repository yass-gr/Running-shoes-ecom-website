<?php

require_once __DIR__ . "/../utils/helpers.php";

class ShopAllController
{
    public function index(): void
    {
        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../models/product.php";
        require_once __DIR__ . "/../models/product_variant.php";

        $productModel = new Product($pdo);
        $variantModel = new ProductVariant($pdo);

        $all = $productModel->findAll();

        $products = [];
        foreach ($all as $p) {
            $variants = $variantModel->findByProduct($p["id"]);
            $first = $variants[0] ?? [];
            $thumb = $first["thumbnail"] ?? "";
            if ($thumb === "" || $thumb === null) continue;

            $seen = [];
            $swatches = [];
            foreach ($variants as $v) {
                $c = $v["color"] ?? "";
                if ($c === "" || isset($seen[$c])) continue;
                $seen[$c] = true;
                $swatches[] = [
                    "name" => $c,
                    "hex" => colorToHex($c),
                    "thumb" => $v["thumbnail"] ?? "",
                ];
            }

            $products[] = [
                "id"    => $p["id"],
                "name"  => $p["name"],
                "price" => $p["base_price"],
                "brand" => $p["brand_name"],
                "image" => $thumb,
                "color" => $swatches[0]["name"] ?? "",
                "swatches" => $swatches,
            ];
        }

        $perPage = 24;
        $totalProducts = count($products);
        $totalPages = max(1, (int) ceil($totalProducts / $perPage));
        $currentPage = max(1, min((int) ($_GET["page"] ?? 1), $totalPages));
        $pageProducts = array_slice($products, ($currentPage - 1) * $perPage, $perPage);

        $categories = [
            ["title" => "Men's",   "image" => "https://www.allbirds.com/cdn/shop/files/26Q2_CanvasCruiser_Site_Homepage_CategoryRow-01_Desktop-Mobile_2x3_01_Lifestyle.jpg?v=1774909856&width=1024", "route" => "mens", "cta" => "Shop Men's"],
            ["title" => "Women's", "image" => "https://www.allbirds.com/cdn/shop/files/26Q2_CanvasCruiser_Site_Homepage_CategoryRow-01_Desktop-Mobile_2x3_04_Lifestyle.jpg?v=1774909855&width=1024", "route" => "womens", "cta" => "Shop Women's"],
            ["title" => "Apparel", "image" => "https://www.allbirds.com/cdn/shop/files/25Q2_BAU_Site_Collections_3xPromo-Apparel_Lifestyle_Desktop_2x3_1.png?v=1751420661&width=1024", "route" => "shop-all", "cta" => "Shop Apparel"],
        ];

        $infoTitle = "SHOP ALL";
        $infoDesc = "Explore our complete collection of footwear and apparel. Every product is thoughtfully designed and crafted from premium natural materials for comfort that lasts all day.";

        require __DIR__ . "/../views/shop-all.php";
    }
}

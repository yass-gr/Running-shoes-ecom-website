<?php

require_once __DIR__ . "/../utils/helpers.php";

class HomeController
{
    public function index(): void
    {
        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../models/product.php";
        require_once __DIR__ . "/../models/product_variant.php";

        $productModel = new Product($pdo);
        $variantModel = new ProductVariant($pdo);

        $all = $productModel->findAll();
        usort($all, fn($a, $b) => strtotime($b["created_at"]) - strtotime($a["created_at"]));

        $items = [];
        foreach ($all as $p) {
            if (count($items) >= 30) break;
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
                $swatches[] = ["name" => $c, "hex" => colorToHex($c), "thumb" => $v["thumbnail"] ?? ""];
            }
            $items[] = [
                "name" => $p["name"],
                "price" => $p["base_price"],
                "image" => $thumb,
                "color" => $swatches[0]["name"] ?? "",
                "swatches" => $swatches,
            ];
        }

        require __DIR__ . "/../views/home.php";
    }
}

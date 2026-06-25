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
            $swatches = [];
            foreach ($variants as $v) {
                $c = $v["color"] ?? "";
                if ($c === "") continue;
                $swatches[] = ["name" => $c, "hex" => colorToHex($c), "thumb" => $v["thumbnail"] ?? ""];
            }

            $badge = null;
            $totalStock = 0;
            foreach ($variants as $v) {
                $qty = (int) ($v["stock_quantity"] ?? 0);
                $totalStock += $qty;
            }
            if ($totalStock <= 400) {
                $badge = "LAST FEW";
            } else {
                $createdAt = strtotime($p["created_at"]);
                if ($createdAt && (time() - $createdAt) < 30 * 24 * 60 * 60) {
                    $badge = "NEW";
                }
            }

            $salePct = (float) ($p["sale"] ?? 0);
            $salePrice = $salePct > 0 ? $p["base_price"] * (1 - $salePct / 100) : null;

            $items[] = [
                "name" => $p["name"],
                "price" => $p["base_price"],
                "sale"  => $salePct,
                "sale_price" => $salePrice,
                "total_stock" => $totalStock,
                "image" => $thumb,
                "color" => $swatches[0]["name"] ?? "",
                "swatches" => $swatches,
                "badge" => $badge,
            ];
        }

        require __DIR__ . "/../views/home.php";
    }
}

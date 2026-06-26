<?php

require_once __DIR__ . "/../utils/helpers.php";

class SearchController
{
    public function index(): void
    {
        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../models/product.php";
        require_once __DIR__ . "/../models/product_variant.php";

        $productModel = new Product($pdo);
        $variantModel = new ProductVariant($pdo);

        $query = trim($_GET["q"] ?? "");

        if ($query !== "") {
            $all = $productModel->search($query);
        } else {
            $all = [];
        }

        $products = [];
        foreach ($all as $p) {
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

            $products[] = [
                "id"    => $p["id"],
                "name"  => $p["name"],
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

        $perPage = 24;
        $productCount = count($products);
        $totalPages = max(1, (int) ceil($productCount / $perPage));
        $currentPage = max(1, min((int) ($_GET["page"] ?? 1), $totalPages));
        $pageProducts = array_slice($products, ($currentPage - 1) * $perPage, $perPage);

        require __DIR__ . "/../views/search.php";
    }
}

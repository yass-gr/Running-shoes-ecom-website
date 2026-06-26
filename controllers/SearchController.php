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
            $all = $productModel->findAll();
            shuffle($all);
            $all = array_slice($all, 0, 10);
        }

        $products = [];
        foreach ($all as $p) {
            $variants = $variantModel->findByProduct($p["id"]);
            $first = $variants[0] ?? [];
            $thumb = $first["thumbnail"] ?? "";
            if ($thumb === "" || $thumb === null) continue;

            $swatches = [];
            $seenColors = [];
            foreach ($variants as $v) {
                $c = $v["color"] ?? "";
                if ($c === "" || isset($seenColors[$c])) continue;
                $seenColors[$c] = true;
                $c = stripColorSole($c);
                $swatches[] = ["name" => $c, "hex" => colorToHex($c), "thumb" => $v["thumbnail"] ?? ""];
            }

            $badge = null;
            $totalStock = 0;
            foreach ($variants as $v) {
                $qty = (int) ($v["stock_quantity"] ?? 0);
                $totalStock += $qty;
            }
            $sales = (int) ($p["sales"] ?? 0);
            if ($sales >= 400) {
                $badge = "BESTSELLER";
            } elseif ($totalStock <= 400) {
                $badge = "LAST FEW";
            } else {
                $createdAt = strtotime($p["created_at"]);
                if ($createdAt && (time() - $createdAt) < 30 * 24 * 60 * 60) {
                    $badge = "NEW";
                }
            }

            $discount = computeVariantDiscount($variants, (float)$p["base_price"]);

            $products[] = [
                "id"    => $p["id"],
                "name"  => $p["name"],
                "price" => $p["base_price"],
                "sales" => $discount,
                "sale_price" => $discount["sale_price"] ?? null,
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

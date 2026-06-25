<?php

require_once __DIR__ . "/../utils/helpers.php";

class SaleController
{
    public function index(): void
    {
        require_once __DIR__ . "/../config/database.php";
        require_once __DIR__ . "/../models/product.php";
        require_once __DIR__ . "/../models/product_variant.php";
        require_once __DIR__ . "/../models/discount.php";

        $productModel = new Product($pdo);
        $variantModel = new ProductVariant($pdo);
        $discountModel = new Discount($pdo);

        $activeDiscounts = $discountModel->findActive();
        $discountIds = array_column($activeDiscounts, "id");

        if (empty($discountIds)) {
            $products = [];
        } else {
            $placeholders = implode(",", array_fill(0, count($discountIds), "?"));
            $all = $productModel->fetchAll(
                "SELECT DISTINCT p.*, b.name AS brand_name, c.material AS category_material
                 FROM Products p
                 JOIN Brands b ON b.id = p.brand_id
                 JOIN Categories c ON c.id = p.category_id
                 JOIN Product_variants pv ON pv.product_id = p.id
                 WHERE pv.discount_id IN ($placeholders)
                 ORDER BY p.name",
                $discountIds
            );

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
                    $swatches[] = [
                        "name" => $c,
                        "hex" => colorToHex($c),
                        "thumb" => $v["thumbnail"] ?? "",
                    ];
                }

                $discountPrice = null;
                foreach ($variants as $v) {
                    if ($v["discount_id"] !== null && in_array($v["discount_id"], $discountIds)) {
                        $d = $discountModel->findById($v["discount_id"]);
                        if ($d) {
                            if ($d["discount_type"] === "%") {
                                $discountPrice = $p["base_price"] - ($p["base_price"] * $d["value"] / 100);
                            } else {
                                $discountPrice = $p["base_price"] - $d["value"];
                            }
                            $discountPrice = max(0, $discountPrice);
                        }
                        break;
                    }
                }

                $badge = null;
                $hasLowStock = false;
                foreach ($variants as $v) {
                    if (($v["stock_quantity"] ?? 0) <= 1) {
                        $hasLowStock = true;
                        break;
                    }
                }
                if ($hasLowStock) {
                    $badge = "LAST FEW";
                } else {
                    $createdAt = strtotime($p["created_at"]);
                    if ($createdAt && (time() - $createdAt) < 7 * 24 * 60 * 60) {
                        $badge = "NEW";
                    }
                }

                $products[] = [
                    "id"    => $p["id"],
                    "name"  => $p["name"],
                    "price" => $p["base_price"],
                    "sale_price" => $discountPrice,
                    "image" => $thumb,
                    "color" => $swatches[0]["name"] ?? "",
                    "swatches" => $swatches,
                    "badge" => $badge,
                ];
            }
        }

        $sort = $_GET["sort"] ?? "featured";
        if ($sort === "price_asc") {
            usort($products, fn($a, $b) => $a["price"] <=> $b["price"]);
        } elseif ($sort === "price_desc") {
            usort($products, fn($a, $b) => $b["price"] <=> $a["price"]);
        } elseif ($sort === "name") {
            usort($products, fn($a, $b) => strcmp($a["name"], $b["name"]));
        }

        $perPage = 24;
        $totalProducts = count($products);
        $totalPages = max(1, (int) ceil($totalProducts / $perPage));
        $currentPage = max(1, min((int) ($_GET["page"] ?? 1), $totalPages));
        $pageProducts = array_slice($products, ($currentPage - 1) * $perPage, $perPage);

        $categories = [
            ["title" => "Men's", "image" => "https://www.allbirds.com/cdn/shop/files/26Q2_CanvasCruiser_Site_Homepage_CategoryRow-01_Desktop-Mobile_2x3_01_Lifestyle.jpg?v=1774909856&width=1024", "route" => "mens", "cta" => "Shop Men's"],
            ["title" => "Women's", "image" => "https://www.allbirds.com/cdn/shop/files/26Q2_CanvasCruiser_Site_Homepage_CategoryRow-01_Desktop-Mobile_2x3_04_Lifestyle.jpg?v=1774909855&width=1024", "route" => "womens", "cta" => "Shop Women's"],
            ["title" => "Apparel", "image" => "https://www.allbirds.com/cdn/shop/files/25Q2_BAU_Site_Collections_3xPromo-Apparel_Lifestyle_Desktop_2x3_1.png?v=1751420661&width=1024", "route" => "shop-all", "cta" => "Shop Apparel"],
        ];

        require __DIR__ . "/../views/sale.php";
    }
}

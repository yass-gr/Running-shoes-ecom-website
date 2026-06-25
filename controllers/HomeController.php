<?php

function colorToHex(string $name): string {
    $map = [
        'anthracite' => '#383838', 'black' => '#000000', 'white' => '#ffffff',
        'natural'  => '#f5f0e1', 'blizzard' => '#e8edf2', 'warm' => '#fff5e6',
        'core'     => '#1a1a1a', 'jet' => '#0a0a0a', 'true' => '#1a1a1a',
        'dark'     => '#2d2d2d', 'light' => '#d4d4d4', 'medium' => '#a8a8a8',
        'stormy'   => '#6b7b8d', 'deep' => '#0d1b2a', 'navy' => '#000080',
        'grey'     => '#808080', 'hazy' => '#c8b8d0', 'mushroom' => '#c4b0a0',
        'mist'     => '#d8d8d8', 'blue' => '#0000ff', 'red' => '#e35335',
        'poppy'    => '#e35335', 'port' => '#5c3a4a', 'rustic' => '#8b5e3c',
        'wheat'    => '#f5deb3', 'kelly' => '#4cbb17', 'mid' => '#ffd700',
        'thunder'  => '#445c4c', 'rugged' => '#746655', 'stony' => '#a0937d',
        'sea'      => '#2e8b57', 'sulphur' => '#e8d500', 'green' => '#008000',
        'color'    => '#e0e0e0',
    ];
    $first = strtolower(explode(' ', trim(explode('(', $name)[0]))[0]);
    return $map[$first] ?? '#cccccc';
}

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
                $swatches[] = ["name" => $c, "hex" => colorToHex($c)];
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

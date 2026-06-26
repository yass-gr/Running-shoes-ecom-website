<?php

function e($value): string {
  return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function d($value): string {
  return trim(urldecode((string) ($value ?? "")));
}

function m($cents): string {
  return "$" . rtrim(rtrim(number_format(((int) $cents) / 100, 2), "0"), ".");
}

function imageUrl($src): string {
  if (!$src) return "";
  return str_starts_with($src, "//") ? "https:" . $src : $src;
}

function resolveSwatch(array $product): string {
  $swatch = $product["colorcode"] ?? null;
  if ($swatch) return $swatch;

  $hue = $product["hues"][0] ?? strtolower(d($product["colorName"] ?? ""));
  $map = [
    "black" => "#1f1f1f", "grey" => "#9a9994", "gray" => "#9a9994",
    "white" => "#f7f5ee", "beige" => "#d8c8ad", "red" => "#a53a32",
    "yellow" => "#d7b443", "green" => "#4f6f50", "blue" => "#2f557f",
    "brown" => "#6a4f3f",
  ];
  return $map[$hue] ?? "#d8d3c8";
}

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

function getFilterColors($variantModel): array {
    $raw = $variantModel->getDistinctColors();
    $seen = [];
    $colors = [];
    foreach ($raw as $r) {
        $base = trim(explode('(', $r["color"])[0]);
        $label = trim(preg_replace('/\s+/', ' ', $base));
        $key = strtolower($label);
        if ($key === '' || isset($seen[$key])) continue;
        $seen[$key] = true;
        $colors[] = [
            "label" => $label,
            "hex" => filterColorHex($label),
            "white" => strpos(strtolower($label), 'white') !== false || strpos(strtolower($label), 'blizzard') !== false,
        ];
    }
    usort($colors, fn($a, $b) => strcmp($a["label"], $b["label"]));
    return $colors;
}

function filterColorHex(string $name): string {
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
    $base = trim(explode('(', $name)[0]);
    $words = explode(' ', $base);
    for ($i = count($words) - 1; $i >= 0; $i--) {
        $key = strtolower(trim($words[$i]));
        if (isset($map[$key])) return $map[$key];
    }
    return '#cccccc';
}

function computeVariantDiscount(array $variants, float $basePrice): ?array {
    foreach ($variants as $v) {
        if (!empty($v["discount_type"]) && !empty($v["discount_value"])) {
            $discountValue = (float) $v["discount_value"];
            $salePrice = $basePrice;
            if ($v["discount_type"] === "%") {
                $salePrice = $basePrice - ($basePrice * $discountValue / 100);
            } else {
                $salePrice = $basePrice - $discountValue;
            }
            $salePrice = max(0, round($salePrice, 2));
            return [
                "type" => $v["discount_type"],
                "value" => $discountValue,
                "sale_price" => $salePrice,
            ];
        }
    }
    return null;
}

function stripColorSole(string $color): string {
    return trim(explode('(', $color)[0]);
}

function parseSizes(array $product): array {
  $sizes = [];
  foreach (($product["sizes"] ?? []) as $size) {
    $label = $size["title"] ?? "";
    $label = trim(explode("(", $label)[0]);
    $label = trim(explode("/", $label)[0]);
    if ($label !== "") $sizes[] = $label;
  }
  return array_slice($sizes, 0, 8);
}

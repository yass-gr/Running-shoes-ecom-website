<?php

require_once __DIR__ . "/helpers.php";

function productsDataPath(): string {
  return __DIR__ . "/../testdata(temporary)/allbirds_products.json";
}

function loadAllProducts(): array {
  return json_decode(file_get_contents(productsDataPath()), true) ?? [];
}

function getCollectionProducts(array $allProducts): array {
  $products = array_values(array_filter($allProducts, static function ($p) {
    return in_array("collection:apr26", $p["tags"] ?? [], true)
      && !empty($p["masterName"])
      && !empty($p["images"][0]["src"]);
  }));

  usort($products, static function ($a, $b) {
    $order = ["shoes" => 0, "apparel" => 1, "" => 2];
    $aType = $order[$a["type"] ?? ""] ?? 2;
    $bType = $order[$b["type"] ?? ""] ?? 2;
    return $aType <=> $bType;
  });

  return $products;
}

function buildVariantMap(array $allProducts): array {
  $map = [];
  $seen = [];
  foreach ($allProducts as $p) {
    $m = $p["master"] ?? $p["handle"] ?? "p" . $p["id"];
    $cc = $p["colorcode"] ?? "#d8d3c8";
    $key = $m . "|" . $cc;
    if (isset($seen[$key])) continue;
    $seen[$key] = true;
    $img = imageUrl($p["images"][0]["src"] ?? "");
    $hover = imageUrl($p["images"][1]["src"] ?? $p["images"][0]["src"] ?? "");
    $map[$m][] = [
      "url" => $p["url"] ?? "#",
      "colorcode" => $cc,
      "image" => $img,
      "hoverImage" => $hover !== $img ? $hover : "",
    ];
  }
  return $map;
}

function extractFilterOptions(array $products): array {
  $types = []; $genders = []; $materials = []; $editions = []; $hues = [];
  foreach ($products as $p) {
    if (!empty($p["type"])) $types[$p["type"]] = true;
    foreach ($p["tags"] ?? [] as $tag) {
      if (preg_match('/^allbirds::gender\s*=>\s*(.+)$/', $tag, $m)) $genders[trim($m[1])] = true;
      if (preg_match('/^allbirds::material\s*=>\s*(.+)$/', $tag, $m)) $materials[trim($m[1])] = true;
      if (preg_match('/^allbirds::edition\s*=>\s*(.+)$/', $tag, $m)) $editions[trim($m[1])] = true;
      if (preg_match('/^allbirds::hue\s*=>\s*(.+)$/', $tag, $m)) $hues[trim($m[1])] = true;
    }
  }
  return [array_keys($types), array_keys($genders), array_keys($materials), array_keys($editions), array_keys($hues)];
}

function extractProductTags(array $product): array {
  $gender = ""; $material = ""; $edition = ""; $hue = "";
  foreach ($product["tags"] ?? [] as $tag) {
    if (preg_match('/^allbirds::gender\s*=>\s*(.+)$/', $tag, $m)) $gender = trim($m[1]);
    if (preg_match('/^allbirds::material\s*=>\s*(.+)$/', $tag, $m)) $material = trim($m[1]);
    if (preg_match('/^allbirds::edition\s*=>\s*(.+)$/', $tag, $m)) $edition = trim($m[1]);
    if (preg_match('/^allbirds::hue\s*=>\s*(.+)$/', $tag, $m)) $hue = trim($m[1]);
  }
  return [$gender, $material, $edition, $hue];
}

function getProductsByGender(array $allProducts, string $gender): array {
  $products = array_values(array_filter($allProducts, static function ($p) {
    return !empty($p["masterName"]) && !empty($p["images"][0]["src"]);
  }));

  usort($products, static function ($a, $b) {
    $order = ["shoes" => 0, "apparel" => 1, "" => 2];
    $aType = $order[$a["type"] ?? ""] ?? 2;
    $bType = $order[$b["type"] ?? ""] ?? 2;
    return $aType <=> $bType;
  });

  return array_values(array_filter($products, static function ($p) use ($gender) {
    foreach ($p["tags"] ?? [] as $tag) {
      if (preg_match('/^allbirds::gender\s*=>\s*(.+)$/', $tag, $m) && trim($m[1]) === $gender) return true;
    }
    return false;
  }));
}

function getSaleProducts(array $allProducts): array {
  $products = array_values(array_filter($allProducts, static function ($p) {
    return !empty($p["compareAtPrice"])
      && $p["compareAtPrice"] > ($p["price"] ?? 0)
      && !empty($p["masterName"])
      && !empty($p["images"][0]["src"]);
  }));

  usort($products, static function ($a, $b) {
    $order = ["shoes" => 0, "apparel" => 1, "" => 2];
    $aType = $order[$a["type"] ?? ""] ?? 2;
    $bType = $order[$b["type"] ?? ""] ?? 2;
    return $aType <=> $bType;
  });

  return $products;
}

function getAllProducts(array $allProducts): array {
  $products = array_values(array_filter($allProducts, static function ($p) {
    return !empty($p["masterName"]) && !empty($p["images"][0]["src"]);
  }));

  usort($products, static function ($a, $b) {
    $order = ["shoes" => 0, "apparel" => 1, "" => 2];
    $aType = $order[$a["type"] ?? ""] ?? 2;
    $bType = $order[$b["type"] ?? ""] ?? 2;
    return $aType <=> $bType;
  });

  return $products;
}

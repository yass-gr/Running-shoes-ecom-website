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

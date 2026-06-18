<?php
$product = $product ?? [];

$escape = static fn($value) => htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
$decode = static fn($value) => trim(urldecode((string) ($value ?? "")));
$money = static fn($value) => "$" . rtrim(rtrim(number_format(((int) $value) / 100, 2), "0"), ".");

$name = $decode($product["masterName"] ?? $product["fullName"] ?? "Product");
$color = $decode($product["colorName"] ?? "");
$price = $money($product["price"] ?? 0);
$compareAtPrice = !empty($product["compareAtPrice"]) ? $money($product["compareAtPrice"]) : null;
$url = $product["url"] ?? (!empty($product["handle"]) ? "/products/" . $product["handle"] : "#");
$image = $product["images"][0]["src"] ?? "";
$image = str_starts_with($image, "//") ? "https:" . $image : $image;
$fallbackImage = $product["images"][1]["src"] ?? $image;
$fallbackImage = str_starts_with($fallbackImage, "//") ? "https:" . $fallbackImage : $fallbackImage;
$swatch = $product["colorcode"] ?? null;

if (!$swatch) {
  $hue = $product["hues"][0] ?? strtolower($color);
  $swatches = [
    "black" => "#1f1f1f",
    "grey" => "#9a9994",
    "gray" => "#9a9994",
    "white" => "#f7f5ee",
    "beige" => "#d8c8ad",
    "red" => "#a53a32",
    "yellow" => "#d7b443",
    "green" => "#4f6f50",
    "blue" => "#2f557f",
    "brown" => "#6a4f3f",
  ];
  $swatch = $swatches[$hue] ?? "#d8d3c8";
}

$sizes = array_keys($product["sizes"] ?? []);
$sizes = array_slice($sizes, 0, 8);
?>

<article class="product-card">
  <a class="product-card__media" href="<?= $escape($url) ?>" aria-label="<?= $escape($name . ($color ? " - " . $color : "")) ?>"<?php if ($fallbackImage && $fallbackImage !== $image): ?> data-hover="<?= $escape($fallbackImage) ?>"<?php endif; ?>>
    <span class="product-card__badge">NEW</span>
    <img
      class="product-card__image"
      src="<?= $escape($image) ?>"
      alt="<?= $escape($name . ($color ? " - " . $color : "")) ?>"
      loading="lazy"
      width="1024"
      height="1024"
    />
  </a>

  <div class="product-card__body">
    <a class="product-card__link" href="<?= $escape($url) ?>">
      <h2 class="product-card__name"><?= $escape($name) ?></h2>
      <?php if ($color): ?>
        <p class="product-card__color"><?= $escape($color) ?></p>
      <?php endif; ?>
      <p class="product-card__price">
        <span class="<?= $compareAtPrice ? "product-card__price-sale" : "" ?>"><?= $escape($price) ?></span>
        <?php if ($compareAtPrice): ?>
          <span class="product-card__price-compare"><?= $escape($compareAtPrice) ?></span>
        <?php endif; ?>
      </p>
    </a>

    <div class="product-card__meta">
      <span class="product-card__swatch" style="background-color: <?= $escape($swatch) ?>"></span>
      <?php if (!empty($sizes)): ?>
        <span class="product-card__sizes"><?= $escape(implode(", ", $sizes)) ?></span>
      <?php endif; ?>
    </div>
  </div>

  <?php if (!empty($sizes)): ?>
    <div class="product-card__quick-add" aria-label="Available sizes">
      <?php foreach ($sizes as $size): ?>
        <button type="button"><?= $escape($size) ?></button>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</article>

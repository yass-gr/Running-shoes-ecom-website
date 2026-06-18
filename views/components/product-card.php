<?php
require_once __DIR__ . "/../../utils/helpers.php";

$product = $product ?? [];
$colorVariants = $colorVariants ?? [];

$name = d($product["masterName"] ?? $product["fullName"] ?? "Product");
$color = d($product["colorName"] ?? "");
$price = m($product["price"] ?? 0);
$compareAtPrice = !empty($product["compareAtPrice"]) ? m($product["compareAtPrice"]) : null;
$url = $product["url"] ?? (!empty($product["handle"]) ? "/products/" . $product["handle"] : "#");
$image = imageUrl($product["images"][0]["src"] ?? "");
$fallbackImage = imageUrl($product["images"][1]["src"] ?? $product["images"][0]["src"] ?? "");
$swatch = resolveSwatch($product);
$sizes = parseSizes($product);
?>
<article class="product-card">
  <a class="product-card__media" href="<?= e($url) ?>" aria-label="<?= e($name . ($color ? " - " . $color : "")) ?>"<?php if ($fallbackImage && $fallbackImage !== $image): ?> data-hover="<?= e($fallbackImage) ?>"<?php endif; ?>>
    <span class="product-card__badge">NEW</span>
    <img
      class="product-card__image"
      src="<?= e($image) ?>"
      alt="<?= e($name . ($color ? " - " . $color : "")) ?>"
      loading="lazy"
      width="1024"
      height="1024"
    />
  </a>

  <div class="product-card__body">
    <a class="product-card__link" href="<?= e($url) ?>">
      <h2 class="product-card__name"><?= e($name) ?></h2>
      <?php if ($color): ?>
        <p class="product-card__color"><?= e($color) ?></p>
      <?php endif; ?>
      <p class="product-card__price">
        <span class="<?= $compareAtPrice ? "product-card__price-sale" : "" ?>"><?= e($price) ?></span>
        <?php if ($compareAtPrice): ?>
          <span class="product-card__price-compare"><?= e($compareAtPrice) ?></span>
        <?php endif; ?>
      </p>
    </a>

    <div class="product-card__meta">
      <div class="product-card__swatches">
        <?php $shownVariants = array_slice($colorVariants, 0, 5); ?>
        <?php $extraCount = count($colorVariants) - 5; ?>
        <?php foreach ($shownVariants as $variant): ?>
          <a
            class="product-card__swatch <?= $variant["colorcode"] === $swatch ? "product-card__swatch--active" : "" ?>"
            href="<?= e($variant["url"]) ?>"
            style="background-color: <?= e($variant["colorcode"]) ?>"
            data-url="<?= e($variant["url"]) ?>"
            data-image="<?= e($variant["image"]) ?>"
            data-hover="<?= e($variant["hoverImage"]) ?>"
            aria-label="View this color"
          ></a>
        <?php endforeach; ?>
        <?php if ($extraCount > 0): ?>
          <span class="product-card__swatch-more">+<?= $extraCount ?></span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <?php if (!empty($sizes)): ?>
    <div class="product-card__quick-add" aria-label="Available sizes">
      <?php foreach ($sizes as $size): ?>
        <button type="button"><?= e($size) ?></button>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</article>

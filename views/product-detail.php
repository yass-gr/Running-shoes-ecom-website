<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= e($product["name"] ?? "Product") ?></title>
    <link rel="stylesheet" href="../assets/css/main.css" />
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js"></script>
    <script src="https://code.jquery.com/jquery-4.0.0.js" integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U=" crossorigin="anonymous"></script>
    <script type="module" src="../assets/js/shared/nav.js" defer></script>
    <script type="module" src="../assets/js/shared/cart.js" defer></script>
  </head>
  <body>
    <?php require_once __DIR__ . "/components/navbar.php"; ?>

    <main class="product-detail-page">
      <div class="product-detail">
        <div class="product-detail__gallery">
          <?php $mainImage = $images[0]["thumbnail"] ?? $variants[0]["thumbnail"] ?? ""; ?>
          <?php if ($mainImage): ?>
            <img class="product-detail__main-image" src="<?= e($mainImage) ?>" alt="<?= e($product["name"]) ?>" />
          <?php endif; ?>
          <div class="product-detail__thumbnails">
            <?php foreach ($images as $img): ?>
              <img src="<?= e($img["thumbnail"] ?? $img["top_view"] ?? "") ?>" alt="" loading="lazy" />
            <?php endforeach; ?>
          </div>
        </div>

        <div class="product-detail__info">
          <h1 class="product-detail__title"><?= e($product["name"]) ?></h1>
          <p class="product-detail__brand"><?= e($product["brand_name"] ?? "") ?></p>
          <p class="product-detail__price">
            <?php if ($salePrice): ?>
              <span style="text-decoration:line-through;color:#999;">$<?= number_format($product["base_price"]) ?></span>
              <span style="color:#d32f2f;">$<?= number_format($salePrice) ?></span>
            <?php else: ?>
              $<?= number_format($product["base_price"]) ?>
            <?php endif; ?>
          </p>
          <p class="product-detail__description"><?= e($product["description"] ?? "") ?></p>

          <div class="product-detail__variants">
            <label for="color-select">Color:</label>
            <select id="color-select">
              <?php $seenColors = []; ?>
              <?php foreach ($variants as $v): ?>
                <?php if (!in_array($v["color"], $seenColors)): ?>
                  <?php $seenColors[] = $v["color"]; ?>
                  <option value="<?= e($v["color"]) ?>"><?= e($v["color"]) ?></option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>

            <label for="size-select">Size:</label>
            <select id="size-select">
              <?php $seenSizes = []; ?>
              <?php foreach ($variants as $v): ?>
                <?php if (!in_array($v["size"], $seenSizes)): ?>
                  <?php $seenSizes[] = $v["size"]; ?>
                  <option value="<?= e($v["size"]) ?>"><?= e($v["size"]) ?></option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
          </div>

          <form method="POST" action="?route=cart">
            <input type="hidden" name="action" value="add" />
            <input type="hidden" name="product_id" value="<?= $product["id"] ?>" />
            <button class="product-detail__add-to-cart" type="submit">Add to Cart</button>
          </form>
        </div>
      </div>
    </main>

    <?php require_once __DIR__ . "/components/footer.php"; ?>
  </body>
</html>

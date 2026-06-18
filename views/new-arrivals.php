<?php
$productsPath = __DIR__ . "/../testdata(temporary)/allbirds_products.json";
$products = json_decode(file_get_contents($productsPath), true) ?? [];
$products = array_values(array_filter($products, static function ($product) {
  return in_array("collection:apr26", $product["tags"] ?? [], true)
    && !empty($product["masterName"])
    && !empty($product["images"][0]["src"]);
}));

usort($products, static function ($a, $b) {
  $typeOrder = ["shoes" => 0, "apparel" => 1, "" => 2];
  $aType = $typeOrder[$a["type"] ?? ""] ?? 2;
  $bType = $typeOrder[$b["type"] ?? ""] ?? 2;
  return $aType <=> $bType;
});

$productCount = count($products);
$categories = [
  [
    "title" => "Men's",
    "image" => "https://www.allbirds.com/cdn/shop/files/26Q2_CanvasCruiser_Site_Homepage_CategoryRow-01_Desktop-Mobile_2x3_01_Lifestyle.jpg?v=1774909856&width=1024",
    "href" => "#",
    "cta" => "Shop Men's",
  ],
  [
    "title" => "Women's",
    "image" => "https://www.allbirds.com/cdn/shop/files/26Q2_CanvasCruiser_Site_Homepage_CategoryRow-01_Desktop-Mobile_2x3_04_Lifestyle.jpg?v=1774909855&width=1024",
    "href" => "#",
    "cta" => "Shop Women's",
  ],
  [
    "title" => "Apparel",
    "image" => "https://www.allbirds.com/cdn/shop/files/25Q2_BAU_Site_Collections_3xPromo-Apparel_Lifestyle_Desktop_2x3_1.png?v=1751420661&width=1024",
    "href" => "#",
    "cta" => "Shop Apparel",
  ],
];
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NEW ARRIVALS '26</title>
    <link rel="stylesheet" href="../assets/css/main.css" />
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js"></script>
    <script
      src="https://code.jquery.com/jquery-4.0.0.js"
      integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U="
      crossorigin="anonymous"
    ></script>
    <script type="module" src="../assets/js/shared/nav.js" defer></script>
    <script type="module" src="../assets/js/shared/cart.js" defer></script>
    <script type="module" src="../assets/js/shared/productCard.js" defer></script>
  </head>
  <body>
    <?php require_once __DIR__ . "/components/navbar.php"; ?>

    <main class="collection-page">
      

      <section class="collection-toolbar" aria-label="Collection controls">
        <div class="collection-toolbar__left">
          <button class="collection-toolbar__filter" type="button">
            <svg fill="none" viewBox="0 0 24 24" aria-hidden="true">
              <path fill="currentColor" d="M17.6 7h-6.7a1.25 1.25 0 0 0-2.3 0h-2a.5.5 0 0 0 0 1h2a1.25 1.25 0 0 0 2.3 0h6.7a.5.5 0 0 0 0-1ZM6.6 11.5a.5.5 0 0 0 0 1h6a1.25 1.25 0 0 0 2.3 0h2.7a.5.5 0 0 0 0-1h-2.7a1.25 1.25 0 0 0-2.3 0h-6ZM6.1 16.5a.5.5 0 0 1 .5-.5h2a1.25 1.25 0 0 1 2.3 0h6.7a.5.5 0 0 1 0 1h-6.7a1.25 1.25 0 0 1-2.3 0h-2a.5.5 0 0 1-.5-.5Z"/>
            </svg>
            Filter
          </button>
          <span class="collection-toolbar__count">(<?= $productCount ?> products)</span>
        </div>

        

        <select class="collection-toolbar__sort" aria-label="Sort by">
          <option>Date, new to old</option>
          <option>Featured</option>
          <option>Best selling</option>
          <option>Price, low to high</option>
          <option>Price, high to low</option>
        </select>
      </section>

      <section class="collection-grid" aria-label="New arrivals products">
        <?php foreach ($products as $product): ?>
          <?php require __DIR__ . "/components/product-card.php"; ?>
        <?php endforeach; ?>
      </section>

      <section class="collection-categories" aria-label="Shop more categories">
        <?php foreach ($categories as $category): ?>
          <article class="collection-category">
            <img src="<?= htmlspecialchars($category["image"], ENT_QUOTES, "UTF-8") ?>" alt="" loading="lazy" />
            <div class="collection-category__content">
              <h2><?= htmlspecialchars($category["title"], ENT_QUOTES, "UTF-8") ?></h2>
              <a href="<?= htmlspecialchars($category["href"], ENT_QUOTES, "UTF-8") ?>">
                <?= htmlspecialchars($category["cta"], ENT_QUOTES, "UTF-8") ?>
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </section>
    </main>

    <?php require_once __DIR__ . "/components/footer.php"; ?>
  </body>
</html>

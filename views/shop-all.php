<?php
$productCount = count($products);
$queryParams = $_GET;
$queryParams["route"] = "shop-all";
unset($queryParams["page"]);
$pageUrl = "?" . http_build_query($queryParams);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SHOP ALL</title>
    <link rel="stylesheet" href="../assets/css/main.css" />
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js"></script>
    <script src="https://code.jquery.com/jquery-4.0.0.js" integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U=" crossorigin="anonymous"></script>
    <script type="module" src="../assets/js/shared/nav.js" defer></script>
    <script type="module" src="../assets/js/shared/cart.js" defer></script>
    <script type="module" src="../assets/js/shared/filterModal.js" defer></script>
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
          <div class="collection-toolbar__chips" aria-label="Active filters"></div>
        </div>
        <select class="collection-toolbar__sort" aria-label="Sort by">
          <option value="featured">Featured</option>
          <option value="newest">Date, new to old</option>
          <option value="best_selling">Best selling</option>
          <option value="price_asc">Price, low to high</option>
          <option value="price_desc">Price, high to low</option>
        </select>
      </section>

      <?php require_once __DIR__ . "/components/filter-panel.php"; ?>

      <section class="collection-grid" aria-label="All products">
        <?php if (empty($pageProducts)): ?>
          <p class="collection-grid__empty">No products match your criteria.</p>
        <?php else: ?>
        <?php foreach ($pageProducts as $item): ?>
          <div class="card"
               data-price="<?= $item["price"] ?>"
               data-color="<?= $item["color"] ?>"
               data-material="<?= $item["material"] ?? "" ?>"
               data-sizes="<?= implode(",", $item["sizes"] ?? []) ?>">
            <a href="?route=product&id=<?= $item["id"] ?>">
              <img src="<?= $item["image"] ?>" alt="<?= $item["name"] ?>">
              <?php if ($item["badge"]): ?>
                <span class="card__badge card__badge--<?= str_replace(' ', '_', strtolower($item["badge"])) ?>"><?= $item["badge"] ?></span>
              <?php endif; ?>
              <div class="info">
                <p class="name"><?= $item["name"] ?></p>
                <p class="color"><?= $item["color"] ?></p>
                <p class="price">
                  <?php if (isset($item["sale_price"])): ?>
                    <span style="color:#d32f2f;">$<?= number_format($item["sale_price"]) ?></span>
                    <span style="text-decoration:line-through;color:#999;">$<?= number_format($item["price"]) ?></span>
                  <?php else: ?>
                    $<?= number_format($item["price"]) ?>
                  <?php endif; ?>
                </p>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
        <?php endif; ?>
      </section>

      <?php if ($totalPages > 1): ?>
      <nav class="pagination" aria-label="Page navigation">
        <a class="pagination__btn <?= $currentPage <= 1 ? 'pagination__btn--disabled' : '' ?>"
           href="<?= $pageUrl ?>&amp;page=<?= $currentPage - 1 ?>" <?= $currentPage <= 1 ? 'aria-disabled="true" tabindex="-1"' : '' ?>>
          &#8249; Prev
        </a>
        <div class="pagination__pages">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="pagination__page <?= $i === $currentPage ? 'pagination__page--active' : '' ?>"
               href="<?= $pageUrl ?>&amp;page=<?= $i ?>"><?= $i ?></a>
          <?php endfor; ?>
        </div>
        <a class="pagination__btn <?= $currentPage >= $totalPages ? 'pagination__btn--disabled' : '' ?>"
           href="<?= $pageUrl ?>&amp;page=<?= $currentPage + 1 ?>" <?= $currentPage >= $totalPages ? 'aria-disabled="true" tabindex="-1"' : '' ?>>
          Next &#8250;
        </a>
      </nav>
      <?php endif; ?>

      <?php $infoTitle = "SHOP ALL"; $infoDesc = "Explore our complete collection of footwear and apparel. Every product is thoughtfully designed and crafted from premium natural materials for comfort that lasts all day."; ?>
      <?php require_once __DIR__ . "/components/info-faq.php"; ?>

      <?php require_once __DIR__ . "/components/collection-categories.php"; ?>
    </main>

    <?php require_once __DIR__ . "/components/trust-cards.php"; ?>
    <?php require_once __DIR__ . "/components/footer.php"; ?>
    <script>
      document.querySelector('.collection-toolbar__sort')?.addEventListener('change', function() {
        var url = new URL(window.location.href);
        url.searchParams.set('sort', this.value);
        url.searchParams.delete('page');
        window.location.href = url.toString();
      });
    </script>
  </body>
</html>

<?php
$productCount = count($products);
$queryParams = $_GET;
$queryParams["route"] = "search";
unset($queryParams["page"]);
$pageUrl = "?" . http_build_query($queryParams);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SEARCH</title>
    <link rel="stylesheet" href="../assets/css/main.css" />
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js"></script>
    <script src="https://code.jquery.com/jquery-4.0.0.js" integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U=" crossorigin="anonymous"></script>
    <script type="module" src="../assets/js/shared/nav.js" defer></script>
    <script type="module" src="../assets/js/shared/cart.js" defer></script>
    <script type="module" src="../assets/js/shared/filterModal.js" defer></script>
  </head>
  <body>
    <?php require_once __DIR__ . "/components/navbar.php"; ?>

    <main class="search-page">
      <div class="search-area">
        <form class="search-field" method="get" action="">
          <input type="hidden" name="route" value="search" />
          <input
            class="search-field__input"
            type="text"
            name="q"
            placeholder="What are you looking for?"
            value="<?= e($query ?? "") ?>"
            autofocus
            autocomplete="off"
          />
          <button class="search-field__icon" type="submit" aria-label="Search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"/>
              <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
          </button>
        </form>
      </div>

      <?php if (($query ?? "") !== ""): ?>
        <p class="search-results-info">
          <?= $productCount ?> result<?= $productCount !== 1 ? "s" : "" ?> for "<strong><?= e($query) ?></strong>"
        </p>
      <?php else: ?>
        <p class="search-results-info">
          Explore our picks
        </p>
      <?php endif; ?>

      <section class="collection-grid" aria-label="Search results">
        <?php if (empty($pageProducts)): ?>
          <div class="search-empty">
            <svg class="search-empty__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"/>
              <line x1="21" y1="21" x2="16.65" y2="16.65"/>
              <line x1="8" y1="11" x2="14" y2="11"/>
            </svg>
            <h2 class="search-empty__title">No results found</h2>
            <p class="search-empty__desc">
              <?php if (($query ?? "") !== ""): ?>
                We couldn't find anything matching "<strong><?= e($query) ?></strong>". Try a different search term.
              <?php else: ?>
                Start typing to search our collection.
              <?php endif; ?>
            </p>
          </div>
        <?php else: ?>
          <?php foreach ($pageProducts as $item): ?>
            <div class="card">
              <a href="?route=product&id=<?= $item["id"] ?>">
                <img src="<?= $item["image"] ?? "" ?>" alt="<?= $item["name"] ?>">
                <div class="info">
                  <p class="name"><?= $item["name"] ?></p>
                  <p class="color"><?= $item["color"] ?? "" ?></p>
                  <p class="price">$<?= number_format((float) ($item["price"] ?? 0)) ?></p>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </section>

      <?php if (($totalPages ?? 1) > 1): ?>
      <nav class="pagination" aria-label="Page navigation">
        <a class="pagination__btn <?= ($currentPage ?? 1) <= 1 ? 'pagination__btn--disabled' : '' ?>"
           href="<?= $pageUrl ?>&amp;q=<?= urlencode($query ?? "") ?>&amp;page=<?= ($currentPage ?? 1) - 1 ?>" <?= ($currentPage ?? 1) <= 1 ? 'aria-disabled="true" tabindex="-1"' : '' ?>>
          &#8249; Prev
        </a>
        <div class="pagination__pages">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="pagination__page <?= $i === ($currentPage ?? 1) ? 'pagination__page--active' : '' ?>"
               href="<?= $pageUrl ?>&amp;q=<?= urlencode($query ?? "") ?>&amp;page=<?= $i ?>"><?= $i ?></a>
          <?php endfor; ?>
        </div>
        <a class="pagination__btn <?= ($currentPage ?? 1) >= $totalPages ? 'pagination__btn--disabled' : '' ?>"
           href="<?= $pageUrl ?>&amp;q=<?= urlencode($query ?? "") ?>&amp;page=<?= ($currentPage ?? 1) + 1 ?>" <?= ($currentPage ?? 1) >= $totalPages ? 'aria-disabled="true" tabindex="-1"' : '' ?>>
          Next &#8250;
        </a>
      </nav>
      <?php endif; ?>

      <?php $infoTitle = "SEARCH"; $infoDesc = "Browse our complete collection of thoughtfully designed footwear and apparel, crafted from premium natural materials."; ?>
      <?php require_once __DIR__ . "/components/info-faq.php"; ?>
    </main>

    <?php require_once __DIR__ . "/components/trust-cards.php"; ?>
    <?php require_once __DIR__ . "/components/footer.php"; ?>
  </body>
</html>

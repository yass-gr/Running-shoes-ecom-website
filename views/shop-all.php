<?php $productCount = count($products); ?>
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
    <script type="module" src="../assets/js/shared/productCard.js" defer></script>
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
        </div>
        <select class="collection-toolbar__sort" aria-label="Sort by">
          <option>Date, new to old</option>
          <option>Featured</option>
          <option>Best selling</option>
          <option>Price, low to high</option>
          <option>Price, high to low</option>
        </select>
      </section>

      <div class="filter-overlay" aria-hidden="true"></div>

      <div class="filter-panel" role="dialog" aria-label="Filter products" aria-hidden="true">
        <div class="filter-panel__header">
          <button class="filter-panel__close" type="button" aria-label="Close filter">
            <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
          <span class="filter-panel__title">Collapse Filters</span>
          <span class="filter-panel__count">(<?= $productCount ?> products)</span>
          <button class="filter-panel__apply" type="button">APPLY FILTERS</button>
        </div>

        <div class="filter-panel__body">
          <div class="filter-col filter-col--size">
            <h3 class="filter-col__heading">Size</h3>
            <p class="filter-col__desc">Select your size to narrow results.</p>
            <div class="filter-sizes">
              <?php foreach (["S","M","L","XL","5","5.5","6","6.5","7","7.5","8","8.5","9","9.5","10","10.5","11","11.5","12","12.5","13","13.5","14","15"] as $s): ?>
                <button class="filter-size" type="button" data-filter="size" data-value="<?= $s ?>"><?= $s ?></button>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="filter-col filter-col--color">
            <h3 class="filter-col__heading">Color</h3>
            <div class="filter-colors">
              <?php foreach ([
                ["label" => "Black",   "hex" => "#1a1a1a"],
                ["label" => "Grey",    "hex" => "#9e9e9e"],
                ["label" => "White",   "hex" => "#ffffff", "white" => true],
                ["label" => "Beige",   "hex" => "#d2c6a5"],
                ["label" => "Red",     "hex" => "#c0392b"],
                ["label" => "Yellow",  "hex" => "#f0c040"],
                ["label" => "Green",   "hex" => "#5a7a5a"],
                ["label" => "Blue",    "hex" => "#4a6fa5"],
              ] as $c): ?>
                <button class="filter-color" type="button" data-filter="color" data-value="<?= $c["label"] ?>">
                  <span class="filter-color__swatch <?= ($c["white"] ?? false) ? 'filter-color__swatch--white' : '' ?>" style="background:<?= $c["hex"] ?>"></span>
                  <span class="filter-color__label"><?= $c["label"] ?></span>
                </button>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="filter-col filter-col--price">
            <h3 class="filter-col__heading">Price</h3>
            <div class="filter-checkboxes">
              <?php foreach ([
                ["label" => "Under $75",       "value" => "under75"],
                ["label" => "$76–$100",        "value" => "76-100"],
                ["label" => "$101–$125",       "value" => "101-125"],
                ["label" => "$126–$150",       "value" => "126-150"],
                ["label" => "Over $150",       "value" => "over150", "disabled" => true],
              ] as $p): ?>
                <button class="filter-checkbox <?= ($p["disabled"] ?? false) ? 'filter-checkbox--disabled' : '' ?>" type="button" data-filter="price" data-value="<?= $p["value"] ?>" <?= ($p["disabled"] ?? false) ? 'disabled' : '' ?>>
                  <span class="filter-checkbox__box"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span>
                  <span class="filter-checkbox__label"><?= $p["label"] ?></span>
                </button>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="filter-col filter-col--type">
            <h3 class="filter-col__heading">Product Type</h3>
            <div class="filter-checkboxes">
              <?php foreach (["Everyday Sneakers","Flats","Running Shoes","Slip Ons","Slippers","Socks"] as $t): ?>
                <button class="filter-checkbox" type="button" data-filter="type" data-value="<?= $t ?>">
                  <span class="filter-checkbox__box"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span>
                  <span class="filter-checkbox__label"><?= $t ?></span>
                </button>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="filter-col filter-col--material">
            <h3 class="filter-col__heading">Material</h3>
            <div class="filter-checkboxes">
              <?php foreach (["Alternative-Leather","Canvas","Cotton","Cozy-Collection","Sugar","Tree"] as $mt): ?>
                <button class="filter-checkbox" type="button" data-filter="material" data-value="<?= $mt ?>">
                  <span class="filter-checkbox__box"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span>
                  <span class="filter-checkbox__label"><?= $mt ?></span>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <section class="collection-grid" aria-label="All products">
        <?php foreach ($pageProducts as $item): ?>
          <article class="product-card">
            <a class="product-card__media" href="?route=product&id=<?= $item["id"] ?>" aria-label="<?= $item["name"] ?>">
              <span class="product-card__badge">NEW</span>
              <img class="product-card__image" src="<?= $item["image"] ?>" alt="<?= $item["name"] ?>" loading="lazy" width="1024" height="1024">
            </a>
            <div class="product-card__body">
              <a class="product-card__link" href="?route=product&id=<?= $item["id"] ?>">
                <h2 class="product-card__name"><?= $item["name"] ?></h2>
                <p class="product-card__color"><?= $item["color"] ?></p>
                <p class="product-card__price">$<?= number_format($item["price"], 2) ?></p>
              </a>
              <div class="product-card__meta">
                <div class="product-card__swatches">
                  <?php foreach ($item["swatches"] as $s): ?>
                    <span class="product-card__swatch" style="background-color: <?= $s["hex"] ?>" data-url="?route=product&amp;id=<?= $item["id"] ?>" data-image="<?= $s["thumb"] ?>" data-hover=""></span>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </section>

      <?php if ($totalPages > 1): ?>
      <nav class="pagination" aria-label="Page navigation">
        <a class="pagination__btn <?= $currentPage <= 1 ? 'pagination__btn--disabled' : '' ?>"
           href="?page=<?= $currentPage - 1 ?>" <?= $currentPage <= 1 ? 'aria-disabled="true" tabindex="-1"' : '' ?>>
          &#8249; Prev
        </a>
        <div class="pagination__pages">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="pagination__page <?= $i === $currentPage ? 'pagination__page--active' : '' ?>"
               href="?page=<?= $i ?>"><?= $i ?></a>
          <?php endfor; ?>
        </div>
        <a class="pagination__btn <?= $currentPage >= $totalPages ? 'pagination__btn--disabled' : '' ?>"
           href="?page=<?= $currentPage + 1 ?>" <?= $currentPage >= $totalPages ? 'aria-disabled="true" tabindex="-1"' : '' ?>>
          Next &#8250;
        </a>
      </nav>
      <?php endif; ?>

      <?php $infoTitle = "SHOP ALL"; $infoDesc = "Explore our complete collection of footwear and apparel. Every product is thoughtfully designed and crafted from premium natural materials for comfort that lasts all day."; ?>
      <?php require_once __DIR__ . "/components/info-faq.php"; ?>

      <section class="collection-categories" aria-label="Shop more categories">
        <?php foreach ($categories as $category): ?>
          <article class="collection-category">
            <img src="<?= e($category["image"]) ?>" alt="" loading="lazy" />
            <div class="collection-category__content">
              <h2><?= e($category["title"]) ?></h2>
              <a href="?route=<?= $category["route"] ?>"><?= $category["cta"] ?></a>
            </div>
          </article>
        <?php endforeach; ?>
      </section>
    </main>

    <?php require_once __DIR__ . "/components/trust-cards.php"; ?>
    <?php require_once __DIR__ . "/components/footer.php"; ?>
  </body>
</html>

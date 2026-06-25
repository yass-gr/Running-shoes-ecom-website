<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>mobile ver</title>
    <link rel="stylesheet" href="./assets/css/main.css" />
    <link
      href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=help,person"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&display=swap"
      rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js"></script>
    <script type="module" src="assets/js/index.js" defer></script>
    <script
      src="https://code.jquery.com/jquery-4.0.0.js"
      integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U="
      crossorigin="anonymous"
    ></script>
  </head>
  <body>

    <?php require_once __DIR__ . "/components/navbar.php" ?>

    <section class="hero-container">
      <div id="hero-section">
        <div class="hero-txt">
          <h2>The New Canvas Cruiser Collection</h2>
          <div class="hero-btns">
            <button>SHOP MEN</button>
            <button>SHOP WOMEN</button>
          </div>
        </div>
      </div>
    </section>

    <main>
      <section id="category">
        <div>
          <span>
            <h2>NEW ARRIVALS</h2>
            <button>SHOP MEN</button>
            <button>SHOP WOMEN</button>
          </span>
        </div>
        <div>
          <span>
            <h2>MENS</h2>
            <button>SHOP MEN</button></span>
        </div>
        <div>
          <span>
            <h2>WOMENS</h2>
            <button>SHOP WOMEN</button>
          </span>
        </div>
        <div>
          <span>
            <h2>BEST SELLERS</h2>
            <button>SHOP MEN</button>
            <button>SHOP WOMEN</button>
          </span>
        </div>
      </section>

      <section id="new-arrivals">
        <h2 class="title">NEW ARRIVALS</h2>
        <div class="arrow">-></div>
        <span class="leftControl"></span>
        <span class="rightControl"></span>
        <div class="newAriv1content">
          <?php foreach ($newArrivals as $i => $item): ?>
            <?php if ($i >= 20) break; ?>
            <div data-name="<?= $item["name"] ?>" data-price="<?= $item["price"] ?>">
              <img src="<?= $item["image"] ?>" alt="<?= $item["name"] ?>">
            </div>
          <?php endforeach; ?>
        </div>
        <div class="info">
          <h2 class="collName">June's Collection</h2>
          <p class="pName"><?= $newArrivals[0]["name"] ?> - $<?= number_format($newArrivals[0]["price"], 2) ?></p>
          <div>
            <button>SHOP MEN</button>
            <button>SHOP WOMEN</button>
          </div>
        </div>
      </section>

      <section class="specialCollectionSection">
        <div class="main">
          <h1>Bold By Nature</h1>
          <p>Show your true colors in eight exclusive Pantone-curated shades.</p>
          <button>SHOP NOW</button>
        </div>
        <div></div>
        <div></div>
        <div></div>
      </section>

      <section class="newArrivals2">
        <div class="header">
          <h2 class="title">NEW ARRIVALS</h2>
          <div class="arrows">
            <div class="left">⇠</div>
            <div class="right">⇢</div>
          </div>
        </div>
        <div class="content">
          <?php foreach ($newArrivals as $i => $item): ?>
            <?php if ($i < 20) continue; ?>
            <div class="card">
              <img src="<?= $item["image"] ?>" alt="<?= $item["name"] ?>">
              <div class="info">
                <p class="name"><?= $item["name"] ?></p>
                <p class="cName"><?= $item["color"] ?></p>
                <p class="price">$<?= number_format($item["price"], 2) ?></p>
                <div class="hue"></div>
              </div>
              <span class="badge">NEW</span>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

      <section id="categories2">
        <div class="category">
          <img class="img1" src="assets/images/c3.jpg" alt="" />
          <h1>Road Running</h1>
          <div class="btns">
            <button>SHOP MEN</button>
            <button>SHOP WOMEN</button>
          </div>
        </div>
        <div class="category">
          <img class="img2" src="assets/images/c1.jpg" alt="" />
          <h1>Trail Running</h1>
          <div class="btns">
            <button>SHOP MEN</button>
            <button>SHOP WOMEN</button>
          </div>
        </div>
        <div class="category">
          <img class="img3" src="assets/images/c2.jpg" alt="" />
          <h1>Marathon</h1>
          <div class="btns">
            <button>SHOP MEN</button>
            <button>SHOP WOMEN</button>
          </div>
        </div>
      </section>

      <?php require_once __DIR__ . "/components/trust-cards.php" ?>
    </main>

    <?php require_once __DIR__ . "/components/footer.php" ?>

  </body>
</html>

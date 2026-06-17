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
    <script type="module" src="assets/js/index.js" defer></script>
    <script
      src="https://code.jquery.com/jquery-4.0.0.js"
      integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U="
      crossorigin="anonymous"
    ></script>
  </head>
  <body>


  <!-- header------- -->
   <?php require_once __DIR__ . "/views/shared/navbar.php" ?>



   <!-- hero------ -->
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

    <!-- categories------ -->
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
            <button>SHOP MEN</button></span
          >
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





    <!-- new arrivals 1-------- -->
      <section id="new-arrivals">
        <h2 class="title">NEW ARRIVALS</h2>
        <div class="arrow">
          ->
        </div>
         <span class="leftControl"></span>
          <span class="rightControl">
          </span>
        <div class="newAriv1content">
         
        </div>
        <div class="info">
          <h2 class="collName">June's Collection</h2>
          <p class="pName">Product Name - price $</p>
          <div>
            <button>SHOP MEN</button>
            <button>SHOP WOMEN</button>
          </div>
        </div>
      </section>



      <!-- new arrivals 2 -->
      <section class="specialCollectionSection">
        <div class="main">
          <h1>Bold By Nature</h1>
          <p>Show your true colors in eight exclusivePantone-curated shades.</p>
          <button>SHOP NOW</button>
        </div>
        <div></div>
        <div></div>
        <div></div>
      </section>

      <!-- new arrivals 2---- --> 
      <section class="newArrivals2">
        <h2 class="title">NEW ARRIVALS</h2>
        <div class="content">later...</div>
      </section>


      <!-- categories 2 ---------- -->
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




      <!-- trust cards------ -->
      <section class="cards-section">
        <div class="cards-grid">
          <div class="card">
            <p class="card-title">Wear All Day Comfort</p>
            <p class="card-body">
              Lightweight, bouncy, and wildly comfortable, Allbirds shoes make
              any outing feel effortless. Slip in, lace up, or slide them on and
              enjoy the comfy support.
            </p>
          </div>
          <div class="card">
            <p class="card-title">Sustainability in Every Step</p>
            <p class="card-body">
              From materials to transport, we're working to reduce our carbon
              footprint to near zero. Holding ourselves accountable and striving
              for climate goals isn't a 30-year goal—it's now.
            </p>
          </div>
          <div class="card">
            <p class="card-title">Materials from the Earth</p>
            <p class="card-body">
              We replace petroleum-based synthetics with natural alternatives
              wherever we can. Like using wool, tree fiber, and sugarcane.
              They're soft, breathable, and better for the planet—win, win, win.
            </p>
          </div>
        </div>
      </section>
    </main>





  <!-- footer------ -->
  <?php require_once __DIR__ . "/views/shared/footer.php" ?>

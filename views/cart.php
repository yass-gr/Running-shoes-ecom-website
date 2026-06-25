<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../assets/css/main.css" />
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js"></script>
    <script src="https://code.jquery.com/jquery-4.0.0.js" integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U=" crossorigin="anonymous"></script>
    <script type="module" src="../assets/js/shared/nav.js" defer></script>
    <script type="module" src="../assets/js/shared/cart.js" defer></script>
  </head>
  <body>
    <?php require_once __DIR__ . "/components/navbar.php"; ?>

    <main class="cart-page">
      <h1>Shopping Cart</h1>

      <?php if (empty($cartItems)): ?>
        <p class="cart-page__empty">Your cart is empty.</p>
        <a href="?route=shop-all" class="cart-page__continue">Continue Shopping</a>
      <?php else: ?>
        <table class="cart-table">
          <thead>
            <tr>
              <th>Product</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Total</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php $grandTotal = 0; ?>
            <?php foreach ($cartItems as $item): ?>
              <?php $total = $item["price"] * $item["quantity"]; ?>
              <?php $grandTotal += $total; ?>
              <tr>
                <td>
                  <img src="<?= e($item["image"]) ?>" alt="<?= e($item["name"]) ?>" width="80" />
                  <span><?= e($item["name"]) ?></span>
                </td>
                <td>$<?= number_format($item["price"]) ?></td>
                <td>
                  <form method="POST" action="?route=cart" style="display:inline;">
                    <input type="hidden" name="action" value="update" />
                    <input type="hidden" name="product_id" value="<?= $item["product_id"] ?>" />
                    <input type="number" name="quantity" value="<?= $item["quantity"] ?>" min="1" style="width:60px;" />
                    <button type="submit">Update</button>
                  </form>
                </td>
                <td>$<?= number_format($total) ?></td>
                <td>
                  <form method="POST" action="?route=cart" style="display:inline;">
                    <input type="hidden" name="action" value="remove" />
                    <input type="hidden" name="product_id" value="<?= $item["product_id"] ?>" />
                    <button type="submit">Remove</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3"><strong>Total</strong></td>
              <td><strong>$<?= number_format($grandTotal) ?></strong></td>
              <td></td>
            </tr>
          </tfoot>
        </table>

        <div class="cart-page__actions">
          <a href="?route=checkout" class="cart-page__checkout">Proceed to Checkout</a>
          <a href="?route=shop-all" class="cart-page__continue">Continue Shopping</a>
        </div>
      <?php endif; ?>
    </main>

    <?php require_once __DIR__ . "/components/footer.php"; ?>
  </body>
</html>

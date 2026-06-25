<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Checkout</title>
    <link rel="stylesheet" href="../assets/css/main.css" />
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js"></script>
    <script src="https://code.jquery.com/jquery-4.0.0.js" integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U=" crossorigin="anonymous"></script>
    <script type="module" src="../assets/js/shared/nav.js" defer></script>
    <script type="module" src="../assets/js/shared/cart.js" defer></script>
  </head>
  <body>
    <?php require_once __DIR__ . "/components/navbar.php"; ?>

    <main class="checkout-page">
      <h1>Checkout</h1>

      <?php if (empty($cartItems)): ?>
        <p>Your cart is empty. <a href="?route=shop-all">Continue Shopping</a></p>
      <?php else: ?>
        <form method="POST" action="?route=checkout" class="checkout-form">
          <section class="checkout-form__section">
            <h2>Shipping Information</h2>
            <div>
              <label for="address">Address</label>
              <input type="text" id="address" name="address" required />
            </div>
            <div>
              <label for="city">City</label>
              <select id="city" name="city_id" required>
                <option value="">Select your city</option>
                <?php if (isset($cities)): ?>
                  <?php foreach ($cities as $city): ?>
                    <option value="<?= $city["id"] ?>"><?= e($city["name"]) ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div>
              <label for="phone">Phone</label>
              <input type="tel" id="phone" name="phone" required />
            </div>
          </section>

          <section class="checkout-form__section">
            <h2>Order Summary</h2>
            <table class="cart-table">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Price</th>
                  <th>Qty</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <?php $grandTotal = 0; ?>
                <?php foreach ($cartItems as $item): ?>
                  <?php $total = $item["price"] * $item["quantity"]; ?>
                  <?php $grandTotal += $total; ?>
                  <tr>
                    <td><?= e($item["name"]) ?></td>
                    <td>$<?= number_format($item["price"]) ?></td>
                    <td><?= $item["quantity"] ?></td>
                    <td>$<?= number_format($total) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3"><strong>Total</strong></td>
                  <td><strong>$<?= number_format($grandTotal) ?></strong></td>
                </tr>
              </tfoot>
            </table>
          </section>

          <section class="checkout-form__section">
            <h2>Payment</h2>
            <p>Payment will be collected upon delivery.</p>
          </section>

          <button type="submit" class="checkout-form__submit">Place Order</button>
        </form>
      <?php endif; ?>
    </main>

    <?php require_once __DIR__ . "/components/footer.php"; ?>
  </body>
</html>

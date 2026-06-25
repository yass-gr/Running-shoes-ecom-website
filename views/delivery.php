<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Delivery Dashboard</title>
    <link rel="stylesheet" href="../assets/css/main.css" />
    <script src="https://code.jquery.com/jquery-4.0.0.js" integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U=" crossorigin="anonymous"></script>
  </head>
  <body>
    <main class="delivery-page">
      <h1>Delivery Dashboard</h1>

      <?php if (empty($assignedOrders)): ?>
        <p>No orders assigned to you yet.</p>
      <?php else: ?>
        <table class="cart-table">
          <thead>
            <tr>
              <th>Order #</th>
              <th>Client</th>
              <th>City</th>
              <th>Total</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($assignedOrders as $order): ?>
              <tr>
                <td>#<?= $order["id"] ?></td>
                <td><?= e($order["client_name"] ?? "") ?></td>
                <td><?= e($order["city_name"] ?? "") ?></td>
                <td>$<?= number_format($order["subtotal"]) ?></td>
                <td><?= e($order["shipping_status"]) ?></td>
                <td>
                  <?php if ($order["shipping_status"] === "shipped"): ?>
                    <form method="POST" action="?route=delivery">
                      <input type="hidden" name="action" value="mark_delivered" />
                      <input type="hidden" name="order_id" value="<?= $order["id"] ?>" />
                      <button type="submit">Mark Delivered</button>
                    </form>
                  <?php else: ?>
                    <?= e(ucfirst($order["shipping_status"])) ?>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </main>
  </body>
</html>

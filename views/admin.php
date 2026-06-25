<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/main.css" />
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js"></script>
    <script src="https://code.jquery.com/jquery-4.0.0.js" integrity="sha256-9fsHeVnKBvqh3FB2HYu7g2xseAZ5MlN6Kz/qnkASV8U=" crossorigin="anonymous"></script>
  </head>
  <body>
    <?php require_once __DIR__ . "/components/navbar.php"; ?>

    <main class="admin-page">
      <h1>Admin Dashboard</h1>

      <?php if (!isset($_SESSION["user_id"]) || ($_SESSION["user_role"] ?? "") !== "admin"): ?>
        <p>Access denied. <a href="?route=login">Login as admin</a></p>
      <?php else: ?>
        <div class="admin-page__stats">
          <div class="admin-stat">
            <h3>Total Products</h3>
            <p><?= $stats["total_products"] ?? 0 ?></p>
          </div>
          <div class="admin-stat">
            <h3>Total Orders</h3>
            <p><?= $stats["total_orders"] ?? 0 ?></p>
          </div>
          <div class="admin-stat">
            <h3>Total Users</h3>
            <p><?= $stats["total_users"] ?? 0 ?></p>
          </div>
          <div class="admin-stat">
            <h3>Pending Orders</h3>
            <p><?= $stats["pending_orders"] ?? 0 ?></p>
          </div>
        </div>

        <section class="admin-page__section">
          <h2>Recent Orders</h2>
          <?php if (empty($recentOrders)): ?>
            <p>No orders yet.</p>
          <?php else: ?>
            <table class="cart-table">
              <thead>
                <tr>
                  <th>Order #</th>
                  <th>Client</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recentOrders as $order): ?>
                  <tr>
                    <td>#<?= $order["id"] ?></td>
                    <td><?= e($order["client_name"] ?? "") ?></td>
                    <td>$<?= number_format($order["subtotal"]) ?></td>
                    <td><?= e($order["shipping_status"]) ?></td>
                    <td><?= e($order["created_at"]) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </section>

        <section class="admin-page__section">
          <h2>Low Stock Alerts</h2>
          <?php if (empty($lowStock)): ?>
            <p>All products are well-stocked.</p>
          <?php else: ?>
            <table class="cart-table">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>SKU</th>
                  <th>Stock</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($lowStock as $item): ?>
                  <tr>
                    <td><?= e($item["product_name"] ?? "") ?></td>
                    <td><?= e($item["sku"] ?? "") ?></td>
                    <td><?= $item["stock_quantity"] ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </section>
      <?php endif; ?>
    </main>

    <?php require_once __DIR__ . "/components/footer.php"; ?>
  </body>
</html>

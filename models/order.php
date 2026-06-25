<?php

require_once __DIR__ . '/Model.php';

class Order extends Model {

    // ── READ ────────────────────────────────────────────────────────────────

    public function findById(int $id): ?array {
        return $this->fetchOne(
            'SELECT o.*,
                    c.name  AS city_name,
                    d.code  AS discount_code,
                    d.discount_type, d.value AS discount_value,
                    CONCAT(u.first_name, " ", u.last_name) AS client_name,
                    CONCAT(dg.first_name, " ", dg.last_name) AS delivery_guy_name
             FROM Orders o
             JOIN Users u           ON u.id  = o.client_id
             JOIN Cities c          ON c.id  = o.city_id
             LEFT JOIN Discounts d  ON d.id  = o.discount_id
             LEFT JOIN Users dg     ON dg.id = o.delivery_guy_id
             WHERE o.id = ?',
            [$id]
        );
    }

    public function findByClient(int $clientId): array {
        return $this->fetchAll(
            'SELECT o.*, c.name AS city_name, d.code AS discount_code
             FROM Orders o
             JOIN Cities c         ON c.id = o.city_id
             LEFT JOIN Discounts d ON d.id = o.discount_id
             WHERE o.client_id = ?
             ORDER BY o.created_at DESC',
            [$clientId]
        );
    }

    public function findByStatus(string $status): array {
        return $this->fetchAll(
            'SELECT o.*, CONCAT(u.first_name, " ", u.last_name) AS client_name,
                    c.name AS city_name
             FROM Orders o
             JOIN Users u   ON u.id = o.client_id
             JOIN Cities c  ON c.id = o.city_id
             WHERE o.shipping_status = ?
             ORDER BY o.created_at ASC',
            [$status]
        );
    }

    public function findByDeliveryGuy(int $deliveryGuyId): array {
        return $this->fetchAll(
            'SELECT o.*, CONCAT(u.first_name, " ", u.last_name) AS client_name,
                    c.name AS city_name
             FROM Orders o
             JOIN Users u  ON u.id = o.client_id
             JOIN Cities c ON c.id = o.city_id
             WHERE o.delivery_guy_id = ?
             ORDER BY o.created_at DESC',
            [$deliveryGuyId]
        );
    }

    /**
     * All line items for a given order.
     */
    public function getItems(int $orderId): array {
        return $this->fetchAll(
            'SELECT oi.*,
                    pv.size, pv.color, pv.sku,
                    p.name  AS product_name,
                    pi.thumbnail
             FROM Order_items oi
             JOIN Product_variants pv ON pv.id = oi.variant_id
             JOIN Products p          ON p.id  = pv.product_id
             LEFT JOIN Product_img pi ON pi.id = pv.product_img_id
             WHERE oi.order_id = ?',
            [$orderId]
        );
    }

    // ── CREATE ───────────────────────────────────────────────────────────────

    /**
     * Insert the order header and return the new order ID.
     * Line items are inserted separately via addItem().
     */
    public function create(
        int    $clientId,
        int    $cityId,
        float  $subtotal,
        ?int   $discountId = null
    ): int {
        return $this->insert(
            'INSERT INTO Orders (client_id, city_id, subtotal, discount_id, shipping_status)
             VALUES (?, ?, ?, ?, "pending")',
            [$clientId, $cityId, $subtotal, $discountId]
        );
    }

    public function addItem(
        int   $orderId,
        int   $variantId,
        int   $quantity,
        float $priceAtPurchase
    ): int {
        return $this->insert(
            'INSERT INTO Order_items (order_id, variant_id, quantity, price_at_purchase)
             VALUES (?, ?, ?, ?)',
            [$orderId, $variantId, $quantity, $priceAtPurchase]
        );
    }

    // ── UPDATE ───────────────────────────────────────────────────────────────

    public function updateStatus(int $id, string $status): int {
        return $this->execute(
            'UPDATE Orders SET shipping_status = ? WHERE id = ?',
            [$status, $id]
        );
    }

    public function assignDeliveryGuy(int $orderId, int $deliveryGuyId): int {
        return $this->execute(
            'UPDATE Orders SET delivery_guy_id = ?, shipping_status = "shipped" WHERE id = ?',
            [$deliveryGuyId, $orderId]
        );
    }

    public function markDelivered(int $orderId): int {
        return $this->execute(
            'UPDATE Orders SET shipping_status = "delivered", delivered_at = NOW() WHERE id = ?',
            [$orderId]
        );
    }

    public function cancel(int $orderId): int {
        return $this->execute(
            'UPDATE Orders SET shipping_status = "cancelled", cancelled_at = NOW() WHERE id = ?',
            [$orderId]
        );
    }

    // ── DELETE ───────────────────────────────────────────────────────────────

    public function delete(int $id): int {
        return $this->execute('DELETE FROM Orders WHERE id = ?', [$id]);
    }
}
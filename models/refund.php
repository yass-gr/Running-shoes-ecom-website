<?php

require_once __DIR__ . '/Model.php';

class Refund extends Model {

    // ── READ ────────────────────────────────────────────────────────────────

    public function findById(int $id): ?array {
        return $this->fetchOne(
            'SELECT r.*, o.client_id, o.shipping_status AS order_status
             FROM Refunds r
             JOIN Orders o ON o.id = r.order_id
             WHERE r.id = ?',
            [$id]
        );
    }

    public function findByOrder(int $orderId): array {
        return $this->fetchAll(
            'SELECT * FROM Refunds WHERE order_id = ? ORDER BY created_at DESC',
            [$orderId]
        );
    }

    public function findByStatus(string $status): array {
        return $this->fetchAll(
            'SELECT r.*, o.client_id,
                    CONCAT(u.first_name, " ", u.last_name) AS client_name
             FROM Refunds r
             JOIN Orders o ON o.id = r.order_id
             JOIN Users u  ON u.id = o.client_id
             WHERE r.status = ?
             ORDER BY r.created_at ASC',
            [$status]
        );
    }

    /**
     * Line items included in a refund.
     */
    public function getItems(int $refundId): array {
        return $this->fetchAll(
            'SELECT ri.*,
                    pv.size, pv.color, pv.sku,
                    p.name AS product_name
             FROM Refund_items ri
             JOIN Product_variants pv ON pv.id = ri.variant_id
             JOIN Products p          ON p.id  = pv.product_id
             WHERE ri.refund_id = ?',
            [$refundId]
        );
    }

    // ── CREATE ───────────────────────────────────────────────────────────────

    public function create(int $orderId, float $amount, string $reason): int {
        return $this->insert(
            'INSERT INTO Refunds (order_id, amount, reason, status)
             VALUES (?, ?, ?, "processing")',
            [$orderId, $amount, $reason]
        );
    }

    public function addItem(
        int   $refundId,
        int   $variantId,
        int   $quantity,
        float $priceAtPurchase
    ): int {
        return $this->insert(
            'INSERT INTO Refund_items (refund_id, variant_id, quantity, price_at_purchase)
             VALUES (?, ?, ?, ?)',
            [$refundId, $variantId, $quantity, $priceAtPurchase]
        );
    }

    // ── UPDATE ───────────────────────────────────────────────────────────────

    public function updateStatus(int $id, string $status): int {
        return $this->execute(
            'UPDATE Refunds SET status = ? WHERE id = ?',
            [$status, $id]
        );
    }

    // ── DELETE ───────────────────────────────────────────────────────────────

    public function delete(int $id): int {
        return $this->execute('DELETE FROM Refunds WHERE id = ?', [$id]);
    }
}
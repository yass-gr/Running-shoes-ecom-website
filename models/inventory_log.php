<?php

require_once __DIR__ . '/Model.php';

class InventoryLog extends Model {

    // ── READ ────────────────────────────────────────────────────────────────

    public function findAll(int $limit = 50): array {
        return $this->fetchAll(
            'SELECT il.*,
                    pv.sku, pv.color, pv.size,
                    p.name  AS product_name,
                    CONCAT(u.first_name, " ", u.last_name) AS admin_name
             FROM Inventory_logs il
             JOIN Product_variants pv ON pv.id = il.product_variant_id
             JOIN Products p          ON p.id  = pv.product_id
             JOIN Users u             ON u.id  = il.admin_id
             ORDER BY il.restocked_at DESC
             LIMIT ?',
            [$limit]
        );
    }

    public function findByVariant(int $variantId): array {
        return $this->fetchAll(
            'SELECT il.*,
                    CONCAT(u.first_name, " ", u.last_name) AS admin_name
             FROM Inventory_logs il
             JOIN Users u ON u.id = il.admin_id
             WHERE il.product_variant_id = ?
             ORDER BY il.restocked_at DESC',
            [$variantId]
        );
    }

    public function findByAdmin(int $adminId): array {
        return $this->fetchAll(
            'SELECT il.*,
                    pv.sku, pv.color, pv.size,
                    p.name AS product_name
             FROM Inventory_logs il
             JOIN Product_variants pv ON pv.id = il.product_variant_id
             JOIN Products p          ON p.id  = pv.product_id
             WHERE il.admin_id = ?
             ORDER BY il.restocked_at DESC',
            [$adminId]
        );
    }

    // ── CREATE ───────────────────────────────────────────────────────────────

    /**
     * Log a restock event and bump the variant's stock in one transaction.
     */
    public function restock(
        int   $variantId,
        int   $adminId,
        int   $quantityAdded,
        float $unitPrice
    ): int {
        $this->db->beginTransaction();
        try {
            $logId = $this->insert(
                'INSERT INTO Inventory_logs (product_variant_id, admin_id, quantity_added, unit_price)
                 VALUES (?, ?, ?, ?)',
                [$variantId, $adminId, $quantityAdded, $unitPrice]
            );

            $this->execute(
                'UPDATE Product_variants SET stock_quantity = stock_quantity + ? WHERE id = ?',
                [$quantityAdded, $variantId]
            );

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }

        return $logId;
    }

    // ── DELETE ───────────────────────────────────────────────────────────────

    public function delete(int $id): int {
        return $this->execute('DELETE FROM Inventory_logs WHERE id = ?', [$id]);
    }
}
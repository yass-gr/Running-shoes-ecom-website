<?php

require_once __DIR__ . '/Model.php';

class ShippingRule extends Model {

    // ── READ ────────────────────────────────────────────────────────────────

    public function findAll(): array {
        return $this->fetchAll('SELECT * FROM Shipping_rules ORDER BY name');
    }

    public function findById(int $id): ?array {
        return $this->fetchOne('SELECT * FROM Shipping_rules WHERE id = ?', [$id]);
    }

    /**
     * Get the shipping rule for a given city.
     */
    public function findByCity(int $cityId): ?array {
        return $this->fetchOne(
            'SELECT sr.*
             FROM Shipping_rules sr
             JOIN Cities c ON c.shipping_rule_id = sr.id
             WHERE c.id = ?',
            [$cityId]
        );
    }

    // ── CREATE ───────────────────────────────────────────────────────────────

    public function create(
        string $name,
        float  $price,
        float  $deliveryCommission,
        float  $freeShippingThreshold
    ): int {
        return $this->insert(
            'INSERT INTO Shipping_rules (name, price, delivery_commission, free_shipping_threshold)
             VALUES (?, ?, ?, ?)',
            [$name, $price, $deliveryCommission, $freeShippingThreshold]
        );
    }

    // ── UPDATE ───────────────────────────────────────────────────────────────

    public function update(
        int    $id,
        string $name,
        float  $price,
        float  $deliveryCommission,
        float  $freeShippingThreshold
    ): int {
        return $this->execute(
            'UPDATE Shipping_rules
             SET name = ?, price = ?, delivery_commission = ?, free_shipping_threshold = ?
             WHERE id = ?',
            [$name, $price, $deliveryCommission, $freeShippingThreshold, $id]
        );
    }

    // ── DELETE ───────────────────────────────────────────────────────────────

    public function delete(int $id): int {
        return $this->execute('DELETE FROM Shipping_rules WHERE id = ?', [$id]);
    }
}
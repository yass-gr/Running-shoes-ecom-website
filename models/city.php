<?php

require_once __DIR__ . '/Model.php';

class City extends Model {

    // ── READ ────────────────────────────────────────────────────────────────

    public function findAll(): array {
        return $this->fetchAll(
            'SELECT c.*, sr.name AS rule_name, sr.price AS shipping_price,
                    sr.free_shipping_threshold
             FROM Cities c
             LEFT JOIN Shipping_rules sr ON sr.id = c.shipping_rule_id
             ORDER BY c.name'
        );
    }

    public function findById(int $id): ?array {
        return $this->fetchOne(
            'SELECT c.*, sr.name AS rule_name, sr.price AS shipping_price,
                    sr.delivery_commission, sr.free_shipping_threshold
             FROM Cities c
             LEFT JOIN Shipping_rules sr ON sr.id = c.shipping_rule_id
             WHERE c.id = ?',
            [$id]
        );
    }

    public function findByName(string $name): ?array {
        return $this->fetchOne(
            'SELECT * FROM Cities WHERE name = ?',
            [$name]
        );
    }

    // ── CREATE ───────────────────────────────────────────────────────────────

    public function create(string $name, ?int $shippingRuleId): int {
        return $this->insert(
            'INSERT INTO Cities (name, shipping_rule_id) VALUES (?, ?)',
            [$name, $shippingRuleId]
        );
    }

    // ── UPDATE ───────────────────────────────────────────────────────────────

    public function update(int $id, string $name, ?int $shippingRuleId): int {
        return $this->execute(
            'UPDATE Cities SET name = ?, shipping_rule_id = ? WHERE id = ?',
            [$name, $shippingRuleId, $id]
        );
    }

    // ── DELETE ───────────────────────────────────────────────────────────────

    public function delete(int $id): int {
        return $this->execute('DELETE FROM Cities WHERE id = ?', [$id]);
    }
}
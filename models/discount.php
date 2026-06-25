<?php

require_once __DIR__ . '/Model.php';

class Discount extends Model {

    // ── READ ────────────────────────────────────────────────────────────────

    public function findAll(): array {
        return $this->fetchAll('SELECT * FROM Discounts ORDER BY start_date DESC');
    }

    public function findById(int $id): ?array {
        return $this->fetchOne('SELECT * FROM Discounts WHERE id = ?', [$id]);
    }

    public function findByCode(string $code): ?array {
        return $this->fetchOne(
            'SELECT * FROM Discounts WHERE code = ?',
            [$code]
        );
    }

    /**
     * Return only currently active, valid discounts.
     */
    public function findActive(): array {
        return $this->fetchAll(
            'SELECT * FROM Discounts
             WHERE is_active = TRUE
               AND (start_date IS NULL OR start_date <= NOW())
               AND (end_date   IS NULL OR end_date   >= NOW())
             ORDER BY end_date ASC'
        );
    }

    /**
     * Validate a coupon code and return it if usable, null otherwise.
     */
    public function validateCode(string $code): ?array {
        return $this->fetchOne(
            'SELECT * FROM Discounts
             WHERE code       = ?
               AND is_active  = TRUE
               AND (start_date IS NULL OR start_date <= NOW())
               AND (end_date   IS NULL OR end_date   >= NOW())
               AND (n_uses     IS NULL OR n_uses > 0)',
            [$code]
        );
    }

    // ── CREATE ───────────────────────────────────────────────────────────────

    public function create(
        ?string $code,
        string  $discountType,   // 'fixed' or '%'
        float   $value,
        ?string $startDate,
        ?string $endDate,
        ?int    $nUses,
        bool    $isActive = true
    ): int {
        return $this->insert(
            'INSERT INTO Discounts (code, discount_type, value, start_date, end_date, n_uses, is_active)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$code, $discountType, $value, $startDate, $endDate, $nUses, (int) $isActive]
        );
    }

    // ── UPDATE ───────────────────────────────────────────────────────────────

    public function update(
        int     $id,
        ?string $code,
        string  $discountType,
        float   $value,
        ?string $startDate,
        ?string $endDate,
        ?int    $nUses,
        bool    $isActive
    ): int {
        return $this->execute(
            'UPDATE Discounts
             SET code = ?, discount_type = ?, value = ?,
                 start_date = ?, end_date = ?, n_uses = ?, is_active = ?
             WHERE id = ?',
            [$code, $discountType, $value, $startDate, $endDate, $nUses, (int) $isActive, $id]
        );
    }

    /**
     * Decrement remaining uses by 1 when a code is applied to an order.
     */
    public function decrementUses(int $id): int {
        return $this->execute(
            'UPDATE Discounts SET n_uses = n_uses - 1 WHERE id = ? AND n_uses > 0',
            [$id]
        );
    }

    public function setActive(int $id, bool $isActive): int {
        return $this->execute(
            'UPDATE Discounts SET is_active = ? WHERE id = ?',
            [(int) $isActive, $id]
        );
    }

    // ── DELETE ───────────────────────────────────────────────────────────────

    public function delete(int $id): int {
        return $this->execute('DELETE FROM Discounts WHERE id = ?', [$id]);
    }
}
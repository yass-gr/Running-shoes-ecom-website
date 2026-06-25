<?php

require_once __DIR__ . '/Model.php';

class Category extends Model {

    // ── READ ────────────────────────────────────────────────────────────────

    public function findAll(): array {
        return $this->fetchAll('SELECT * FROM Categories ORDER BY material');
    }

    public function findById(int $id): ?array {
        return $this->fetchOne('SELECT * FROM Categories WHERE id = ?', [$id]);
    }

    // ── CREATE ───────────────────────────────────────────────────────────────

    /**
     * Categories use a manually supplied ID (not AUTO_INCREMENT).
     */
    public function create(int $id, string $material): bool {
        return $this->execute(
            'INSERT INTO Categories (id, material) VALUES (?, ?)',
            [$id, $material]
        ) > 0;
    }

    // ── UPDATE ───────────────────────────────────────────────────────────────

    public function update(int $id, string $material): int {
        return $this->execute(
            'UPDATE Categories SET material = ? WHERE id = ?',
            [$material, $id]
        );
    }

    // ── DELETE ───────────────────────────────────────────────────────────────

    public function delete(int $id): int {
        return $this->execute('DELETE FROM Categories WHERE id = ?', [$id]);
    }
}
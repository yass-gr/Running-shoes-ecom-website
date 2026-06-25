<?php

require_once __DIR__ . '/Model.php';

class Brand extends Model {

    // ── READ ────────────────────────────────────────────────────────────────

    public function findAll(): array {
        return $this->fetchAll('SELECT * FROM Brands ORDER BY name');
    }

    public function findById(int $id): ?array {
        return $this->fetchOne('SELECT * FROM Brands WHERE id = ?', [$id]);
    }

    public function findByName(string $name): ?array {
        return $this->fetchOne('SELECT * FROM Brands WHERE name = ?', [$name]);
    }

    // ── CREATE ───────────────────────────────────────────────────────────────

    public function create(string $name): int {
        return $this->insert(
            'INSERT INTO Brands (name) VALUES (?)',
            [$name]
        );
    }

    // ── UPDATE ───────────────────────────────────────────────────────────────

    public function update(int $id, string $name): int {
        return $this->execute(
            'UPDATE Brands SET name = ? WHERE id = ?',
            [$name, $id]
        );
    }

    // ── DELETE ───────────────────────────────────────────────────────────────

    public function delete(int $id): int {
        return $this->execute('DELETE FROM Brands WHERE id = ?', [$id]);
    }
}
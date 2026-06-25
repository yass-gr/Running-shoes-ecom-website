<?php

require_once __DIR__ . '/Model.php';

class Product extends Model {

    // ── READ ────────────────────────────────────────────────────────────────

    public function findAll(): array {
        return $this->fetchAll(
            'SELECT p.*, b.name AS brand_name, c.material AS category_material
             FROM Products p
             JOIN Brands b     ON b.id = p.brand_id
             JOIN Categories c ON c.id = p.category_id
             ORDER BY p.name'
        );
    }

    public function findById(int $id): ?array {
        return $this->fetchOne(
            'SELECT p.*, b.name AS brand_name, c.material AS category_material
             FROM Products p
             JOIN Brands b     ON b.id = p.brand_id
             JOIN Categories c ON c.id = p.category_id
             WHERE p.id = ?',
            [$id]
        );
    }

    public function findByBrand(int $brandId): array {
        return $this->fetchAll(
            'SELECT p.*, c.material AS category_material
             FROM Products p
             JOIN Categories c ON c.id = p.category_id
             WHERE p.brand_id = ?
             ORDER BY p.name',
            [$brandId]
        );
    }

    public function findByCategory(int $categoryId): array {
        return $this->fetchAll(
            'SELECT p.*, b.name AS brand_name
             FROM Products p
             JOIN Brands b ON b.id = p.brand_id
             WHERE p.category_id = ?
             ORDER BY p.name',
            [$categoryId]
        );
    }

    /**
     * Simple name-based search (LIKE %term%).
     */
    public function search(string $term): array {
        return $this->fetchAll(
            'SELECT p.*, b.name AS brand_name, c.material AS category_material
             FROM Products p
             JOIN Brands b     ON b.id = p.brand_id
             JOIN Categories c ON c.id = p.category_id
             WHERE p.name LIKE ? OR p.description LIKE ?
             ORDER BY p.name',
            ["%$term%", "%$term%"]
        );
    }

    public function getTopSellers(int $limit = 10): array {
        return $this->fetchAll(
            'SELECT p.*, b.name AS brand_name
             FROM Products p
             JOIN Brands b ON b.id = p.brand_id
             ORDER BY p.sales DESC
             LIMIT ?',
            [$limit]
        );
    }

    // ── CREATE ───────────────────────────────────────────────────────────────

    public function create(
        string $name,
        int    $brandId,
        int    $categoryId,
        string $description,
        float  $basePrice
    ): int {
        return $this->insert(
            'INSERT INTO Products (name, brand_id, category_id, description, base_price, sales)
             VALUES (?, ?, ?, ?, ?, 0)',
            [$name, $brandId, $categoryId, $description, $basePrice]
        );
    }

    // ── UPDATE ───────────────────────────────────────────────────────────────

    public function update(
        int    $id,
        string $name,
        int    $brandId,
        int    $categoryId,
        string $description,
        float  $basePrice
    ): int {
        return $this->execute(
            'UPDATE Products
             SET name = ?, brand_id = ?, category_id = ?, description = ?, base_price = ?
             WHERE id = ?',
            [$name, $brandId, $categoryId, $description, $basePrice, $id]
        );
    }

    public function incrementSales(int $id, int $qty = 1): int {
        return $this->execute(
            'UPDATE Products SET sales = sales + ? WHERE id = ?',
            [$qty, $id]
        );
    }

    // ── DELETE ───────────────────────────────────────────────────────────────

    public function delete(int $id): int {
        return $this->execute('DELETE FROM Products WHERE id = ?', [$id]);
    }
}
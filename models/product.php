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

    public function findByGender(string $gender): array {
        return $this->fetchAll(
            'SELECT p.*, b.name AS brand_name, c.material AS category_material
             FROM Products p
             JOIN Brands b     ON b.id = p.brand_id
             JOIN Categories c ON c.id = p.category_id
             WHERE p.gender = ?
             ORDER BY p.name',
            [$gender]
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

    public function findByDiscountIds(array $discountIds): array {
        $placeholders = implode(",", array_fill(0, count($discountIds), "?"));
        return $this->fetchAll(
            "SELECT DISTINCT p.*, b.name AS brand_name, c.material AS category_material
             FROM Products p
             JOIN Brands b ON b.id = p.brand_id
             JOIN Categories c ON c.id = p.category_id
             JOIN Product_variants pv ON pv.product_id = p.id
             WHERE pv.discount_id IN ($placeholders)
             ORDER BY p.name",
            $discountIds
        );
    }

    public function getTopSellers(int $limit = 10): array {
        return $this->fetchAll(
            'SELECT p.*, b.name AS brand_name
             FROM Products p
             JOIN Brands b ON b.id = p.brand_id
             ORDER BY p.sale DESC
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
        float  $basePrice,
        string $gender = 'unisex'
    ): int {
        return $this->insert(
            'INSERT INTO Products (name, brand_id, category_id, description, base_price, gender, sale)
             VALUES (?, ?, ?, ?, ?, ?, 0.00)',
            [$name, $brandId, $categoryId, $description, $basePrice, $gender]
        );
    }

    // ── UPDATE ───────────────────────────────────────────────────────────────

    public function update(
        int    $id,
        string $name,
        int    $brandId,
        int    $categoryId,
        string $description,
        float  $basePrice,
        string $gender = 'unisex'
    ): int {
        return $this->execute(
            'UPDATE Products
             SET name = ?, brand_id = ?, category_id = ?, description = ?, base_price = ?, gender = ?
             WHERE id = ?',
            [$name, $brandId, $categoryId, $description, $basePrice, $gender, $id]
        );
    }

    // ── DELETE ───────────────────────────────────────────────────────────────

    public function delete(int $id): int {
        return $this->execute('DELETE FROM Products WHERE id = ?', [$id]);
    }
}
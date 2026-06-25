<?php

require_once __DIR__ . '/Model.php';

class Collection extends Model {

    // ── READ ────────────────────────────────────────────────────────────────

    public function findAll(): array {
        return $this->fetchAll(
            'SELECT * FROM Collections ORDER BY release_date DESC'
        );
    }

    public function findActive(): array {
        return $this->fetchAll(
            'SELECT * FROM Collections WHERE is_active = TRUE ORDER BY release_date DESC'
        );
    }

    public function findById(int $id): ?array {
        return $this->fetchOne(
            'SELECT * FROM Collections WHERE id = ?',
            [$id]
        );
    }

    /**
     * All products belonging to a collection.
     */
    public function getProducts(int $collectionId): array {
        return $this->fetchAll(
            'SELECT p.*, b.name AS brand_name
             FROM Products p
             JOIN Product_collections pc ON pc.product_id    = p.id
             JOIN Brands b              ON b.id              = p.brand_id
             WHERE pc.collection_id = ?
             ORDER BY p.name',
            [$collectionId]
        );
    }

    // ── CREATE ───────────────────────────────────────────────────────────────

    public function create(
        string  $name,
        string  $description,
        ?string $img,
        bool    $isActive,
        bool    $isLimited,
        ?string $releaseDate = null
    ): int {
        return $this->insert(
            'INSERT INTO Collections (name, description, img, is_active, is_limited, release_date)
             VALUES (?, ?, ?, ?, ?, ?)',
            [$name, $description, $img, (int) $isActive, (int) $isLimited, $releaseDate]
        );
    }

    // ── UPDATE ───────────────────────────────────────────────────────────────

    public function update(
        int     $id,
        string  $name,
        string  $description,
        ?string $img,
        bool    $isActive,
        bool    $isLimited
    ): int {
        return $this->execute(
            'UPDATE Collections
             SET name = ?, description = ?, img = ?, is_active = ?, is_limited = ?
             WHERE id = ?',
            [$name, $description, $img, (int) $isActive, (int) $isLimited, $id]
        );
    }

    // ── PIVOT : Product_collections ──────────────────────────────────────────

    public function addProduct(int $collectionId, int $productId): bool {
        return $this->execute(
            'INSERT IGNORE INTO Product_collections (product_id, collection_id) VALUES (?, ?)',
            [$productId, $collectionId]
        ) > 0;
    }

    public function removeProduct(int $collectionId, int $productId): int {
        return $this->execute(
            'DELETE FROM Product_collections WHERE product_id = ? AND collection_id = ?',
            [$productId, $collectionId]
        );
    }

    // ── DELETE ───────────────────────────────────────────────────────────────

    public function delete(int $id): int {
        return $this->execute('DELETE FROM Collections WHERE id = ?', [$id]);
    }
}
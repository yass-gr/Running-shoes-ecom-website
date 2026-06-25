<?php

require_once __DIR__ . '/Model.php';

class ProductVariant extends Model {

    // ── READ ────────────────────────────────────────────────────────────────

    public function findById(int $id): ?array {
        return $this->fetchOne(
            'SELECT pv.*,
                    pi.thumbnail, pi.top_view, pi.bottom_view, pi.side_view, pi.pair_view,
                    d.code AS discount_code, d.discount_type, d.value AS discount_value
             FROM Product_variants pv
             LEFT JOIN Product_img pi ON pi.id = pv.product_img_id
             LEFT JOIN Discounts   d  ON d.id  = pv.discount_id
             WHERE pv.id = ?',
            [$id]
        );
    }

    public function findByProduct(int $productId): array {
        return $this->fetchAll(
            'SELECT pv.*,
                    pi.thumbnail,
                    d.code AS discount_code, d.discount_type, d.value AS discount_value
             FROM Product_variants pv
             LEFT JOIN Product_img pi ON pi.id = pv.product_img_id
             LEFT JOIN Discounts   d  ON d.id  = pv.discount_id
             WHERE pv.product_id = ?
             ORDER BY pv.color, pv.size',
            [$productId]
        );
    }

    public function findBySku(string $sku): ?array {
        return $this->fetchOne(
            'SELECT * FROM Product_variants WHERE sku = ?',
            [$sku]
        );
    }

    /**
     * Variants that are at or below their reorder_level (low stock alert).
     */
    public function findLowStock(): array {
        return $this->fetchAll(
            'SELECT pv.*, p.name AS product_name
             FROM Product_variants pv
             JOIN Products p ON p.id = pv.product_id
             WHERE pv.stock_quantity <= pv.reorder_level
             ORDER BY pv.stock_quantity ASC'
        );
    }

    /**
     * Return all women's variants linked to a given men's variant.
     */
    public function findWomensVariant(int $womensVariantId): ?array {
        return $this->fetchOne(
            'SELECT * FROM Product_variants WHERE id = ?',
            [$womensVariantId]
        );
    }

    // ── CREATE ───────────────────────────────────────────────────────────────

    public function create(
        int     $productId,
        int     $size,
        string  $color,
        string  $sku,
        int     $stockQuantity,
        float   $variantPrice,
        int     $reorderLevel  = 5,
        ?int    $productImgId  = null,
        ?int    $discountId    = null,
        ?int    $womensVariantId = null
    ): int {
        return $this->insert(
            'INSERT INTO Product_variants
                 (product_id, womens_variant_id, size, color, sku,
                  stock_quantity, product_img_id, variant_price, reorder_level, discount_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $productId, $womensVariantId, $size, $color, $sku,
                $stockQuantity, $productImgId, $variantPrice, $reorderLevel, $discountId,
            ]
        );
    }

    // ── UPDATE ───────────────────────────────────────────────────────────────

    public function update(
        int     $id,
        int     $size,
        string  $color,
        string  $sku,
        int     $stockQuantity,
        float   $variantPrice,
        int     $reorderLevel,
        ?int    $productImgId,
        ?int    $discountId,
        ?int    $womensVariantId
    ): int {
        return $this->execute(
            'UPDATE Product_variants
             SET womens_variant_id = ?, size = ?, color = ?, sku = ?,
                 stock_quantity = ?, product_img_id = ?, variant_price = ?,
                 reorder_level = ?, discount_id = ?
             WHERE id = ?',
            [
                $womensVariantId, $size, $color, $sku,
                $stockQuantity, $productImgId, $variantPrice,
                $reorderLevel, $discountId, $id,
            ]
        );
    }

    /**
     * Reduce stock when an order is placed.
     * Returns false if not enough stock was available.
     */
    public function decrementStock(int $id, int $qty): bool {
        $affected = $this->execute(
            'UPDATE Product_variants
             SET stock_quantity = stock_quantity - ?
             WHERE id = ? AND stock_quantity >= ?',
            [$qty, $id, $qty]
        );
        return $affected > 0;
    }

    /**
     * Increase stock (used by inventory restock / refund).
     */
    public function incrementStock(int $id, int $qty): int {
        return $this->execute(
            'UPDATE Product_variants SET stock_quantity = stock_quantity + ? WHERE id = ?',
            [$qty, $id]
        );
    }

    // ── DELETE ───────────────────────────────────────────────────────────────

    public function delete(int $id): int {
        return $this->execute('DELETE FROM Product_variants WHERE id = ?', [$id]);
    }
}
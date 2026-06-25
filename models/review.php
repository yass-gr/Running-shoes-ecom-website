<?php

require_once __DIR__ . '/Model.php';

class Review extends Model {

    // ── READ ────────────────────────────────────────────────────────────────

    public function findByProduct(int $productId): array {
        return $this->fetchAll(
            'SELECT r.*, u.first_name, u.last_name
             FROM Reviews r
             JOIN Users u ON u.id = r.user_id
             WHERE r.product_id = ?
             ORDER BY r.created_at DESC',
            [$productId]
        );
    }

    public function findById(int $id): ?array {
        return $this->fetchOne(
            'SELECT r.*, u.first_name, u.last_name, p.name AS product_name
             FROM Reviews r
             JOIN Users u    ON u.id = r.user_id
             JOIN Products p ON p.id = r.product_id
             WHERE r.id = ?',
            [$id]
        );
    }

    public function findByUser(int $userId): array {
        return $this->fetchAll(
            'SELECT r.*, p.name AS product_name
             FROM Reviews r
             JOIN Products p ON p.id = r.product_id
             WHERE r.user_id = ?
             ORDER BY r.created_at DESC',
            [$userId]
        );
    }

    /**
     * Average rating and count for a product.
     */
    public function getStats(int $productId): array {
        return $this->fetchOne(
            'SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews
             FROM Reviews WHERE product_id = ?',
            [$productId]
        ) ?? ['avg_rating' => null, 'total_reviews' => 0];
    }

    /**
     * Check whether a user already reviewed a specific product.
     */
    public function userAlreadyReviewed(int $userId, int $productId): bool {
        $row = $this->fetchOne(
            'SELECT id FROM Reviews WHERE user_id = ? AND product_id = ?',
            [$userId, $productId]
        );
        return $row !== null;
    }

    // ── CREATE ───────────────────────────────────────────────────────────────

    public function create(
        int     $userId,
        int     $productId,
        int     $rating,          // 1-5
        string  $comment,
        bool    $verifiedPurchase = false
    ): int {
        return $this->insert(
            'INSERT INTO Reviews (user_id, product_id, rating, comment, verified_purchase)
             VALUES (?, ?, ?, ?, ?)',
            [$userId, $productId, $rating, $comment, (int) $verifiedPurchase]
        );
    }

    // ── UPDATE ───────────────────────────────────────────────────────────────

    public function update(int $id, int $rating, string $comment): int {
        return $this->execute(
            'UPDATE Reviews SET rating = ?, comment = ? WHERE id = ?',
            [$rating, $comment, $id]
        );
    }

    // ── DELETE ───────────────────────────────────────────────────────────────

    public function delete(int $id): int {
        return $this->execute('DELETE FROM Reviews WHERE id = ?', [$id]);
    }
}
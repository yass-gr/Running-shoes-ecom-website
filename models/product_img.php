<?php

require_once __DIR__ . '/Model.php';

class ProductImg extends Model {

    // ── READ ────────────────────────────────────────────────────────────────

    public function findById(int $id): ?array {
        return $this->fetchOne(
            'SELECT * FROM Product_img WHERE id = ?',
            [$id]
        );
    }

    // ── CREATE ───────────────────────────────────────────────────────────────

    /**
     * @param string      $thumbnail  Required thumbnail URL.
     * @param string|null $topView
     * @param string|null $bottomView
     * @param string|null $sideView
     * @param string|null $pairView
     */
    public function create(
        string  $thumbnail,
        ?string $topView    = null,
        ?string $bottomView = null,
        ?string $sideView   = null,
        ?string $pairView   = null
    ): int {
        return $this->insert(
            'INSERT INTO Product_img (thumbnail, top_view, bottom_view, side_view, pair_view)
             VALUES (?, ?, ?, ?, ?)',
            [$thumbnail, $topView, $bottomView, $sideView, $pairView]
        );
    }

    // ── UPDATE ───────────────────────────────────────────────────────────────

    public function update(
        int     $id,
        string  $thumbnail,
        ?string $topView    = null,
        ?string $bottomView = null,
        ?string $sideView   = null,
        ?string $pairView   = null
    ): int {
        return $this->execute(
            'UPDATE Product_img
             SET thumbnail = ?, top_view = ?, bottom_view = ?, side_view = ?, pair_view = ?
             WHERE id = ?',
            [$thumbnail, $topView, $bottomView, $sideView, $pairView, $id]
        );
    }

    // ── DELETE ───────────────────────────────────────────────────────────────

    public function delete(int $id): int {
        return $this->execute('DELETE FROM Product_img WHERE id = ?', [$id]);
    }
}
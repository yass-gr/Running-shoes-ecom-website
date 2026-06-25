<?php

require_once __DIR__ . '/Model.php';

class AuditLog extends Model {

    // ── READ ────────────────────────────────────────────────────────────────

    public function findAll(int $limit = 100): array {
        return $this->fetchAll(
            'SELECT al.*, CONCAT(u.first_name, " ", u.last_name) AS admin_name
             FROM Audit_logs al
             JOIN Users u ON u.id = al.admin_id
             ORDER BY al.created_at DESC
             LIMIT ?',
            [$limit]
        );
    }

    public function findByAdmin(int $adminId): array {
        return $this->fetchAll(
            'SELECT * FROM Audit_logs
             WHERE admin_id = ?
             ORDER BY created_at DESC',
            [$adminId]
        );
    }

    public function findByTable(string $targetTable): array {
        return $this->fetchAll(
            'SELECT al.*, CONCAT(u.first_name, " ", u.last_name) AS admin_name
             FROM Audit_logs al
             JOIN Users u ON u.id = al.admin_id
             WHERE al.target_table = ?
             ORDER BY al.created_at DESC',
            [$targetTable]
        );
    }

    public function findByTarget(string $targetTable, string $targetId): array {
        return $this->fetchAll(
            'SELECT al.*, CONCAT(u.first_name, " ", u.last_name) AS admin_name
             FROM Audit_logs al
             JOIN Users u ON u.id = al.admin_id
             WHERE al.target_table = ? AND al.target_id = ?
             ORDER BY al.created_at DESC',
            [$targetTable, $targetId]
        );
    }

    // ── CREATE ───────────────────────────────────────────────────────────────

    /**
     * Record any admin action. Call this after every sensitive operation.
     *
     * @param string $action      e.g. 'DELETE', 'UPDATE_PRICE', 'CANCEL_ORDER'
     * @param string $targetTable e.g. 'Products', 'Orders'
     * @param string $targetId    The affected row's primary key (stored as string)
     */
    public function log(
        int    $adminId,
        string $action,
        string $targetTable,
        string $targetId
    ): int {
        return $this->insert(
            'INSERT INTO Audit_logs (admin_id, action_performed, target_table, target_id)
             VALUES (?, ?, ?, ?)',
            [$adminId, $action, $targetTable, $targetId]
        );
    }

    // Audit logs are append-only; no update or delete methods.
}
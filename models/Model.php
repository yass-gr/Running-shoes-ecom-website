<?php

abstract class Model {
    protected PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Run a SELECT and return all rows.
     */
    protected function fetchAll(string $sql, array $params = []): array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Run a SELECT and return a single row, or null.
     */
    protected function fetchOne(string $sql, array $params = []): ?array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    /**
     * Run an INSERT/UPDATE/DELETE and return affected rows.
     */
    protected function execute(string $sql, array $params = []): int {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Run INSERT and return the last inserted ID.
     */
    protected function insert(string $sql, array $params = []): int {
        $this->execute($sql, $params);
        return (int) $this->db->lastInsertId();
    }
}
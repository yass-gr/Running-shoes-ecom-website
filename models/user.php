<?php

require_once __DIR__ . '/Model.php';

class User extends Model {

    // ── READ ────────────────────────────────────────────────────────────────

    public function findAll(): array {
        return $this->fetchAll(
            'SELECT u.id, u.first_name, u.last_name, u.email, u.role, u.created_at,
                    c.name AS city_name
             FROM Users u
             LEFT JOIN Cities c ON c.id = u.city_id
             ORDER BY u.last_name, u.first_name'
        );
    }

    public function findById(int $id): ?array {
        return $this->fetchOne(
            'SELECT u.id, u.first_name, u.last_name, u.email, u.role, u.created_at,
                    c.name AS city_name
             FROM Users u
             LEFT JOIN Cities c ON c.id = u.city_id
             WHERE u.id = ?',
            [$id]
        );
    }

    public function findByEmail(string $email): ?array {
        // Includes password hash — used only for auth, never exposed to views.
        return $this->fetchOne(
            'SELECT * FROM Users WHERE email = ?',
            [$email]
        );
    }

    public function findByRole(string $role): array {
        return $this->fetchAll(
            'SELECT u.id, u.first_name, u.last_name, u.email, u.role, u.created_at,
                    c.name AS city_name
             FROM Users u
             LEFT JOIN Cities c ON c.id = u.city_id
             WHERE u.role = ?
             ORDER BY u.last_name',
            [$role]
        );
    }

    // ── CREATE ───────────────────────────────────────────────────────────────

    /**
     * @param string $password  Must be a bcrypt hash — hash BEFORE calling this.
     */
    public function create(
        string  $firstName,
        string  $lastName,
        string  $email,
        string  $password,   // bcrypt hash
        string  $role = 'user',
        ?int    $cityId = null
    ): int {
        return $this->insert(
            'INSERT INTO Users (first_name, last_name, email, password, role, city_id)
             VALUES (?, ?, ?, ?, ?, ?)',
            [$firstName, $lastName, $email, $password, $role, $cityId]
        );
    }

    // ── UPDATE ───────────────────────────────────────────────────────────────

    public function update(
        int     $id,
        string  $firstName,
        string  $lastName,
        string  $email,
        ?int    $cityId,
        string  $role
    ): int {
        return $this->execute(
            'UPDATE Users SET first_name = ?, last_name = ?, email = ?, city_id = ?, role = ?
             WHERE id = ?',
            [$firstName, $lastName, $email, $cityId, $role, $id]
        );
    }

    public function updatePassword(int $id, string $hashedPassword): int {
        return $this->execute(
            'UPDATE Users SET password = ? WHERE id = ?',
            [$hashedPassword, $id]
        );
    }

    // ── AUTH HELPER ──────────────────────────────────────────────────────────

    /**
     * Verify a plain-text password against the stored hash.
     */
    public function verifyPassword(string $plainPassword, string $hash): bool {
        return password_verify($plainPassword, $hash);
    }

    // ── DELETE ───────────────────────────────────────────────────────────────

    public function delete(int $id): int {
        return $this->execute('DELETE FROM Users WHERE id = ?', [$id]);
    }
}
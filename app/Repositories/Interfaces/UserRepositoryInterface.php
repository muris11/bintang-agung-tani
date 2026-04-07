<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    /**
     * Find user by ID
     */
    public function find(int $id): ?User;

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User;

    /**
     * Get all users (paginated)
     */
    public function paginate(int $perPage = 15, array $filters = []): mixed;

    /**
     * Create new user
     */
    public function create(array $data): User;

    /**
     * Update user
     */
    public function update(User $user, array $data): User;

    /**
     * Delete user
     */
    public function delete(User $user): bool;

    /**
     * Get users by role
     */
    public function getByRole(bool $isAdmin): Collection;

    /**
     * Count total users
     */
    public function count(array $filters = []): int;

    /**
     * Search users
     */
    public function search(string $query): Collection;
}

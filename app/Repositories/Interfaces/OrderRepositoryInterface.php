<?php

namespace App\Repositories\Interfaces;

use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    /**
     * Find order by ID
     */
    public function find(int $id): ?Order;

    /**
     * Find order by order number
     */
    public function findByOrderNumber(string $orderNumber): ?Order;

    /**
     * Get all orders for a user
     */
    public function getByUser(User $user, array $filters = []): Collection;

    /**
     * Get paginated orders for a user
     */
    public function paginateByUser(User $user, int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Get paginated orders for admin
     */
    public function paginateForAdmin(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Create new order
     */
    public function create(array $data): Order;

    /**
     * Update order
     */
    public function update(Order $order, array $data): Order;

    /**
     * Get orders by status
     */
    public function getByStatus(string $status, ?User $user = null): Collection;

    /**
     * Count orders by status for user
     */
    public function countByStatus(string $status, User $user): int;

    /**
     * Get recent orders
     */
    public function getRecent(?User $user = null, int $limit = 5): Collection;

    /**
     * Get order statistics for user dashboard
     */
    public function getUserStatistics(User $user): array;
}

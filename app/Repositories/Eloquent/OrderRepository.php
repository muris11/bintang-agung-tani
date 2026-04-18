<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Models\User;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class OrderRepository implements OrderRepositoryInterface
{
    public function find(int $id): ?Order
    {
        return Order::with(['items', 'user', 'statusHistories'])->find($id);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return Order::with(['items', 'user', 'statusHistories'])
            ->where('order_number', $orderNumber)
            ->first();
    }

    public function getByUser(User $user, array $filters = []): Collection
    {
        $query = Order::with(['items'])
            ->where('user_id', $user->id);

        $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function paginateByUser(User $user, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Order::with(['items.product'])
            ->where('user_id', $user->id);

        $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateForAdmin(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Order::with(['user', 'items']);

        $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(Order $order, array $data): Order
    {
        $order->update($data);

        return $order->fresh();
    }

    public function getByStatus(string $status, ?User $user = null): Collection
    {
        $query = Order::with(['items'])
            ->where('status', $status);

        if ($user) {
            $query->where('user_id', $user->id);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function countByStatus(string $status, User $user): int
    {
        return Order::where('user_id', $user->id)
            ->where('status', $status)
            ->count();
    }

    public function getRecent(?User $user = null, int $limit = 5): Collection
    {
        $query = Order::with(['items']);

        if ($user) {
            $query->where('user_id', $user->id);
        }

        return $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getUserStatistics(User $user): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        return [
            'pending_payment_count' => $this->countByStatus(Order::STATUS_PENDING, $user),
            'processing_count' => $this->countByStatus(Order::STATUS_PROCESSING, $user),
            'completed_count' => $this->countByStatus(Order::STATUS_COMPLETED, $user),
            'total_spent_this_month' => Order::where('user_id', $user->id)
                ->where('status', Order::STATUS_COMPLETED)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('total_amount'),
            'pending_payment_total' => Order::where('user_id', $user->id)
                ->where('status', Order::STATUS_PENDING)
                ->sum('total_amount'),
            'total_orders' => Order::where('user_id', $user->id)->count(),
        ];
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, array $filters): void
    {
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }
    }
}

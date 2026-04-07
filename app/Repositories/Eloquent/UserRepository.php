<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

final class UserRepository implements UserRepositoryInterface
{
    private const CACHE_TTL = 3600; // 1 hour

    public function find(int $id): ?User
    {
        return Cache::remember("user:{$id}", self::CACHE_TTL, function () use ($id) {
            return User::find($id);
        });
    }

    public function findByEmail(string $email): ?User
    {
        return Cache::remember("user:email:{$email}", self::CACHE_TTL, function () use ($email) {
            return User::where('email', $email)->first();
        });
    }

    public function paginate(int $perPage = 15, array $filters = []): mixed
    {
        $query = User::query();

        $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = User::create($data);
        
        $this->clearCache();
        
        return $user;
    }

    public function update(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        
        $this->clearUserCache($user);
        
        return $user->fresh();
    }

    public function delete(User $user): bool
    {
        $this->clearUserCache($user);
        
        return $user->delete();
    }

    public function getByRole(bool $isAdmin): Collection
    {
        return Cache::remember("users:role:" . ($isAdmin ? 'admin' : 'user'), self::CACHE_TTL, function () use ($isAdmin) {
            return User::where('is_admin', $isAdmin)->get();
        });
    }

    public function count(array $filters = []): int
    {
        $cacheKey = 'users:count:' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($filters) {
            $query = User::query();
            $this->applyFilters($query, $filters);
            return $query->count();
        });
    }

    public function search(string $query): Collection
    {
        return User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(20)
            ->get();
    }

    private function applyFilters($query, array $filters): void
    {
        if (isset($filters['is_admin'])) {
            $query->where('is_admin', $filters['is_admin']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
    }

    private function clearUserCache(User $user): void
    {
        Cache::forget("user:{$user->id}");
        Cache::forget("user:email:{$user->email}");
        Cache::forget('users:count:*');
    }

    private function clearCache(): void
    {
        Cache::forget('users:count:*');
    }
}

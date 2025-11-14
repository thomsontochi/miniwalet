<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TransactionRepository
{
    public function create(array $attributes): Transaction
    {
        return Transaction::create($attributes);
    }

    public function latestForUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Transaction::query()
            ->with([
                'sender:id,name',
                'receiver:id,name',
            ])
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function recentForUser(int $userId, int $limit = 10): Collection
    {
        return Transaction::query()
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}

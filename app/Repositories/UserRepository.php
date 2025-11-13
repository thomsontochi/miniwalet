<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class UserRepository
{
    public function lockForTransfer(int $senderId, int $receiverId): array
    {
        if ($senderId === $receiverId) {
            throw new InvalidArgumentException('Sender and receiver must be different users.');
        }

        $ids = [$senderId, $receiverId];
        sort($ids);

        /** @var Collection<int, User> $users */
        $users = User::query()
            ->whereIn('id', $ids)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        if ($users->count() !== 2) {
            throw new InvalidArgumentException('Unable to lock both users for transfer.');
        }

        return [
            $users->get($senderId),
            $users->get($receiverId),
        ];
    }

    public function save(User $user): User
    {
        $user->save();

        return $user;
    }
}

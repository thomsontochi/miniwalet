<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class TransferCompleted implements ShouldBroadcastNow
{
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Transaction $transaction)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('users.' . $this->transaction->sender_id),
            new PrivateChannel('users.' . $this->transaction->receiver_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'transfer.completed';
    }

    public function broadcastWith(): array
    {
        return [
            'data' => [
                'id' => $this->transaction->id,
                'sender_id' => $this->transaction->sender_id,
                'receiver_id' => $this->transaction->receiver_id,
                'amount' => $this->transaction->amount,
                'commission_fee' => $this->transaction->commission_fee,
                'status' => $this->transaction->status,
                'reference' => $this->transaction->reference,
                'created_at' => optional($this->transaction->created_at)->toIso8601String(),
            ],
        ];
    }
}

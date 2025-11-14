<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userId = $request->user()?->getKey();

        return [
            'id' => $this->id,
            'sender' => [
                'id' => $this->sender_id,
                'name' => $this->whenLoaded('sender', fn () => $this->sender?->name),
            ],
            'receiver' => [
                'id' => $this->receiver_id,
                'name' => $this->whenLoaded('receiver', fn () => $this->receiver?->name),
            ],
            'amount' => (string) $this->amount,
            'commission_fee' => (string) $this->commission_fee,
            'status' => $this->status,
            'reference' => $this->reference,
            'direction' => match (true) {
                $userId === $this->sender_id => 'outgoing',
                $userId === $this->receiver_id => 'incoming',
                default => 'external',
            },
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}

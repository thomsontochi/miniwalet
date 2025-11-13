<?php

namespace App\Services;

use App\Events\TransferCompleted;
use App\Exceptions\InsufficientFundsException;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use App\ValueObjects\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletTransferService
{
    public function __construct(
        protected UserRepository $users,
        protected TransactionRepository $transactions,
    ) {
    }

    public function transfer(User $sender, User $receiver, string $rawAmount): Transaction
    {
        $amount = $this->normalizeAmount($rawAmount);
        $commission = $amount->multiply('0.015');
        $totalDebit = $amount->add($commission);

        $transaction = DB::transaction(function () use ($sender, $receiver, $amount, $commission, $totalDebit) {
            [$lockedSender, $lockedReceiver] = $this->users->lockForTransfer(
                $sender->getKey(),
                $receiver->getKey()
            );

            $senderBalance = Money::fromString((string) $lockedSender->balance);

            if ($senderBalance->isLessThan($totalDebit)) {
                throw new InsufficientFundsException();
            }

            $lockedSender->balance = $senderBalance->subtract($totalDebit)->toString();
            $lockedReceiver->balance = Money::fromString((string) $lockedReceiver->balance)
                ->add($amount)
                ->toString();

            $this->users->save($lockedSender);
            $this->users->save($lockedReceiver);

            return $this->transactions->create([
                'sender_id' => $lockedSender->getKey(),
                'receiver_id' => $lockedReceiver->getKey(),
                'amount' => $amount->toString(),
                'commission_fee' => $commission->toString(),
                'status' => 'completed',
                'reference' => Str::uuid()->toString(),
                'meta' => [
                    'processed_at' => now()->toIso8601String(),
                ],
            ]);
        });

        DB::afterCommit(function () use ($transaction) {
            event(new TransferCompleted($transaction));
        });

        return $transaction;
    }

    protected function normalizeAmount(string $rawAmount): Money
    {
        return Money::fromString($rawAmount);
    }
}

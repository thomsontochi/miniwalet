<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InsufficientFundsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use App\Services\WalletTransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TransactionsController extends Controller
{
    public function __construct(
        protected TransactionRepository $transactions,
        protected UserRepository $users,
        protected WalletTransferService $walletTransferService,
    ) {
    }

    public function index(Request $request)
    {
        $user = $this->users->findById($request->user()->getKey());


        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        $paginator = $this->transactions->latestForUser($user->getKey(), $perPage);

        return TransactionResource::collection($paginator)
            ->additional([
                'meta' => [
                    'balance' => $user->balance,
                ],
            ]);
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $sender = $request->user();
        $receiver = $this->users->findById($request->integer('receiver_id'));

        try {
            $transaction = $this->walletTransferService->transfer(
                $sender,
                $receiver,
                $request->validatedAmount()
            );
        } catch (InsufficientFundsException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $freshSender = $this->users->findById($sender->getKey());

        return (new TransactionResource($transaction))
            ->additional([
                'meta' => [
                    'balance' => $freshSender->balance,
                ],
            ])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardStatsController extends Controller
{
    public function __construct(
        protected UserRepository $users,
        protected TransactionRepository $transactions,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $user = $this->users->findById($request->user()->getKey());

        $todayStart = Carbon::now()->startOfDay();
        $todayEnd = Carbon::now()->endOfDay();

        $transfersProcessed = $this->transactions->countForUserBetween(
            $user->getKey(),
            $todayStart,
            $todayEnd,
        );

        return response()->json([
            'data' => [
                'balance' => $user->balance,
                'transfers_processed_today' => $transfersProcessed,
            ],
        ]);
    }
}

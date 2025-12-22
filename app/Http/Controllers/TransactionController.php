<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    use ApiResponse;

    public function getUserTransactions(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required',
                'type' => 'required|in:user,organization'
            ]);

            $transactions = Transaction::where('user_id', $request->user_id)
                ->where('account_type', $request->type)
                ->orderBy('created_at', 'desc')
                ->paginate(15);


            if ($transactions->total() === 0) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($transactions);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}

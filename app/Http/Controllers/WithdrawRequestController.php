<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Models\Organization;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WithdrawRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawRequestController extends Controller
{

    use ApiResponse;

    public function index(Request $request)
    {
        $query = WithdrawRequest::query()->with('user');

        // Optional filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $withdrawRequests = $query->latest()->paginate(20);

        return response()->json($withdrawRequests);
    }

    // ✅ Withdraw from available balance
    public function withdraw(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|integer',
                'type' => 'required|in:user,organization',
                'amount'  => 'required|numeric|min:1',
                'bank_number'  => 'required|',
                'method'  => 'required|string', // e.g., bank, paypal, etc.
                'details' => 'nullable',
            ]);

            $user = $request->type == 'user' ?  User::findOrFail($request->user_id) : Organization::findOrFail($request->user_id);


            $wallet = $user->wallet;

            if (!$wallet || $wallet->available_balance < $request->amount) {
                return response()->json(['message' => 'Insufficient balance'], 422);
            }

            $data = [];
            DB::transaction(function () use ($wallet, $request, $user, &$data) {
                // Deduct from available balance
                $wallet->decrement('available_balance', $request->amount);

                // Create withdraw request first
                $withdrawRequest = WithdrawRequest::create([
                    'user_id' => $user->id,
                    'account_type' => $user->account_type,
                    'amount'  => $request->amount,
                    'bank_number' => $request->bank_number,
                    'status'  => 'pending',
                    'meta'    => [
                        'method'  => $request->method,
                        'details' => $request->details ?? [],
                    ],
                ]);

                // Then create transaction linked to the withdraw request
                $transaction =   Transaction::create([
                    'user_id'     => $user->id,
                    'account_type'     => $user->account_type,
                    'type'        => 'withdrawal',
                    'direction'   => 'out',
                    'amount'      => $request->amount,
                    'status'      => 'pending',
                    'note'        => 'Withdrawal request',
                    'source_type' => 'withdraw_requests',
                    'source_id'   => $withdrawRequest->id,
                    'created_at' => now()
                ]);

                $data = [
                    'transaction' =>  $transaction,
                    'withdrawRequest' => $withdrawRequest
                ];
            });

            return $this->successResponse($data, 200, 'Withdrawal request submitted successfully.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function show($id)
    {
        $withdrawRequest = WithdrawRequest::with('user')->findOrFail($id);

        return response()->json($withdrawRequest);
    }


    public function approve($id)
    {
        $withdraw = WithdrawRequest::findOrFail($id);

        if ($withdraw->status !== 'pending') {
            return response()->json(['message' => 'This request has already been processed.'], 422);
        }

        DB::transaction(function () use ($withdraw) {
            $withdraw->update(['status' => 'approved']);

            $transaction = Transaction::where('source_type', 'withdraw_requests')
                ->where('source_id', $withdraw->id)
                ->first();

            if ($transaction) {
                $transaction->update(['status' => 'completed']);
            }
        });

        return response()->json(['message' => 'Withdrawal request approved successfully.']);
    }


    public function reject(Request $request, $id)
    {
        $withdraw = WithdrawRequest::findOrFail($id);

        if ($withdraw->status !== 'pending') {
            return response()->json(['message' => 'This request has already been processed.'], 422);
        }

        DB::transaction(function () use ($withdraw, $request) {
            $withdraw->update([
                'status' => 'rejected',
                'note'   => $request->note,
            ]);

            // Return money to wallet
            $wallet = $withdraw->user->wallet;
            $wallet->increment('available_balance', $withdraw->amount);

            // Update related transaction
            $transaction = Transaction::where('source_type', 'withdraw_requests')
                ->where('source_id', $withdraw->id)
                ->first();

            if ($transaction) {
                $transaction->update(['status' => 'failed']);
            }
        });

        return response()->json(['message' => 'Withdrawal request rejected and amount returned.']);
    }
}

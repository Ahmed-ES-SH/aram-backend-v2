<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Models\Organization;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{

    use ApiResponse;

    // ✅ Show wallet info for a given user
    public function show(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required',
                'type' => 'required|in:user,organization'
            ]);

            $wallet = Wallet::where('user_id', $request->user_id)->where('account_type', $request->type)->first();

            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $request->user_id,
                    'account_type' => $request->type,
                    'available_balance' => 0,
                    'pending_balance' => 0,
                ]);
            }

            $wallet['total_balance'] = $wallet->available_balance + $wallet->pending_balance;

            return $this->successResponse($wallet, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    // ✅ Deposit (simulated) to available balance for given user
    public function deposit(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'type' => 'required|in:user,organization',
            'amount' => 'required|numeric|min:1'
        ]);

        $user = $request->type == 'user' ?  User::findOrFail($request->user_id) : Organization::findOrFail($request->user_id);
        $wallet = $user->wallet ?? $user->wallet()->create([
            'available_balance' => 0,
            'pending_balance' => 0,
        ]);

        DB::transaction(function () use ($wallet, $request, $user) {
            $wallet->increment('available_balance', $request->amount);

            Transaction::create([
                'user_id'    => $user->id,
                'account_type'    => $user->account_type,
                'type'       => 'deposit',
                'direction'  => 'in',
                'amount'     => $request->amount,
                'status'     => 'completed',
                'note'       => 'Simulated deposit',
                'meta'       => ['method' => 'manual'],
            ]);
        });

        return response()->json(['message' => 'Deposit successful']);
    }



    // ✅ Add pending balance to a user
    public function addPending(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'type' => 'required|in:user,organization',
            'amount' => 'required|numeric|min:1'
        ]);

        $user = $request->type == 'user' ?  User::findOrFail($request->user_id) : Organization::findOrFail($request->user_id);

        DB::transaction(function () use ($user, $request) {
            $wallet = $user->wallet ?? $user->wallet()->create([
                'available_balance' => 0,
                'pending_balance' => 0,
            ]);

            $wallet->increment('pending_balance', $request->amount);

            Transaction::create([
                'user_id'    => $user->id,
                'account_type'    => $user->account_type,
                'type'       => 'sale',
                'direction'  => 'in',
                'amount'     => $request->amount,
                'status'     => 'pending',
                'note'       => 'Service sale - pending earnings',
            ]);
        });

        return response()->json(['message' => 'Pending balance added']);
    }


    // ✅ Release pending balance into available
    public function releasePending(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'type' => 'required|in:user,organization',
            'amount' => 'required|numeric|min:1'
        ]);

        $user = $request->type == 'user' ?  User::findOrFail($request->user_id) : Organization::findOrFail($request->user_id);

        DB::transaction(function () use ($user, $request) {
            $wallet = $user->wallet;

            if (!$wallet || $wallet->pending_balance < $request->amount) {
                throw new \Exception('Not enough pending balance.');
            }

            $wallet->decrement('pending_balance', $request->amount);
            $wallet->increment('available_balance', $request->amount);

            Transaction::create([
                'user_id'    => $user->id,
                'account_type'    => $user->account_type,
                'type'       => 'transfer',
                'direction'  => 'in',
                'amount'     => $request->amount,
                'status'     => 'completed',
                'note'       => 'Released from pending to available',
            ]);
        });

        return response()->json(['message' => 'Pending balance released']);
    }
}

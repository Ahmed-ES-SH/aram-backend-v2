<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Models\Organization;
use App\Models\OwnedCard;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class OwnedCardController extends Controller
{
    use ApiResponse;

    public function getAccountCards(Request $request)
    {
        try {
            $request->validate([
                'owner_id' => 'required|integer',
                'owner_type' => 'required|in:user,organization',
            ]);

            // ✅ Load owner info properly
            $owner = $request->owner_type === 'user'
                ? User::select('id', 'name', 'image', 'email')->find($request->owner_id)
                : Organization::select('id', 'title', 'logo', 'email')->find($request->owner_id);

            if (! $owner) {
                return $this->errorResponse('Owner not found', 404);
            }

            // ✅ Load cards with pagination
            $cards = OwnedCard::where('owner_id', $request->owner_id)
                ->where('owner_type', $request->owner_type)->with('card:id,title,image')
                ->paginate(12);

            if ($cards->total() === 0) {
                return $this->noContentResponse();
            }

            $data = [
                'cards' => $cards->items(),
                'owner' => $owner
            ];

            // ✅ Include owner in the response payload
            return response()->json([
                'data' => $data,
                'pagination' => [
                    'current_page' => $cards->currentPage(),
                    'per_page' => $cards->perPage(),
                    'total' => $cards->total(),
                    'last_page' => $cards->lastPage(),
                ],
            ], 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        try {
            $request->validate([
                'query' => 'nullable|string',
            ]);

            $members = Member::query();

            if ($request->filled('query')) {
                $search = $request->input('query');
                $members->where('email', 'LIKE', "%{$search}%");
            }

            $members = $members->paginate(20);

            if ($members->isEmpty()) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($members, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }





    public function getMembersEmails()
    {
        try {
            $membersEmails = Member::pluck('email')->toArray();

            return $this->successResponse($membersEmails, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    /**
     * اشتراك في النشرة البريدية
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:members,email',
        ]);

        Member::create(['email' => $request->email]);

        return response()->json(['message' => 'Subscription successful!'], 201);
    }




    public function unsubscribe($id)
    {
        try {
            $member = Member::findOrFail($id);
            $member->delete();
            return $this->successResponse([],  200, 'Member Deleted Successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Jobs\SendNewsletterJob;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            $members = Member::orderBy('created_at', 'desc')->paginate(15);

            if ($members->isEmpty()) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($members, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getMembersByEmail($searchContnent)
    {
        try {
            $members = Member::where('email', 'LIKE', "%$searchContnent%")->paginate(25);

            if ($members->isEmpty()) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($members, 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed Error', ['message' => $e->getMessage()], 500);
        }
    }



    public function getMembersIds()
    {
        try {
            $membersIds = Member::pluck('id')->toArray();

            return response()->json([
                'data' => $membersIds
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed Error', ['message' => $e->getMessage()], 500);
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

    /**
     * إرسال النشرة البريدية
     */
    public function sendNewsletter(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'members_ids' => 'required|string', // تأكد من أن members_ids مصفوفة
        ]);


        $members_ids = is_string($request->members_ids) ?   json_decode($request->members_ids, true) : $request->members_ids;

        // جلب المشتركين
        $subscribers = Member::whereIn('id', $members_ids)->get();

        if ($subscribers->isEmpty()) {
            return response()->json(['message' => 'No subscribers found!'], 404);
        }

        // إضافة كل رسالة إلى الـ Queue بدلاً من إرسالها مباشرة
        foreach ($subscribers as $subscriber) {
            dispatch(new SendNewsletterJob($subscriber, $request->subject, $request->content));
        }

        return response()->json(['message' => 'Newsletter is being sent in the background.'], 200);
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

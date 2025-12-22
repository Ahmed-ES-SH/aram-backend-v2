<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFamilyMemberRequest;
use App\Http\Services\NotificationService;
use App\Http\Traits\ApiResponse;
use App\Models\FamilyMember;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FamilyMemberController extends Controller
{

    use ApiResponse;
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }


    /**
     * Display a list of accepted family members for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get accepted family relations where the authenticated user is the owner.
            $user = $request->user();

            $family = FamilyMember::with('member:id,name,image,birth_date,phone,gender', 'user:id,name,image,birth_date,phone,gender')
                ->where('user_id', $user->id)
                ->orWhere('family_member_id', $user->id)
                ->get();

            return $this->successResponse($family, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display all pending family requests directed to the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function pendingRequests(Request $request)
    {
        try {
            $user = $request->user();


            // Requests where the authenticated user is the recipient (family_member_id) and status is pending.
            $requests = FamilyMember::with('member:id,name,image,birth_date,phone,gender') // include the requester
                ->where('family_member_id', $user->id)
                ->where('status', 'pending')
                ->get();


            if ($requests->isEmpty()) {
                return $this->noContentResponse();
            }

            return $this->successResponse($requests, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Store a new family request (send invitation).
     *
     * Validation is handled by StoreFamilyMemberRequest.
     *
     * @param  \App\Http\Requests\StoreFamilyMemberRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreFamilyMemberRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $targetId = (int) $request->input('family_member_id');

            // Prevent adding self explicitly (extra safeguard)
            if ($targetId === $user->id) {
                return response()->json([
                    'message' => [
                        'en' => 'You cannot add yourself as a family member.',
                        'ar' => 'لا يمكنك إضافة نفسك كأحد أفراد العائلة.',
                    ],
                ], 422);
            }

            // Ensure target user exists
            $target = User::find($targetId);
            if (!$target) {
                return response()->json([
                    'message' => [
                        'en' => 'The specified user was not found.',
                        'ar' => 'المستخدم المحدد غير موجود.',
                    ],
                ], 404);
            }

            // Prevent duplicate requests or existing accepted relation (in either direction)
            $existing = FamilyMember::where(function ($q) use ($user, $targetId) {
                $q->where('user_id', $user->id)
                    ->where('family_member_id', $targetId);
            })
                ->orWhere(function ($q) use ($user, $targetId) {
                    $q->where('user_id', $targetId)
                        ->where('family_member_id', $user->id);
                })
                ->whereIn('status', ['pending', 'accepted'])
                ->first();

            if ($existing) {
                $msg = $existing->status === 'accepted'
                    ? [
                        'en' => 'A family relation already exists between you and this user.',
                        'ar' => 'علاقة عائلية موجودة بالفعل بينك وبين هذا المستخدم.',
                    ]
                    : [
                        'en' => 'A family request is already pending between you and this user.',
                        'ar' => 'يوجد بالفعل طلب عائلي قيد الانتظار بينك وبين هذا المستخدم.',
                    ];

                return response()->json([
                    'message' => $msg,
                ], 422);
            }

            // ✅ Wrap all actions in a database transaction
            $family = DB::transaction(function () use ($user, $targetId, $request) {
                // Create pending family request
                $family = FamilyMember::create([
                    'user_id' => $user->id,
                    'family_member_id' => $targetId,
                    'relationship' => $request->input('relationship'),
                    'status' => 'pending',
                ]);

                // Prepare and send notification
                $sender = User::find($user->id);

                $notificationData = [
                    'content' => sprintf(
                        '%s لقد أرسل لك طلب تسجيل عائلي للانضمام إلى "%s".',
                        $sender->name,
                        $request->input('relationship') ?? 'عضو في السجل العائلي'
                    ),
                    'recipient_id' => $targetId,
                    'recipient_type' => 'user',
                    'sender_id' => $user->id,
                    'sender_type' => 'user',
                ];

                $this->notificationService->sendNotification($notificationData, $sender);

                return $family;
            });

            $family->load([
                'user:id,name,image,birth_date,phone,gender',
                'member:id,name,image,birth_date,phone,gender',
            ]);

            return $this->successResponse($family, 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => [
                    'en' => 'An unexpected error occurred. Please try again later.',
                    'ar' => 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى لاحقًا.',
                ],
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Accept a pending family request.
     *
     * $id refers to the primary key in family_members table (the request record).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function accept(int $id, Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Fetch the pending request where the authenticated user is the recipient.
            $requestRecord = FamilyMember::where('id', $id)
                ->where('family_member_id', $user->id)
                ->where('status', 'pending')
                ->first();

            if (!$requestRecord) {
                return response()->json([
                    'message' => 'Pending family request not found or you are not authorized to accept it.',
                ], 404);
            }

            DB::transaction(function () use ($requestRecord, $user) {

                // Update the original request to accepted.
                $requestRecord->status = 'accepted';
                $requestRecord->save();

                // Create reciprocal accepted relation if it doesn't exist.
                $reciprocalExists = FamilyMember::where('user_id', $user->id)
                    ->where('family_member_id', $requestRecord->user_id)
                    ->exists();

                if (! $reciprocalExists) {
                    FamilyMember::create([
                        'user_id' => $user->id,
                        'family_member_id' => $requestRecord->user_id,
                        'relationship' => $requestRecord->relationship,
                        'status' => 'accepted',
                    ]);
                } else {
                    FamilyMember::where('user_id', $user->id)
                        ->where('family_member_id', $requestRecord->user_id)
                        ->update(['status' => 'accepted']);
                }

                // 🔔 Send acceptance notification to the original sender
                $sender = User::find($user->id);
                $recipientId = $requestRecord->user_id;

                $notificationData = [
                    'content' => sprintf(
                        'تم قبول طلبك للانضمام إلى السجل العائلي من قبل %s.',
                        $sender->name
                    ),
                    'recipient_id' => $recipientId,
                    'recipient_type' => 'user',
                    'sender_id' => $user->id,
                    'sender_type' => 'user',
                ];

                $this->notificationService->sendNotification($notificationData, $sender);
            });

            return $this->successResponse([], 200, "Family request accepted successfully.");
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    /**
     * Reject a pending family request.
     *
     * $id refers to the primary key in family_members table (the request record).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reject(int $id, Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Find the pending request where the authenticated user is the recipient.
            $requestRecord = FamilyMember::where('id', $id)
                ->where('family_member_id', $user->id)
                ->where('status', 'pending')
                ->first();

            if (! $requestRecord) {
                return response()->json([
                    'message' => 'لم يتم العثور على طلب عائلي معلق أو أنك غير مخول لرفضه.',
                ], 404);
            }

            DB::transaction(function () use ($requestRecord, $user) {

                // تحديث الحالة إلى مرفوض
                $requestRecord->status = 'rejected';
                $requestRecord->save();

                // 🔔 إرسال إشعار للطرف المرسل الأصلي
                $sender = User::find($user->id);
                $recipientId = $requestRecord->user_id;

                $notificationData = [
                    'content' => sprintf(
                        'قام %s برفض دعوتك للانضمام إلى السجل العائلي.',
                        $sender->name
                    ),
                    'recipient_id' => $recipientId,
                    'recipient_type' => 'user',
                    'sender_id' => $user->id,
                    'sender_type' => 'user',
                ];

                $this->notificationService->sendNotification($notificationData, $sender);
            });

            return $this->successResponse([], 200, "تم رفض الدعوة بنجاح.");
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    /**
     * Remove a family relation or cancel a pending request.
     *
     * $id refers to the primary key in family_members table.
     * Either party (owner or recipient) can delete the relation/request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Find the record where the authenticated user is either owner or recipient.
            $record = FamilyMember::where('id', $id)
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhere('family_member_id', $user->id);
                })
                ->first();

            if (! $record) {
                return response()->json([
                    'message' => 'Family relation not found or you are not authorized to delete it.',
                ], 404);
            }

            // Delete reciprocal accepted record if exists (to keep symmetry)
            if ($record->status === 'accepted') {
                // Determine counterpart owner/recipient
                $owner = $record->user_id;
                $member = $record->family_member_id;

                FamilyMember::where('user_id', $member)
                    ->where('family_member_id', $owner)
                    ->delete();
            }

            $record->delete();

            return $this->successResponse([[], 200, "Family relation/request deleted successfully."]);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}

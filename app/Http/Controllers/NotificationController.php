<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendNotificationRequest;
use App\Http\Traits\ApiResponse;
use App\Http\Services\NotificationService;
use App\Models\Notification;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function sendNotification(SendNotificationRequest $request)
    {
        $data = $request->validated();
        $sender = $data['sender_type'] == 'user' ? User::where('id', $data['sender_id'])->select('id', 'name', 'email', 'image', 'account_type')->first()
            : Organization::where('id', $data['sender_id'])->select('id', 'title as name', 'email', 'logo as image', 'account_type')->first();
        $result = $this->notificationService->sendNotification($data, $sender);

        if (!$result['success']) {
            return $this->errorResponse([
                "ar" => "فشل إرسال الإشعار. السبب: " . $result['message'],
                "en" => "Notification sending failed. Reason: " . $result['message']
            ], 500);
        }

        return $this->successResponse($result['notification'], 200);
    }


    public function sendMultipleNotification(Request $request)
    {
        $data = $request->validate([
            'user_ids' => 'required', // تأكد من أنها مصفوفة
            'user_ids.*' => 'exists:users,id',
            'content' => 'required|string',
            'sender_id' => 'required',
            'sender_type' => 'required|in:user,organization',
            'recipient_type' => 'required|in:user,organization',
        ]);

        $sender_id = $data['sender_id'];
        $recipient_type = $data['recipient_type'];

        // تحديد المرسل
        $sender = $data['sender_type'] == 'user'
            ? User::where('id', $sender_id)->select('id', 'name', 'email', 'image', 'account_type')->first()
            : Organization::where('id', $sender_id)->select('id', 'title as name', 'email', 'logo as image', 'account_type')->first();

        // استدعاء دالة الخدمة للإشعارات المتعددة
        $result = $this->notificationService->sendMultipleNotifications($data, $sender, $recipient_type);

        if (!$result['success']) {
            return $this->errorResponse([
                "ar" => "فشل إرسال الإشعارات. السبب: " . $result['message'],
                "en" => "Notifications sending failed. Reason: " . $result['message']
            ], 500);
        }

        return $this->successResponse($result['notifications'], 200);
    }

    public function getNotificationsForAccount($id, $type = 'user')
    {
        try {
            $notifications = Notification::where('recipient_id', $id)
                ->where('recipient_type', $type)
                ->orderBy('created_at', 'desc')
                ->paginate(30);

            $notifications->getCollection()->transform(function ($notification) {
                // لو المرسل User
                if ($notification->sender_type === 'user') {
                    $notification->load([
                        'sender' => function ($query) {
                            $query->select('id', 'name', 'email', 'image');
                        }
                    ]);
                }

                // لو المرسل Organization
                if ($notification->sender_type === 'organization') {
                    $notification->load([
                        'sender' => function ($query) {
                            $query->select('id', 'title as name', 'logo as image', 'email');
                        }
                    ]);
                }

                // نفس الفكرة للـ recipient
                if ($notification->recipient_type === 'user') {
                    $notification->load([
                        'recipient' => function ($query) {
                            $query->select('id', 'name', 'email', 'image');
                        }
                    ]);
                }

                if ($notification->recipient_type === 'organization') {
                    $notification->load([
                        'recipient' => function ($query) {
                            $query->select('id', 'title as name', 'logo as image', 'email');
                        }
                    ]);
                }

                return $notification;
            });

            return $this->paginationResponse($notifications, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function makeAllNotificationsAsRead($id)
    {
        try {
            // Update all unread notifications for the recipient
            $updatedCount = Notification::where('recipient_id', $id)
                ->where('is_read', 0) // assuming is_read is TINYINT(0/1)
                ->update(['is_read' => 1]);

            if ($updatedCount === 0) {
                return $this->errorResponse([
                    "ar" => "لا توجد إشعارات غير مقروءة.",
                    "en" => "No unread notifications found."
                ], 404);
            }

            return $this->successResponse([
                "ar" => "تم تحديث جميع الإشعارات إلى مقروءة.",
                "en" => "All notifications have been marked as read."
            ], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function getLastTenNotifications($id, $type = 'user')
    {
        try {
            $notifications = Notification::where('recipient_id', $id)
                ->where('recipient_type', $type)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            if ($notifications->isEmpty()) {
                return $this->noContentResponse();
            }

            $notifications->transform(function ($notification) {
                // Sender
                if ($notification->sender_type === 'user') {
                    $notification->load([
                        'sender' => function ($query) {
                            $query->select('id', 'name', 'email', 'image');
                        }
                    ]);
                }

                if ($notification->sender_type == 'organization') {
                    $notification->load([
                        'sender' => function ($query) {
                            $query->select('id', 'title as name', 'logo as image', 'email');
                        }
                    ]);
                }

                // Recipient
                if ($notification->recipient_type === 'user') {
                    $notification->load([
                        'recipient' => function ($query) {
                            $query->select('id', 'name', 'email', 'image');
                        }
                    ]);
                }

                if ($notification->recipient_type == 'organization') {
                    $notification->load([
                        'recipient' => function ($query) {
                            $query->select('id', 'title as name', 'logo as image', 'email');
                        }
                    ]);
                }

                return $notification;
            });


            if ($notifications->isEmpty()) {
                return $this->noContentResponse();
            }

            return $this->successResponse($notifications, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}

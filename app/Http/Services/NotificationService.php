<?php

namespace App\Http\Services;

use Pusher\Pusher;
use App\Models\Notification;

class NotificationService
{
    protected $pusher;

    public function __construct()
    {
        $this->pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            [
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'useTLS' => config('broadcasting.connections.pusher.options.useTLS'),
            ]
        );
    }

    /**
     * إرسال إشعار للمستخدم عبر Pusher
     */
    public function sendNotification(array $data, $sender)
    {
        try {
            // حفظ الإشعار في قاعدة البيانات
            $notification = Notification::create($data);

            // إرسال الإشعار عبر Pusher
            $this->pusher->trigger(
                'notifications.' . $data['recipient_type'] . '.' . $data['recipient_id'],
                'NotificationSent',
                [
                    'content' => $data['content'],
                    'recipient_id' => $data['recipient_id'],
                    'sender_id' => $data['sender_id'],
                    'recipient_type' => $data['recipient_type'],
                    'sender_type' => $data['sender_type'],
                    'sender' => $sender,
                    'created_at' => now(),
                    'is_read' => 0
                ]
            );



            return ['success' => true, 'notification' => $notification];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    public function sendMultipleNotifications(array $data, $sender, $type = 'user')
    {
        try {
            $userIds = is_array($data['user_ids'])
                ? $data['user_ids']
                : json_decode($data['user_ids']);

            $createdNotifications = [];

            foreach ($userIds as $userId) {

                // تجهيز بيانات الإشعار بنفس شكل sendNotification
                $notificationData = [
                    'recipient_id' => $userId,
                    'recipient_type' => $type,
                    'content' => $data['content'],
                    'sender_id' => $sender->id,
                    'sender_type' => $sender->account_type ?? $data['sender_type'],
                    'is_read' => 0,
                ];

                // 1️⃣ إنشاء الإشعار في قاعدة البيانات -> Model حقيقي
                $notification = Notification::create($notificationData);
                $createdNotifications[] = $notification;

                // 2️⃣ إطلاق الإشعار عبر Pusher بنفس القناة
                $this->pusher->trigger(
                    'notifications.' . $type . '.' . $userId,
                    'NotificationSent',
                    [
                        'content' => $notification->content,
                        'recipient_id' => $notification->recipient_id,
                        'recipient_type' => $notification->recipient_type,
                        'sender_id' => $notification->sender_id,
                        'sender_type' => $notification->sender_type,
                        'sender' => $sender, // نفس sendNotification
                        'created_at' => $notification->created_at,
                        'is_read' => 0
                    ]
                );
            }

            return [
                'success' => true,
                'message' => 'Notifications sent successfully.',
                'notifications' => $createdNotifications
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

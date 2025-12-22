<?php

namespace App\Http\Services;

use App\Http\Services\ImageService;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Arr;
use Pusher\Pusher;
use Illuminate\Support\Facades\Cache;

class ChatService
{
    protected $pusher;
    protected $imageService;

    public function __construct(ImageService $imageService)
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

        $this->imageService = $imageService;
    }

    /**
     * إرسال رسالة في المحادثة عبر Pusher
     */
    public function sendMessage(array $data)
    {
        try {
            // ✅ حفظ الرسالة
            $message = Message::create(Arr::except($data, ['attachment']));

            // ✅ معالجة المرفقات إن وجدت
            if (isset($data['attachment'])) {
                $this->imageService->uploadChatAttachment($data['attachment'], $message);
            }

            // ✅ تحديد المستقبل بناءً على المحادثة
            $conversation = Conversation::findOrFail($message->conversation_id);
            $receiverId = $conversation->participant_one_id == $message->sender_id
                ? $conversation->participant_two_id
                : $conversation->participant_one_id;

            // ✅ إرسال الرسالة عبر Pusher
            $this->pusher->trigger(
                'conversation.' . $message->conversation_id,
                'MessageSent',
                [
                    'id' => $message->id,
                    'conversation_id' => $message->conversation_id,
                    'sender_id' => $message->sender_id,
                    'sender_type' => $message->sender_type,
                    'receiver_id' => $receiverId,
                    'message' => $message->message,
                    'message_type' => $message->message_type,
                    'is_read' => false,
                    'attachment' => $message->attachment ?? null,
                    'created_at' => $message->created_at,
                ]
            );

            // ✅ تحقق من المحادثة النشطة للمستقبل
            $activeConversation = Cache::get("user:{$receiverId}:active_conversation");

            if ($activeConversation != $message->conversation_id) {
                // ✅ حساب عدد الرسائل غير المقروءة للمستقبل
                $unreadCount = Message::where('conversation_id', $message->conversation_id)
                    ->where('sender_id', '!=', $receiverId)
                    ->where('is_read', false)
                    ->count();

                // ✅ إرسال إشعار للمستقبل لتحديث العداد
                $this->pusher->trigger(
                    'user.' . $receiverId,
                    'UnreadMessageUpdated',
                    [
                        'conversation_id' => $message->conversation_id,
                        'unread_count' => $unreadCount,
                        'message' => [
                            'id' => $message->id,
                            'conversation_id' => $message->conversation_id,
                            'sender_id' => $message->sender_id,
                            'sender_type' => $message->sender_type,
                            'message' => $message->message,
                            'is_read' => false,
                            'attachment' => $message->attachment ?? null,
                            'created_at' => $message->created_at->toDateTimeString(),
                        ]
                    ]
                );
            }

            return ['success' => true, 'message' => $message];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

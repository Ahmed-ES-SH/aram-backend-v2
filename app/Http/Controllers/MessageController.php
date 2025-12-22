<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Services\ChatService;
use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{


    use ApiResponse;
    protected $chatService;
    protected $imageservice;

    public function __construct(ChatService $chatService, ImageService $imageService)
    {
        $this->chatService = $chatService;
        $this->imageservice = $imageService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMessageRequest $request)
    {
        try {
            $data = $request->validated();

            // تأكد من إضافة نوع الحساب من المستخدم الحالي
            $data['sender_id'] = $request->user()->id;
            $data['sender_type'] = $request->user()->account_type;

            $response = $this->chatService->sendMessage($data);

            if (!$response['success']) {
                return response()->json(['error' => $response['error']], 500);
            }

            return response()->json([
                'message' => 'Message sent successfully',
                'data' => $response['message']
            ], 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {

            $request->validate([
                'message_id' => 'required|numeric|exists:messages,id'
            ]);

            $messageId = $request->message_id;

            $message = Message::findOrFail($messageId);

            if (isset($message->attachment)) {
                $this->imageservice->deleteChatAttachment($message);
            }

            $message->delete();

            return response()->json(['message' => 'Message deleted successfully', 200]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function markAsRead(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required|numeric|exists:conversations,id',
            ]);

            $userId = $request->user()->id;
            $conversationId = $request->conversation_id;

            // التأكد أن المستخدم مشارك في المحادثة
            $conversation = Conversation::where('id', $conversationId)
                ->where(function ($query) use ($userId) {
                    $query->where('participant_one_id', $userId)
                        ->orWhere('participant_two_id', $userId);
                })
                ->first();

            if (!$conversation) {
                return $this->errorResponse("You are not a participant in this conversation.", 403);
            }

            // الطرف الآخر في المحادثة
            $otherParticipantId = $conversation->participant_one_id == $userId
                ? $conversation->participant_two_id
                : $conversation->participant_one_id;

            // جلب الرسائل الغير مقروءة من الطرف الآخر
            $messages = Message::where('conversation_id', $conversationId)
                ->where('sender_id', $otherParticipantId) // المرسل هو الطرف الآخر
                ->where('is_read', false);

            if (!$messages->exists()) {
                return response()->json([
                    'message' => "No unread messages found."
                ], 200);
            }

            // تحديث جميع الرسائل دفعة واحدة
            $updatedCount = $messages->update(['is_read' => true]);

            return response()->json([
                'message' => 'All unread messages marked as read.',
                'updated_count' => $updatedCount
            ], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}

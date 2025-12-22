<?php

namespace App\Http\Controllers;

use App\Http\Requests\Conversation\BlockUserRequest;
use App\Http\Requests\Conversation\GetConversationRequest;
use App\Http\Requests\Conversation\SetActiveConversationRequest;
use App\Http\Requests\Conversation\StoreConversationRequest;
use App\Http\Services\ConversationService;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    use ApiResponse;

    protected $conversationService;

    /**
     * =========================================================================
     * Constructor: Dependency Injection for ConversationService
     * =========================================================================
     * Injects the ConversationService to handle business logic for conversations.
     */
    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }

    /**
     * =========================================================================
     * Store Conversation: Create or Retrieve Existing Conversation
     * =========================================================================
     * Creates a new conversation between participants or returns an existing one
     * if it already exists. Uses validated request data.
     */
    public function StoreConversation(StoreConversationRequest $request)
    {
        try {
            $result = $this->conversationService->store($request->validated());
            $message = $result['created'] ? 'Conversation created successfully.' : 'Conversation already exists.';
            return $this->successResponse($result['conversation'], 201, $message);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * =========================================================================
     * Get Conversation: Retrieve Specific Conversation Details
     * =========================================================================
     * Fetches detailed information about a specific conversation, including
     * messages and participants, based on the provided conversation ID and
     * participant details.
     */
    public function getConversation(GetConversationRequest $request)
    {
        try {
            $data = $this->conversationService->getConversation(
                $request->conversation_id,
                $request->participant_id,
                $request->participant_type
            );
            return $this->successResponse($data, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * =========================================================================
     * Get User Conversations: List All Conversations for a Participant
     * =========================================================================
     * Retrieves all conversations for a specific participant. If participant
     * details are not provided in the request, it falls back to the
     * authenticated user's information.
     */
    public function getUserConversations(Request $request)
    {
        try {
            $participantId = $request->input('participant_id');
            $participantTypeShort = $request->input('participant_type');

            // Fallback to authenticated user if participant details are not provided
            if (!$participantId || !$participantTypeShort) {
                $user = $request->user();
                if (!$user) {
                    return $this->errorResponse('Participant not provided and user not authenticated', 422);
                }
                $participantId = $participantId ?? $user->id;
                $participantTypeShort = $participantTypeShort ?? ($user->account_type ?? 'user');
            }

            $conversations = $this->conversationService->getUserConversations($participantId, $participantTypeShort);

            if ($conversations->isEmpty()) {
                return $this->noContentResponse();
            }

            return $this->successResponse($conversations, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * =========================================================================
     * Set Active Conversation: Mark Conversation as Active for User
     * =========================================================================
     * Marks a specific conversation as the active conversation for the
     * authenticated user. This is useful for tracking the user's current
     * chat context.
     */
    public function setActiveConversation(SetActiveConversationRequest $request)
    {
        $user = $request->user();
        $this->conversationService->setActive($user->id, $user->account_type, $request->conversation_id);

        return response()->json([
            'message' => 'Conversation marked as active',
            'participant' => [
                'id' => $user->id,
                'type' => $user->account_type,
            ],
            'conversation_id' => $request->conversation_id
        ]);
    }

    /**
     * =========================================================================
     * Clear Active Conversation: Remove Active Conversation Status
     * =========================================================================
     * Clears the active conversation setting for the authenticated user,
     * effectively resetting their current chat context.
     */
    public function clearActiveConversation(Request $request)
    {
        $user = $request->user();
        $this->conversationService->clearActive($user->id, $user->account_type);

        return response()->json([
            'message' => 'Active conversation cleared',
            'participant' => [
                'id' => $user->id,
                'type' => $user->account_type,
            ]
        ]);
    }

    /**
     * =========================================================================
     * Block User: Block a User within a Conversation
     * =========================================================================
     * Blocks a specific user within a conversation, preventing them from
     * sending messages. Requires authentication and conversation context.
     */
    public function blockUser(BlockUserRequest $request)
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                return $this->errorResponse('User not authenticated', 401);
            }

            $this->conversationService->blockUser($userId, $request->conversation_id, $request->blocked_user);

            return $this->successResponse('User has been blocked successfully', 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * =========================================================================
     * Unblock User: Unblock a Previously Blocked User
     * =========================================================================
     * Reverses the block action, allowing a previously blocked user to send
     * messages again within the conversation.
     */
    public function unblockUser(BlockUserRequest $request)
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                return $this->errorResponse('User not authenticated', 401);
            }

            $this->conversationService->unblockUser($userId, $request->conversation_id, $request->blocked_user);

            return $this->successResponse('User has been unblocked successfully', 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}

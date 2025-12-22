<?php

namespace App\Http\Services;

use App\Models\Conversation;
use App\Models\ConversationBlock;
use App\Models\Message;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ConversationService
{
    protected $typeMap = [
        'user' => User::class,
        'organization' => Organization::class,
    ];

    /**
     * Store a new conversation
     */
    public function store(array $data)
    {
        $p1Type = $this->typeMap[$data['participant_one_type']];
        $p2Type = $this->typeMap[$data['participant_two_type']];

        // check existence of participants
        if (!$p1Type::find($data['participant_one_id'])) {
            throw new \Exception('Participant one not found.', 404);
        }
        if (!$p2Type::find($data['participant_two_id'])) {
            throw new \Exception('Participant two not found.', 404);
        }

        // check if conversation already exists
        $conversation = Conversation::where(function ($query) use ($data, $p1Type, $p2Type) {
            $query->where('participant_one_id', $data['participant_one_id'])
                ->where('participant_one_type', $p1Type)
                ->where('participant_two_id', $data['participant_two_id'])
                ->where('participant_two_type', $p2Type);
        })->orWhere(function ($query) use ($data, $p1Type, $p2Type) {
            $query->where('participant_one_id', $data['participant_two_id'])
                ->where('participant_one_type', $p2Type)
                ->where('participant_two_id', $data['participant_one_id'])
                ->where('participant_two_type', $p1Type);
        })->with(['messages', 'participantOne', 'participantTwo'])->first();

        if ($conversation) {
            return ['conversation' => $conversation, 'created' => false];
        }

        // create new conversation
        $conversation = Conversation::create([
            'participant_one_id'   => $data['participant_one_id'],
            'participant_one_type' => $p1Type,
            'participant_two_id'   => $data['participant_two_id'],
            'participant_two_type' => $p2Type,
        ]);

        $conversation->load(['participantOne', 'participantTwo']);

        return ['conversation' => $conversation, 'created' => true];
    }

    /**
     * Get a single conversation details
     */
    public function getConversation($conversationId, $participantId, $participantType)
    {
        $modelType = $this->typeMap[$participantType] ?? null;
        if (!$modelType) {
            throw new \Exception("Invalid participant type", 422);
        }

        if (!$modelType::find($participantId)) {
            throw new \Exception("Participant not found.", 404);
        }

        $conversation = Conversation::with(['participantOne', 'participantTwo', 'messages'])
            ->where('id', $conversationId)
            ->where(function ($query) use ($participantId, $modelType) {
                $query->where(function ($q) use ($participantId, $modelType) {
                    $q->where('participant_one_id', $participantId)
                        ->where('participant_one_type', $modelType);
                })->orWhere(function ($q) use ($participantId, $modelType) {
                    $q->where('participant_two_id', $participantId)
                        ->where('participant_two_type', $modelType);
                });
            })
            ->first();

        if (!$conversation) {
            throw new \Exception("Conversation not found or access denied.", 404);
        }

        // Determine other participant
        $isParticipantOne = $conversation->participant_one_id == $participantId
            && $conversation->participant_one_type == $modelType;

        $otherParticipant = $isParticipantOne
            ? $conversation->participantTwo
            : $conversation->participantOne;

        $participantData = $this->formatParticipant($otherParticipant);

        return [
            'id' => $conversation->id,
            'participant' => $participantData,
            'messages' => $conversation->messages,
        ];
    }

    /**
     * Get list of conversations for a user
     */
    public function getUserConversations($participantId, $participantTypeShort)
    {
        $participantModelClass = $this->typeMap[$participantTypeShort] ?? null;
        if (!$participantModelClass) {
            throw new \Exception('Invalid participant_type', 422);
        }

        // Load conversations
        $conversations = Conversation::with(['lastMessage', 'unreadMessages'])
            ->where(function ($q) use ($participantId, $participantModelClass) {
                $q->where(function ($q2) use ($participantId, $participantModelClass) {
                    $q2->where('participant_one_id', $participantId)
                        ->where('participant_one_type', $participantModelClass);
                })->orWhere(function ($q3) use ($participantId, $participantModelClass) {
                    $q3->where('participant_two_id', $participantId)
                        ->where('participant_two_type', $participantModelClass);
                });
            })
            ->orderByDesc('updated_at')
            ->get([
                'id',
                'participant_one_id',
                'participant_one_type',
                'participant_two_id',
                'participant_two_type',
                'updated_at'
            ]);

        // Eager load other participants efficiently
        $otherUserIds = [];
        $otherOrgIds = [];

        foreach ($conversations as $conv) {
            $isP1 = ($conv->participant_one_id == $participantId)
                && ($conv->participant_one_type === $participantModelClass);

            if ($isP1) {
                $otherId = $conv->participant_two_id;
                $otherType = $conv->participant_two_type;
            } else {
                $otherId = $conv->participant_one_id;
                $otherType = $conv->participant_one_type;
            }

            if ($otherType === User::class) {
                $otherUserIds[] = $otherId;
            } else {
                $otherOrgIds[] = $otherId;
            }
        }

        $otherUserIds = array_unique($otherUserIds);
        $otherOrgIds = array_unique($otherOrgIds);

        $usersById = !empty($otherUserIds)
            ? User::whereIn('id', $otherUserIds)->get(['id', 'name', 'image', 'account_type'])->keyBy('id')
            : collect();

        $orgsById = !empty($otherOrgIds)
            ? Organization::whereIn('id', $otherOrgIds)->get(['id', 'title', 'logo', 'account_type'])->keyBy('id')
            : collect();

        // Format result
        $filtered = $conversations->map(function ($conv) use (
            $participantId,
            $participantModelClass,
            $usersById,
            $orgsById,
            $participantTypeShort
        ) {
            $isP1 = ($conv->participant_one_id == $participantId)
                && ($conv->participant_one_type === $participantModelClass);

            $otherId = $isP1 ? $conv->participant_two_id : $conv->participant_one_id;
            $otherType = $isP1 ? $conv->participant_two_type : $conv->participant_one_type;

            $other = ($otherType === User::class)
                ? $usersById->get($otherId)
                : $orgsById->get($otherId);

            // Safer format logic
            $participantData = [
                'id' => $otherId,
                'type' => $other->account_type ?? ($otherType === User::class ? 'user' : 'organization'),
            ];

            if ($other) {
                if ($otherType === User::class) {
                    $participantData['name'] = $other->name;
                    $participantData['image'] = $other->image;
                } else {
                    $participantData['name'] = $other->title;
                    $participantData['image']  = $other->logo;
                }
            }

            $otherShortType = ($otherType == Organization::class) ? 'organization' : 'user';

            // Unread count
            $unreadCount = Message::where('conversation_id', $conv->id)
                ->where('is_read', 0)
                ->where('sender_id', $otherId)
                ->where('sender_type', $otherShortType)
                ->where('receiver_id', $participantId)
                ->where('receiver_type', $participantTypeShort)
                ->count();

            // Last message
            $lastMessage = $conv->lastMessage ? [
                'id' => $conv->lastMessage->id,
                'message' => $conv->lastMessage->message,
                'message_type' => $conv->lastMessage->message_type ?? null,
                'attachment' => $conv->lastMessage->attachment ?? null,
                'sender_id' => $conv->lastMessage->sender_id,
                'sender_type' => $conv->lastMessage->sender_type ?? null,
                'created_at' => $conv->lastMessage->created_at,
            ] : null;

            return [
                'id' => $conv->id,
                'participant' => $participantData,
                'last_message' => $lastMessage,
                'unread_count' => $unreadCount,
                'updated_at' => $conv->updated_at,
            ];
        });

        return $filtered;
    }

    public function setActive($userId, $accountType, $conversationId)
    {
        Cache::put("participant:{$accountType}:{$userId}:active_conversation", $conversationId, now()->addMinutes(30));
    }

    public function clearActive($userId, $accountType)
    {
        Cache::forget("participant:{$accountType}:{$userId}:active_conversation");
    }

    public function blockUser($userId, $conversationId, $blockedUserId)
    {
        // Check if user is participant
        $conversation = Conversation::where('id', $conversationId)
            ->where(function ($query) use ($userId) {
                $query->where('participant_one_id', $userId)
                    ->orWhere('participant_two_id', $userId);
            })->first();

        if (!$conversation) {
            throw new \Exception('Conversation not found or unauthorized', 403);
        }

        $existingBlock = ConversationBlock::where('conversation_id', $conversationId)
            ->where('blocked_by', $userId)
            ->where('blocked_user', $blockedUserId)
            ->first();

        if ($existingBlock) {
            throw new \Exception('User is already blocked', 400);
        }

        ConversationBlock::create([
            'conversation_id' => $conversationId,
            'blocked_by' => $userId,
            'blocked_user' => $blockedUserId,
        ]);
    }

    public function unblockUser($userId, $conversationId, $blockedUserId)
    {
        $block = ConversationBlock::where('conversation_id', $conversationId)
            ->where('blocked_by', $userId)
            ->where('blocked_user', $blockedUserId)
            ->first();

        if (!$block) {
            throw new \Exception('No block record found', 404);
        }

        $block->delete();
    }

    protected function formatParticipant($participant)
    {
        if (!$participant) return null;

        if ($participant instanceof User) {
            return [
                'id'    => $participant->id,
                'name'  => $participant->name,
                'image' => $participant->image,
                'is_signed' => $participant->is_signed,
            ];
        } elseif ($participant instanceof Organization) {
            return [
                'id'    => $participant->id,
                'title' => $participant->title,
                'logo'  => $participant->logo,
                'is_signed'  => $participant->is_signed,
            ];
        }
        return null;
    }
}

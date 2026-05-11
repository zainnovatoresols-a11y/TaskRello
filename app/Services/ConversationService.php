<?php

namespace App\Services;

use App\Events\ConversationCreated;
use App\Models\Board;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    // ──────────────────────────────────────────────────────────
    // Find existing direct conversation OR create a new one
    // Called when user clicks "Message" on another user's profile
    // ──────────────────────────────────────────────────────────
    public function findOrCreateDirect(User $userA, User $userB): Conversation
    {
        // Check if direct conversation already exists
        $existing = Conversation::findDirect($userA->id, $userB->id);

        if ($existing) {
            return $existing;
        }

        // Create new direct conversation inside a transaction
        return DB::transaction(function () use ($userA, $userB) {

            $conversation = Conversation::create([
                'type'       => 'direct',
                'name'       => null,
                'created_by' => $userA->id,
            ]);

            // Add both users as participants
            $this->addParticipant(
                $conversation,
                $userA,
                role: 'admin'
            );

            $this->addParticipant(
                $conversation,
                $userB,
                role: 'admin'
            );
            // Both are admin in direct chats
            // because both can manage the conversation

            // Notify both users via their personal channels
            broadcast(new ConversationCreated(
                $conversation,
                [$userA->id, $userB->id]
            ))->toOthers();

            return $conversation;
        });
    }

    // ──────────────────────────────────────────────────────────
    // Create a new group conversation
    // ──────────────────────────────────────────────────────────
    public function createGroup(
        User   $creator,
        array  $userIds,
        string $name
    ): Conversation {

        return DB::transaction(function () use ($creator, $userIds, $name) {

            $conversation = Conversation::create([
                'type'       => 'group',
                'name'       => $name,
                'created_by' => $creator->id,
            ]);

            // Add creator as admin
            $this->addParticipant(
                $conversation,
                $creator,
                role: 'admin'
            );

            // Add all other members
            $allParticipantIds = [$creator->id];

            foreach ($userIds as $userId) {
                if ($userId === $creator->id) continue;

                $user = User::find($userId);
                if (!$user) continue;

                $this->addParticipant($conversation, $user, role: 'member');
                $allParticipantIds[] = $userId;
            }

            // Notify all participants on their personal channels
            broadcast(new ConversationCreated(
                $conversation,
                $allParticipantIds
            ))->toOthers();

            return $conversation;
        });
    }

    // ──────────────────────────────────────────────────────────
    // Create or get board conversation
    // Called automatically when a board is created
    // ──────────────────────────────────────────────────────────
    public function findOrCreateBoardConversation(Board $board): Conversation
    {
        // Return existing if already created
        $existing = $board->conversation;
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($board) {

            $conversation = Conversation::create([
                'type'       => 'board',
                'name'       => $board->name,
                'board_id'   => $board->id,
                'created_by' => $board->user_id,
            ]);

            // Add all current board members as participants
            $participantIds = [];

            foreach ($board->members as $member) {
                $role = $member->pivot->role === 'owner' ? 'admin' : 'member';
                $this->addParticipant($conversation, $member, role: $role);
                $participantIds[] = $member->id;
            }

            return $conversation;
        });
    }

    // ──────────────────────────────────────────────────────────
    // Add a single participant to a conversation
    // ──────────────────────────────────────────────────────────
    public function addParticipant(
        Conversation $conversation,
        User         $user,
        string       $role = 'member'
    ): ConversationParticipant {

        // Use firstOrCreate to prevent duplicates
        return ConversationParticipant::firstOrCreate(
            [
                'conversation_id' => $conversation->id,
                'user_id'         => $user->id,
            ],
            [
                'role'      => $role,
                'joined_at' => now(),
            ]
        );
    }

    // ──────────────────────────────────────────────────────────
    // Remove a participant from a conversation
    // ──────────────────────────────────────────────────────────
    public function removeParticipant(
        Conversation $conversation,
        User         $user
    ): void {

        ConversationParticipant::where('conversation_id', $conversation->id)
                               ->where('user_id', $user->id)
                               ->delete();
    }

    // ──────────────────────────────────────────────────────────
    // Mark all messages as read for a user in a conversation
    // Updates last_read_at timestamp on the participant record
    // ──────────────────────────────────────────────────────────
    public function markAsRead(Conversation $conversation, User $user): void
    {
        ConversationParticipant::where('conversation_id', $conversation->id)
                               ->where('user_id', $user->id)
                               ->update(['last_read_at' => now()]);
    }

    // ──────────────────────────────────────────────────────────
    // Get all conversations for a user (inbox)
    // Ordered by most recent activity
    // ──────────────────────────────────────────────────────────
    public function getInbox(User $user): \Illuminate\Support\Collection
    {
        return $user->conversations()
                    ->with([
                        'users',
                        'lastMessage.sender',
                        'participants' => fn($q) => $q->where('user_id', $user->id),
                        'board',
                    ])
                    ->orderByDesc('last_message_at')
                    ->get()
                    ->filter(function (Conversation $conv) use ($user) {
                        // For board conversations, only include if user is accepted member
                        if ($conv->type === 'board' && $conv->board) {
                            return $conv->board->isMember($user);
                        }
                        return true;
                    })
                    ->map(function (Conversation $conv) use ($user) {
                        return [
                            'id'              => $conv->id,
                            'type'            => $conv->type,
                            'name'            => $conv->getDisplayNameFor($user),
                            'board_id'        => $conv->board_id,
                            'last_message_at' => $conv->last_message_at?->toDateTimeString(),
                            'unread_count'    => $conv->unreadCountFor($user),
                            'is_muted'        => $conv->pivot->is_muted ?? false,
                            'participants'    => $conv->users->map(fn($u) => [
                                'id'     => $u->id,
                                'name'   => $u->name,
                                'avatar' => $u->avatar,
                            ]),
                            'last_message'    => $conv->lastMessage ? [
                                'body'        => $conv->lastMessage->deleted_at
                                    ? 'Message deleted'
                                    : ($conv->lastMessage->body ?? 'Sent an attachment'),
                                'sender_name' => $conv->lastMessage->sender?->name,
                                'created_at'  => $conv->lastMessage->created_at
                                    ?->diffForHumans(),
                            ] : null,
                        ];
                    });
    }

    // ──────────────────────────────────────────────────────────
    // Get total unread count across all conversations
    // Used for the navbar badge
    // ──────────────────────────────────────────────────────────
    public function getTotalUnread(User $user): int
    {
        return ConversationParticipant::where('user_id', $user->id)
            ->with('conversation.board')
            ->get()
            ->sum(function ($participant) use ($user) {
                $conversation = $participant->conversation;

                // For board conversations, only count if user is accepted member
                if ($conversation->type === 'board' && $conversation->board) {
                    if (!$conversation->board->isMember($user)) {
                        return 0;
                    }
                }

                $query = \App\Models\Message::where(
                    'conversation_id',
                    $participant->conversation_id
                )
                ->where('user_id', '!=', $user->id)
                ->whereNull('deleted_at');

                if ($participant->last_read_at) {
                    $query->where('created_at', '>', $participant->last_read_at);
                }

                return $query->count();
            });
    }

    // ──────────────────────────────────────────────────────────
    // Toggle mute for a conversation
    // ──────────────────────────────────────────────────────────
    public function toggleMute(Conversation $conversation, User $user): bool
    {
        $participant = ConversationParticipant::where('conversation_id', $conversation->id)
                                              ->where('user_id', $user->id)
                                              ->first();

        if (!$participant) return false;

        $newMuteState = !$participant->is_muted;
        $participant->update(['is_muted' => $newMuteState]);

        return $newMuteState;
    }

    // ──────────────────────────────────────────────────────────
    // Sync board members with board conversation
    // Called when a member is added or removed from a board
    // ──────────────────────────────────────────────────────────
    public function syncBoardParticipant(
        Board  $board,
        User   $user,
        string $action = 'add'
    ): void {

        $conversation = $board->conversation;
        if (!$conversation) return;

        if ($action === 'add') {
            $this->addParticipant($conversation, $user, role: 'member');
        } else {
            $this->removeParticipant($conversation, $user);
        }
    }
}
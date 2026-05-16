<?php

namespace App\Services;

use App\Events\ConversationCreated;
use App\Models\Board;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use App\Repositories\Contracts\ConversationRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    public function __construct(
        private ConversationRepositoryInterface $conversationRepository
    ) {}

    public function findOrCreateDirect(User $userA, User $userB): Conversation
    {
        $existing = Conversation::findDirect($userA->id, $userB->id);

        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($userA, $userB) {

            $conversation = Conversation::create([
                'type'       => 'direct',
                'name'       => null,
                'created_by' => $userA->id,
            ]);

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
            broadcast(new ConversationCreated(
                $conversation,
                [$userA->id, $userB->id]
            ))->toOthers();

            return $conversation;
        });
    }

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

            $this->addParticipant(
                $conversation,
                $creator,
                role: 'admin'
            );

            $allParticipantIds = [$creator->id];

            foreach ($userIds as $userId) {
                if ($userId === $creator->id) continue;

                $user = User::find($userId);
                if (!$user) continue;

                $this->addParticipant($conversation, $user, role: 'member');
                $allParticipantIds[] = $userId;
            }

            broadcast(new ConversationCreated(
                $conversation,
                $allParticipantIds
            ))->toOthers();

            return $conversation;
        });
    }

    public function findOrCreateBoardConversation(Board $board): Conversation
    {
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

            $participantIds = [];

            foreach ($board->members as $member) {
                $role = $member->pivot->role === 'owner' ? 'admin' : 'member';
                $this->addParticipant($conversation, $member, role: $role);
                $participantIds[] = $member->id;
            }

            return $conversation;
        });
    }

    public function addParticipant(
        Conversation $conversation,
        User         $user,
        string       $role = 'member'
    ): ConversationParticipant {

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

    public function removeParticipant(
        Conversation $conversation,
        User         $user
    ): void {

        ConversationParticipant::where('conversation_id', $conversation->id)
                               ->where('user_id', $user->id)
                               ->delete();
    }

    public function markAsRead(Conversation $conversation, User $user): void
    {
        ConversationParticipant::where('conversation_id', $conversation->id)
                               ->where('user_id', $user->id)
                               ->update(['last_read_at' => now()]);
    }

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

    public function getTotalUnread(User $user): int
    {
        return ConversationParticipant::where('user_id', $user->id)
            ->with('conversation.board')
            ->get()
            ->sum(function ($participant) use ($user) {
                $conversation = $participant->conversation;

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

    public function loadWithRelations(Conversation $conversation): Conversation
    {
        return $this->conversationRepository->loadWithRelations($conversation);
    }

    public function searchBoardMembers(User $user, string $searchTerm): Collection
    {
        return $this->conversationRepository->searchBoardMembers($user, $searchTerm);
    }

    public function getBoardMembers(User $user): Collection
    {
        return $this->conversationRepository->getBoardMembers($user);
    }
}
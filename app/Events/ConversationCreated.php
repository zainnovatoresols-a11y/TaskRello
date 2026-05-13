<?php

namespace App\Events;

use App\Models\User;
use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Conversation $conversation,
        public array        $participantIds
    ) {}

    public function broadcastOn(): array
    {
        return collect($this->participantIds)
            ->map(fn($userId) => new PrivateChannel('user.' . $userId))
            ->toArray();
    }

    public function broadcastAs(): string
    {
        return 'conversation.created';
    }

    public function broadcastWith(): array
    {
        $this->conversation->loadMissing(['users', 'lastMessage', 'creator']);

        return [
            'conversation' => [
                'id'              => $this->conversation->id,
                'type'            => $this->conversation->type,
                'name'            => $this->conversation->name,
                'board_id'        => $this->conversation->board_id,
                'last_message_at' => $this->conversation->last_message_at?->toDateTimeString(),
                'created_at'      => $this->conversation->created_at->toDateTimeString(),
                'creator'         => [
                    'id'   => $this->conversation->creator->id,
                    'name' => $this->conversation->creator->name,
                ],
                'participants'    => $this->conversation->users->map(fn($u) => [
                    'id'     => $u->id,
                    'name'   => $u->name,
                    'avatar' => $u->avatar,
                    'role'   => $u->pivot->role,
                ]),
                'unread_count'    => 0,
                'last_message'    => null,
            ],
        ];
    }
}
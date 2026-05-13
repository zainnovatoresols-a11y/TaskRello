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

class UserTyping implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User         $user,
        public Conversation $conversation,
        public bool         $isTyping
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
                'conversation.' . $this->conversation->id
            ),
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.typing';
    }

    public function broadcastWith(): array
    {
        return [
            'user' => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
            'conversation_id' => $this->conversation->id,
            'is_typing'       => $this->isTyping,
        ];
    }

    public function broadcastConnection(): string
    {
        return 'reverb';
    }
}
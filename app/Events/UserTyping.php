<?php

namespace App\Events;

use App\Models\User;
use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User         $user,
        public Conversation $conversation,
        public bool         $isTyping
    ) {}

    // ── Broadcast on this channel ─────────────────────────────
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
                'conversation.' . $this->conversation->id
            ),
        ];
    }

    // ── Event name the frontend listens for ───────────────────
    public function broadcastAs(): string
    {
        return 'user.typing';
    }

    // ── Payload sent to the frontend ──────────────────────────
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

    // ── IMPORTANT: This event is ephemeral ────────────────────
    // Typing indicators are NEVER stored in the database.
    // They fire on Reverb and disappear after 3 seconds on
    // the frontend. No queue, no persistence needed.
    // Use ShouldBroadcastNow so it fires instantly without queue.
    public function broadcastConnection(): string
    {
        return 'reverb';
    }
}
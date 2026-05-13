<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Message      $message,
        public Conversation $conversation
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
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        // Load relationships needed for rendering
        $this->message->loadMissing(['sender', 'replyTo.sender']);

        return [
            'message' => [
                'id'              => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'body'            => $this->message->body,
                'type'            => $this->message->type,
                'is_edited'       => $this->message->is_edited,
                'is_deleted'      => false,
                'attachment_url'  => $this->message->attachment_url,
                'attachment_name' => $this->message->attachment_name,
                'attachment_size' => $this->message->attachment_size,
                'formatted_size'  => $this->message->formatted_size,
                'created_at'      => $this->message->created_at->toDateTimeString(),
                'time'            => $this->message->created_at->format('g:i A'),
                'sender'          => [
                    'id'     => $this->message->sender->id,
                    'name'   => $this->message->sender->name,
                    'avatar' => $this->message->sender->avatar,
                ],
                'reply_to'        => $this->message->reply_to_id
                    ? [
                        'id'     => $this->message->replyTo?->id,
                        'body'   => $this->message->replyTo?->body
                                    ?? 'Message deleted',
                        'sender' => $this->message->replyTo?->sender?->name
                                    ?? 'Unknown',
                    ]
                    : null,
            ],
            'conversation_id' => $this->conversation->id,
        ];
    }

    public function broadcastToEveryoneElse(): bool
    {
        return true;
    }
}
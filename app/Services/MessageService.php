<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MessageService
{
    // ──────────────────────────────────────────────────────────
    // Send a text message
    // ──────────────────────────────────────────────────────────
    public function send(
        Conversation  $conversation,
        User          $sender,
        string        $body,
        ?int          $replyToId = null
    ): Message {

        return DB::transaction(function () use (
            $conversation, $sender, $body, $replyToId
        ) {
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'user_id'         => $sender->id,
                'body'            => $body,
                'type'            => 'text',
                'reply_to_id'     => $replyToId,
            ]);

            // Update last_message_at on conversation
            // This keeps the inbox sorted by recent activity
            $conversation->update([
                'last_message_at' => now(),
            ]);

            // Mark as read for the sender immediately
            // Sender has obviously read their own message
            $this->markReadForSender($conversation, $sender);

            // Broadcast to all participants via Reverb
            broadcast(new MessageSent($message, $conversation))
                ->toOthers();

            return $message->load('sender', 'replyTo.sender');
        });
    }

    // ──────────────────────────────────────────────────────────
    // Send a file or image attachment
    // ──────────────────────────────────────────────────────────
    public function sendAttachment(
        Conversation  $conversation,
        User          $sender,
        UploadedFile  $file,
        ?int          $replyToId = null
    ): Message {

        return DB::transaction(function () use (
            $conversation, $sender, $file, $replyToId
        ) {
            // Determine message type
            $isImage = str_starts_with($file->getMimeType(), 'image/');
            $type    = $isImage ? 'image' : 'file';

            // Store file
            $path = $file->store(
                'chat-attachments/conversation-' . $conversation->id,
                'public'
            );

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'user_id'         => $sender->id,
                'body'            => null,
                'type'            => $type,
                'attachment_path' => $path,
                'attachment_name' => $file->getClientOriginalName(),
                'attachment_size' => $file->getSize(),
                'reply_to_id'     => $replyToId,
            ]);

            $conversation->update(['last_message_at' => now()]);

            $this->markReadForSender($conversation, $sender);

            broadcast(new MessageSent($message, $conversation))
                ->toOthers();

            return $message->load('sender', 'replyTo.sender');
        });
    }

    // ──────────────────────────────────────────────────────────
    // Edit a message body
    // Only the sender can edit their own message
    // ──────────────────────────────────────────────────────────
    public function edit(Message $message, string $newBody): Message
    {
        $message->update([
            'body'      => $newBody,
            'is_edited' => true,
        ]);

        return $message->fresh()->load('sender');
    }

    // ──────────────────────────────────────────────────────────
    // Soft delete a message
    // Deleted messages show "This message was deleted" in UI
    // Hard delete is never done to preserve reply context
    // ──────────────────────────────────────────────────────────
    public function delete(Message $message): void
    {
        // Delete attachment from storage if exists
        if ($message->attachment_path) {
            Storage::disk('public')->delete($message->attachment_path);
        }

        $message->delete(); // soft delete via SoftDeletes trait
    }

    // ──────────────────────────────────────────────────────────
    // Mark all messages as read for a user
    // Called when user opens a conversation
    // ──────────────────────────────────────────────────────────
    public function markAsRead(Conversation $conversation, User $user): void
    {
        \App\Models\ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);
    }

    // ──────────────────────────────────────────────────────────
    // Mark individual message as read (for read receipts)
    // Creates a record in message_reads table
    // ──────────────────────────────────────────────────────────
    public function markMessageRead(Message $message, User $user): void
    {
        MessageRead::firstOrCreate(
            [
                'message_id' => $message->id,
                'user_id'    => $user->id,
            ],
            [
                'read_at' => now(),
            ]
        );
    }

    // ──────────────────────────────────────────────────────────
    // Load messages for a conversation
    // Loads all messages ordered oldest first
    // ──────────────────────────────────────────────────────────
    public function getMessages(
        Conversation $conversation,
        ?int         $beforeId = null
        // beforeId not used since loading all
    ): \Illuminate\Database\Eloquent\Collection {

        $query = Message::where('conversation_id', $conversation->id)
                        ->withTrashed()
                        // include soft-deleted so "deleted" label shows
                        ->with(['sender', 'replyTo.sender'])
                        ->orderBy('created_at', 'asc');

        return $query->get();
    }

    // ──────────────────────────────────────────────────────────
    // Broadcast typing indicator
    // This does NOT store anything in the database
    // It is a pure ephemeral WebSocket event
    // ──────────────────────────────────────────────────────────
    public function broadcastTyping(
        Conversation $conversation,
        User         $user,
        bool         $isTyping
    ): void {

        broadcast(new UserTyping($user, $conversation, $isTyping))
            ->toOthers();
    }

    // ──────────────────────────────────────────────────────────
    // Format a message for JSON response
    // Consistent shape for both HTTP and WebSocket responses
    // ──────────────────────────────────────────────────────────
    public function formatMessage(Message $message): array
    {
        return [
            'id'              => $message->id,
            'conversation_id' => $message->conversation_id,
            'body'            => $message->deleted_at
                                 ? null
                                 : $message->body,
            'type'            => $message->type,
            'is_edited'       => $message->is_edited,
            'is_deleted'      => (bool) $message->deleted_at,
            'attachment_url'  => $message->attachment_url,
            'attachment_name' => $message->attachment_name,
            'attachment_size' => $message->attachment_size,
            'formatted_size'  => $message->formatted_size,
            'reply_to'        => $message->replyTo ? [
                'id'     => $message->replyTo->id,
                'body'   => $message->replyTo->deleted_at
                            ? 'Message deleted'
                            : $message->replyTo->body,
                'sender' => $message->replyTo->sender?->name ?? 'Unknown',
            ] : null,
            'created_at'      => $message->created_at->toDateTimeString(),
            'time'            => $message->created_at->format('g:i A'),
            'date'            => $message->created_at->toDateString(),
            'sender'          => $message->sender ? [
                'id'     => $message->sender->id,
                'name'   => $message->sender->name,
                'avatar' => $message->sender->avatar,
            ] : null,
        ];
    }

    // ──────────────────────────────────────────────────────────
    // Private — mark sender's own message as read immediately
    // ──────────────────────────────────────────────────────────
    private function markReadForSender(
        Conversation $conversation,
        User         $sender
    ): void {

        \App\Models\ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $sender->id)
            ->update(['last_read_at' => now()]);
    }
}
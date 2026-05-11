<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private MessageService $messageService
    ) {}

    // ──────────────────────────────────────────────────────────
    // GET /chat/conversations/{conversation}/messages
    // Load paginated messages for a conversation
    // Supports infinite scroll via ?before_id=123
    // ──────────────────────────────────────────────────────────
    public function index(Request $request, Conversation $conversation)
    {
        if (!$conversation->hasParticipant($request->user())) {
            abort(403, 'You are not a participant in this conversation.');
        }

        $beforeId = $request->query('before_id');

        $paginated = $this->messageService->getMessages(
            $conversation,
            perPage: 50,
            beforeId: $beforeId ? (int) $beforeId : null
        );

        $messages = collect($paginated->items())
            ->map(fn($msg) => $this->messageService->formatMessage($msg));

        return response()->json([
            'success'  => true,
            'data'     => $messages,
            'has_more' => $paginated->hasMorePages(),
            'next_before_id' => $paginated->items()
                ? collect($paginated->items())->first()?->id
                : null,
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // POST /chat/conversations/{conversation}/messages
    // Send a new message
    // Body: { body: "Hello!", reply_to_id: null }
    // Or multipart/form-data with file attachment
    // ──────────────────────────────────────────────────────────
    public function store(Request $request, Conversation $conversation)
    {
        if (!$conversation->hasParticipant($request->user())) {
            abort(403, 'You are not a participant in this conversation.');
        }

        // Handle file attachment
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => [
                    'required',
                    'file',
                    'max:20480', // 20MB max
                    'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,zip',
                ],
                'reply_to_id' => ['nullable', 'exists:messages,id'],
            ]);

            $message = $this->messageService->sendAttachment(
                $conversation,
                $request->user(),
                $request->file('file'),
                $request->reply_to_id
            );

        } else {
            // Text message
            $request->validate([
                'body'        => ['required', 'string', 'max:5000'],
                'reply_to_id' => ['nullable', 'exists:messages,id'],
            ]);

            $message = $this->messageService->send(
                $conversation,
                $request->user(),
                $request->body,
                $request->reply_to_id
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent.',
            'data'    => $this->messageService->formatMessage($message),
        ], 201);
    }

    // ──────────────────────────────────────────────────────────
    // PUT /chat/messages/{message}
    // Edit a message body
    // Only the sender can edit their own message
    // Body: { body: "Updated text" }
    // ──────────────────────────────────────────────────────────
    public function update(Request $request, Message $message)
    {
        // Only sender can edit
        if ($message->user_id !== $request->user()->id) {
            abort(403, 'You can only edit your own messages.');
        }

        // Cannot edit deleted messages
        if ($message->deleted_at) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit a deleted message.',
            ], 422);
        }

        // Cannot edit attachments
        if ($message->type !== 'text') {
            return response()->json([
                'success' => false,
                'message' => 'Only text messages can be edited.',
            ], 422);
        }

        $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $updated = $this->messageService->edit($message, $request->body);

        return response()->json([
            'success' => true,
            'message' => 'Message updated.',
            'data'    => $this->messageService->formatMessage($updated),
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // DELETE /chat/messages/{message}
    // Soft delete a message
    // Sender can delete their own
    // Conversation admin can delete any message
    // ──────────────────────────────────────────────────────────
    public function destroy(Request $request, Message $message)
    {
        $user         = $request->user();
        $isSender     = $message->user_id === $user->id;
        $conversation = $message->conversation;

        $isAdmin = $conversation->participants()
                                ->where('user_id', $user->id)
                                ->where('role', 'admin')
                                ->exists();

        if (!$isSender && !$isAdmin) {
            abort(403, 'You cannot delete this message.');
        }

        $this->messageService->delete($message);

        return response()->json([
            'success'    => true,
            'message'    => 'Message deleted.',
            'message_id' => $message->id,
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // POST /chat/conversations/{conversation}/read
    // Mark all messages as read when user opens conversation
    // Called automatically when conversation is focused
    // ──────────────────────────────────────────────────────────
    public function markRead(Request $request, Conversation $conversation)
    {
        if (!$conversation->hasParticipant($request->user())) {
            abort(403);
        }

        $this->messageService->markAsRead($conversation, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Marked as read.',
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // POST /chat/conversations/{conversation}/typing
    // Broadcast typing indicator to other participants
    // Body: { is_typing: true }
    // This does NOT store anything in the database
    // ──────────────────────────────────────────────────────────
    public function typing(Request $request, Conversation $conversation)
    {
        if (!$conversation->hasParticipant($request->user())) {
            abort(403);
        }

        $request->validate([
            'is_typing' => ['required', 'boolean'],
        ]);

        $this->messageService->broadcastTyping(
            $conversation,
            $request->user(),
            $request->boolean('is_typing')
        );

        return response()->json(['success' => true]);
    }
}
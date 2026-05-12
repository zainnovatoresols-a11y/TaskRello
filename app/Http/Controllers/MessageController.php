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

    public function index(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        if ($conversation->type === 'board') {
            if (!$conversation->board->isMember($user)) {
                abort(403, 'You are not an accepted member of this board.');
            }
        } else {
            if (!$conversation->hasParticipant($user)) {
                abort(403, 'You are not a participant in this conversation.');
            }
        }

        $beforeId = $request->query('before_id');

        $messages = $this->messageService->getMessages(
            $conversation,
            beforeId: $beforeId ? (int) $beforeId : null
        );

        $messages = $messages
            ->map(fn($msg) => $this->messageService->formatMessage($msg));

        return response()->json([
            'success'  => true,
            'data'     => $messages,
            'has_more' => false,
            'next_before_id' => null,
        ]);
    }

    public function store(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        if ($conversation->type === 'board') {
            if (!$conversation->board->isMember($user)) {
                abort(403, 'You are not an accepted member of this board.');
            }
        } else {
            if (!$conversation->hasParticipant($user)) {
                abort(403, 'You are not a participant in this conversation.');
            }
        }

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

    public function update(Request $request, Message $message)
    {
        if ($message->user_id !== $request->user()->id) {
            abort(403, 'You can only edit your own messages.');
        }

        if ($message->deleted_at) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit a deleted message.',
            ], 422);
        }

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

    public function destroy(Request $request, Message $message)
    {
        $user         = $request->user();
        $isSender     = $message->user_id === $user->id;
        $conversation = $message->conversation;

        if ($conversation->type === 'board') {
            if (!$conversation->board->isMember($user)) {
                abort(403, 'You are not an accepted member of this board.');
            }
        } else {
            if (!$conversation->hasParticipant($user)) {
                abort(403, 'You are not a participant in this conversation.');
            }
        }

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

    public function markRead(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        if ($conversation->type === 'board') {
            if (!$conversation->board->isMember($user)) {
                abort(403);
            }
        } else {
            if (!$conversation->hasParticipant($user)) {
                abort(403);
            }
        }

        $this->messageService->markAsRead($conversation, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Marked as read.',
        ]);
    }

    public function typing(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        if ($conversation->type === 'board') {
            if (!$conversation->board->isMember($user)) {
                abort(403);
            }
        } else {
            if (!$conversation->hasParticipant($user)) {
                abort(403);
            }
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
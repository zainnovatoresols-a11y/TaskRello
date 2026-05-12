<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use App\Services\ConversationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private ConversationService $conversationService
    ) {}

    public function index(Request $request)
    {
        $inbox = $this->conversationService->getInbox($request->user());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $inbox,
            ]);
        }

        return view('chat.index', [
            'conversations' => $inbox,
            'activeId'      => null,
        ]);
    }

    public function show(Request $request, Conversation $conversation)
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

        $this->conversationService->markAsRead(
            $conversation,
            $request->user()
        );

        $conversation->load(['users', 'board']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => [
                    'id'           => $conversation->id,
                    'type'         => $conversation->type,
                    'name'         => $conversation->getDisplayNameFor(
                        $request->user()
                    ),
                    'board_id'     => $conversation->board_id,
                    'participants' => $conversation->users->map(fn($u) => [
                        'id'     => $u->id,
                        'name'   => $u->name,
                        'avatar' => $u->avatar,
                        'role'   => $u->pivot->role,
                    ]),
                ],
            ]);
        }

        $inbox = $this->conversationService->getInbox($request->user());

        return view('chat.index', [
            'conversations'      => $inbox,
            'activeId'           => $conversation->id,
            'activeConversation' => $conversation,
        ]);
    }

    public function direct(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $otherUser = User::findOrFail($request->user_id);

        if ($otherUser->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot start a conversation with yourself.',
            ], 422);
        }

        $conversation = $this->conversationService->findOrCreateDirect(
            $request->user(),
            $otherUser
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => [
                    'id'   => $conversation->id,
                    'type' => $conversation->type,
                ],
            ]);
        }

        return redirect()->route('chat.show', $conversation);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'user_ids'   => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $conversation = $this->conversationService->createGroup(
            $request->user(),
            $request->user_ids,
            $request->name
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Group created successfully.',
                'data'    => [
                    'id'   => $conversation->id,
                    'type' => $conversation->type,
                    'name' => $conversation->name,
                ],
            ], 201);
        }

        return redirect()->route('chat.show', $conversation)
            ->with('success', 'Group created.');
    }

    public function addMember(Request $request, Conversation $conversation)
    {
        if ($conversation->type === 'direct') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot add members to a direct conversation.',
            ], 422);
        }

        $participant = $conversation->participants()
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$participant || $participant->role !== 'admin') {
            abort(403, 'Only group admins can add members.');
        }

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = User::findOrFail($request->user_id);

        if ($conversation->hasParticipant($user)) {
            return response()->json([
                'success' => false,
                'message' => "{$user->name} is already in this conversation.",
            ], 422);
        }

        $this->conversationService->addParticipant(
            $conversation,
            $user,
            role: 'member'
        );

        app(MessageService::class)->send(
            $conversation,
            $request->user(),
            "{$request->user()->name} added {$user->name} to the group",
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$user->name} added to the conversation.",
                'data'    => [
                    'id'     => $user->id,
                    'name'   => $user->name,
                    'avatar' => $user->avatar,
                    'role'   => 'member',
                ],
            ]);
        }

        return redirect()->back()->with('success', "{$user->name} added.");
    }

    public function removeMember(
        Request      $request,
        Conversation $conversation,
        User         $user
    ) {
        if ($conversation->type === 'direct') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove members from a direct conversation.',
            ], 422);
        }

        $participant = $conversation->participants()
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$participant || $participant->role !== 'admin') {
            abort(403, 'Only group admins can remove members.');
        }

        if ($user->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot remove yourself. Leave the group instead.',
            ], 422);
        }

        $this->conversationService->removeParticipant($conversation, $user);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$user->name} removed from the conversation.",
            ]);
        }

        return redirect()->back()->with('success', "{$user->name} removed.");
    }

    public function toggleMute(Request $request, Conversation $conversation)
    {
        if (!$conversation->hasParticipant($request->user())) {
            abort(403);
        }

        $isMuted = $this->conversationService->toggleMute(
            $conversation,
            $request->user()
        );

        return response()->json([
            'success'  => true,
            'is_muted' => $isMuted,
            'message'  => $isMuted
                ? 'Conversation muted.'
                : 'Conversation unmuted.',
        ]);
    }

    public function searchUsers(Request $request)
    {
        $searchTerm = $request->get('query', '');

        if (empty(trim($searchTerm))) {
            return response()->json([
                'success' => true,
                'data'    => [],
            ]);
        }

        $users = User::where('id', '!=', $request->user()->id)
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%');
            })
            ->select('id', 'name', 'email', 'avatar')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $users,
        ]);
    }
}

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

    // ──────────────────────────────────────────────────────────
    // GET /chat
    // Show inbox — all conversations for the logged in user
    // ──────────────────────────────────────────────────────────
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

    // ──────────────────────────────────────────────────────────
    // GET /chat/conversations/{conversation}
    // Open a specific conversation
    // ──────────────────────────────────────────────────────────
    public function show(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        // For board conversations, check if user is an accepted member
        if ($conversation->type === 'board') {
            if (!$conversation->board->isMember($user)) {
                abort(403, 'You are not an accepted member of this board.');
            }
        } else {
            if (!$conversation->hasParticipant($user)) {
                abort(403, 'You are not a participant in this conversation.');
            }
        }

        // Mark as read when conversation is opened
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

    // ──────────────────────────────────────────────────────────
    // POST /chat/conversations/direct
    // Find or create a direct (1-to-1) conversation
    // Body: { user_id: 5 }
    // ──────────────────────────────────────────────────────────
    public function direct(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $otherUser = User::findOrFail($request->user_id);

        // Cannot start a conversation with yourself
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

    // ──────────────────────────────────────────────────────────
    // POST /chat/conversations
    // Create a new group conversation
    // Body: { name: "Team Chat", user_ids: [2, 3, 4] }
    // ──────────────────────────────────────────────────────────
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

    // ──────────────────────────────────────────────────────────
    // POST /chat/conversations/{conversation}/members
    // Add a member to a group conversation
    // Body: { user_id: 5 }
    // Only group admins can do this
    // ──────────────────────────────────────────────────────────
    public function addMember(Request $request, Conversation $conversation)
    {
        // Only group conversations support adding members
        if ($conversation->type === 'direct') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot add members to a direct conversation.',
            ], 422);
        }

        // Check if requester is an admin of this conversation
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

        // Check if already a participant
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

        // Send system message
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

    // ──────────────────────────────────────────────────────────
    // DELETE /chat/conversations/{conversation}/members/{user}
    // Remove a member from a group conversation
    // Only group admins can do this
    // ──────────────────────────────────────────────────────────
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

        // Cannot remove yourself — use leave instead
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

    // ──────────────────────────────────────────────────────────
    // POST /chat/conversations/{conversation}/mute
    // Toggle mute for a conversation
    // ──────────────────────────────────────────────────────────
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

    // ──────────────────────────────────────────────────────────
    // GET /api/chat/users
    // Search users to start a conversation with
    // Body: { query: "ali" }
    // ──────────────────────────────────────────────────────────
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
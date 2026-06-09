<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBoardRequest;
use App\Http\Requests\UpdateBoardRequest;
use App\Models\Board;
use App\Models\User;
use App\Services\BoardService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Mail\BoardInvitationMail;
use App\Models\BoardInvitation;
use Illuminate\Support\Facades\Mail;


class BoardController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private BoardService $boardService
    ) {}

    public function index(Request $request)
    {
        $boards = $this->boardService->getUserBoards($request->user());

        // Only return JSON for explicit API calls (XMLHttpRequest), not browser navigation
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'data'   => $boards,
            ], 200);
        }

        return view('boards.index', compact('boards'));
    }

    public function state(Request $request, Board $board)
    {
        $this->authorize('view', $board);

        $board = $this->boardService->loadStateRelations($board);

        return response()->json([
            'success' => true,
            'board'   => [
                'id'       => $board->id,
                'owner_id' => $board->user_id,
            ],
            'members' => $board->members->map(function ($member) {
                return [
                    'id'    => $member->id,
                    'name'  => $member->name,
                    'email' => $member->email,
                    'role'  => $member->pivot->role,
                ];
            })->values(),
            'invited_members' => $board->invitedMembers->map(function ($member) {
                return [
                    'id'    => $member->id,
                    'name'  => $member->name,
                    'email' => $member->email,
                ];
            })->values(),
        ]);
    }

    public function create()
    {
        return view('boards.create');
    }

    public function store(StoreBoardRequest $request)
    {
        $board = $this->boardService->create($request->user(), $request->validated());

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store(
                'board-backgrounds/board-' . $board->id,
                'public'
            );

            $board->update(['background_image' => $path]);
        }

        if ($request->expectsJson() && $request->isXmlHttpRequest()) {
            return response()->json([
                'status'  => 'created',
                'message' => 'Board created successfully!',
                'data'    => $board->fresh(),
            ], 201);
        }

        return redirect()
            ->route('boards.show', $board)
            ->with('success', 'Board created successfully!');
    }

    public function show(Request $request, Board $board)
    {
        $this->authorize('view', $board);
        $board = $this->boardService->loadBoardWithRelations($board);

        if ($request->expectsJson() && $request->isXmlHttpRequest()) {
            return response()->json([
                'success' => true,
                'data'    => [
                    'id'               => $board->id,
                    'name'             => $board->name,
                    'description'      => $board->description,
                    'background_color' => $board->background_color,
                    'is_archived'      => $board->is_archived,
                    'created_at'       => $board->created_at->toDateTimeString(),
                    'owner'            => [
                        'id'   => $board->owner->id,
                        'name' => $board->owner->name,
                    ],
                    'members' => $board->members->map(fn($m) => [
                        'id'    => $m->id,
                        'name'  => $m->name,
                        'email' => $m->email,
                        'role'  => $m->pivot->role,
                    ]),
                    'labels' => $board->labels->map(fn($l) => [
                        'id'    => $l->id,
                        'name'  => $l->name,
                        'color' => $l->color,
                    ]),
                    'lists' => $board->lists->map(fn($list) => [
                        'id'       => $list->id,
                        'name'     => $list->name,
                        'position' => $list->position,
                        'cards'    => $list->cards->map(fn($card) => [
                            'id'           => $card->id,
                            'title'        => $card->title,
                            'description'  => $card->description,
                            'position'     => $card->position,
                            'due_date'     => $card->due_date?->toDateString(),
                            'cover_color'  => $card->cover_color,
                            'is_completed' => $card->is_completed,
                            'is_overdue'   => $card->isOverdue(),
                            'is_due_soon'  => $card->isDueSoon(),
                            'assignees'    => $card->assignees->map(fn($u) => [
                                'id'   => $u->id,
                                'name' => $u->name,
                            ]),
                            'labels' => $card->labels->map(fn($l) => [
                                'id'    => $l->id,
                                'name'  => $l->name,
                                'color' => $l->color,
                            ]),
                        ]),
                    ]),
                ],
            ]);
        }

        return view('boards.show', compact('board'));
    }

    public function edit(Board $board)
    {
        $this->authorize('update', $board);
        return view('boards.edit', compact('board'));
    }

    public function update(UpdateBoardRequest $request, Board $board)
    {
        $this->boardService->update($board, $request->validated());

        if ($request->expectsJson() && $request->isXmlHttpRequest()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Board updated.',
                'data'    => $board->fresh(),
            ], 200);
        }

        return redirect()
            ->route('boards.show', $board)
            ->with('success', 'Board updated.');
    }

    public function destroy(Request $request, Board $board)
    {
        $this->authorize('delete', $board);
        $this->boardService->delete($board);

        if ($request->expectsJson() && $request->isXmlHttpRequest()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Board deleted.',
            ], 200);
        }

        return redirect()
            ->route('boards.index')
            ->with('success', 'Board deleted.');
    }

    public function addMember(Request $request, Board $board)
    {
        $this->authorize('manageMember', $board);

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No user found with that email address.',
        ]);

        $user = User::where('email', $request->email)->first();

        $response = $this->boardService->addMember($board, $request->user(), $user, $request);

        if ($response) {
            return $response;
        }

        if ($request->expectsJson() && $request->isXmlHttpRequest()) {
            return response()->json([
                'success' => true,
                'message' => "{$user->name} has been invited to the board.",
                'data'    => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => 'member',
                ],
            ], 201);
        }

        return redirect()
            ->route('boards.edit', $board)
            ->with('success', "{$user->name} has been invited to the board.");
    }

    public function acceptInvitation(Request $request, Board $board)
    {
        $user = $request->user();

        if (!$board->hasPendingInvite($user)) {
            abort(403, 'No pending invitation found for this board.');
        }

        $this->boardService->acceptInvitation($board, $user, $request->query('notif_id'));

        return redirect()
            ->route('boards.show', $board)
            ->with('success', 'You have joined the board.');
    }

    public function declineInvitation(Request $request, Board $board)
    {
        $user = $request->user();

        if (!$board->hasPendingInvite($user)) {
            if ($request->expectsJson() && $request->isXmlHttpRequest()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No pending invitation found for this board.',
                ], 403);
            }
            abort(403, 'No pending invitation found for this board.');
        }

        $this->boardService->declineInvitation($board, $user, $request->query('notif_id'));

        if ($request->expectsJson() && $request->isXmlHttpRequest()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Invitation declined.',
            ], 200);
        }

        return redirect()
            ->route('boards.index')
            ->with('success', 'Invitation declined.');
    }

    public function removeMember(Request $request, Board $board, User $user)
    {
        $this->authorize('manageMember', $board);

        $response = $this->boardService->removeMember($board, $request->user(), $user, $request);

        if ($response) {
            return $response;
        }

        if ($request->expectsJson() && $request->isXmlHttpRequest()) {
            return response()->json([
                'status'  => 'success',
                'message' => "{$user->name} has been removed from the board.",
            ], 200);
        }

        return redirect()
            ->route('boards.edit', $board)
            ->with('success', "{$user->name} has been removed from the board.");
    }

    public function uploadBackgroundImage(Request $request, Board $board)
    {
        $this->authorize('update', $board);

        $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:8192',
            ],
        ]);

        $board = $this->boardService->uploadBackgroundImage($board, $request->file('image'), $request->user());

        return response()->json([
            'success'              => true,
            'message'              => 'Background image updated.',
            'background_image_url' => $board->background_image_url,
        ]);
    }

    public function removeBackgroundImage(Request $request, Board $board)
    {
        $this->authorize('update', $board);

        $this->boardService->removeBackgroundImage($board);

        return response()->json([
            'success' => true,
            'message' => 'Background image removed.',
        ]);
    }

    public function inviteByEmail(Request $request, Board $board)
    {
        $this->authorize('manageMember', $board);

        $request->validate([
            'invite_email' => ['required', 'email'],
        ], [
            'invite_email.required' => 'Please enter an email address.',
            'invite_email.email'    => 'Please enter a valid email address.',
        ]);

        $email = strtolower(trim($request->invite_email));

        // Check if user is already registered
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            // Already registered — tell them to use the Add Member section
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "{$existingUser->name} is already registered. Use the \"Add Member\" section above to add them directly.",
                ], 422);
            }

            return redirect()
                ->route('boards.edit', $board)
                ->with('error', "{$existingUser->name} is already registered. Use the Add Member section to add them.");
        }

        // Check if already a board member by email
        $alreadyMember = $board->members()
            ->where('email', $email)
            ->exists();

        if ($alreadyMember) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This email is already a member of this board.',
                ], 422);
            }

            return redirect()
                ->route('boards.edit', $board)
                ->with('error', 'This email is already a member of this board.');
        }

        // Check if invitation already pending for this email on this board
        $existing = BoardInvitation::where('board_id', $board->id)
            ->where('email', $email)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($existing) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "An invitation was already sent to {$email}. It expires " . $existing->expires_at->diffForHumans() . '.',
                ], 422);
            }

            return redirect()
                ->route('boards.edit', $board)
                ->with('error', "An invitation was already sent to {$email}.");
        }

        // Create the invitation
        $invitation = BoardInvitation::create([
            'board_id'   => $board->id,
            'invited_by' => $request->user()->id,
            'email'      => $email,
            'token'      => BoardInvitation::generateToken(),
            'status'     => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        // Send registration email
        Mail::to($email)->send(
            new BoardInvitationMail($invitation, $board, $request->user())
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Invitation sent to {$email}. They will receive a registration link.",
                'data'    => [
                    'email'      => $email,
                    'expires_at' => $invitation->expires_at->format('M j, Y'),
                ],
            ]);
        }

        return redirect()
            ->route('boards.edit', $board)
            ->with('success', "Invitation sent to {$email}. They will receive a registration email.");
    }
}

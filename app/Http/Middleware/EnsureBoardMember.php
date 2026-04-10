<?php

namespace App\Http\Middleware;

use App\Models\Board;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBoardMember
{
    public function handle(Request $request, Closure $next): Response
    {
        // Get the board from the route parameter
        // Works for routes like /boards/{board} and
        // nested routes like /boards/{board}/lists
        $board = $request->route('board');

        // If no board in the route just continue
        if (!$board instanceof Board) {
            return $next($request);
        }

        // Check if the authenticated user is a member
        if (!$board->isMember($request->user())) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this board.',
                ], 403);
            }

            abort(403, 'You are not a member of this board.');
        }

        return $next($request);
    }
}

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
        $board = $request->route('board');

        if (!$board instanceof Board) {
            return $next($request);
        }

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

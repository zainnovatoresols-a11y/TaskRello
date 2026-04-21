<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Card;
use App\Models\Label;
use App\Services\LabelService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LabelController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private LabelService $labelService
    ) {}

    public function index(Request $request, Board $board)
    {
        $this->authorize('view', $board);

        $labels = $board->labels()->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $labels->map(fn($l) => [
                    'id'    => $l->id,
                    'name'  => $l->name,
                    'color' => $l->color,
                ]),
            ]);
        }

        return redirect()->route('boards.show', $board);
    }

    public function store(Request $request, Board $board)
    {
        $this->authorize('update', $board);

        $request->validate([
            'name'  => 'required|string|max:100',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $label = $this->labelService->create($board, $request->only(['name', 'color']));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $label], 201);
        }

        return redirect()->route('boards.show', $board)->with('success', 'Label created.');
    }

    public function update(Request $request, Board $board, Label $label)
    {
        $this->authorize('update', $board);

        $request->validate([
            'name'  => 'required|string|max:100',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        try {
            $label = $this->labelService->update($board, $label, $request->only(['name', 'color']));
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Label updated successfully.',
                'data'    => ['id' => $label->id, 'name' => $label->name, 'color' => $label->color],
            ]);
        }

        return redirect()->route('boards.show', $board)->with('success', 'Label updated.');
    }

    public function attach(Request $request, Card $card, Label $label)
    {
        $this->authorize('update', $card);

        try {
            $labels = $this->labelService->attach($card, $label);
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'labels' => $labels]);
        }

        return redirect()->route('boards.show', $card->list->board)->with('success', 'Label attached.');
    }

    public function detach(Request $request, Card $card, Label $label)
    {
        $this->authorize('update', $card);

        $labels = $this->labelService->detach($card, $label);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'labels' => $labels]);
        }

        return redirect()->route('boards.show', $card->list->board)->with('success', 'Label removed.');
    }

    public function destroy(Request $request, Board $board, Label $label)
    {
        $this->authorize('update', $board);

        try {
            $labelName = $label->name;
            $this->labelService->delete($board, $label);
        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Label '{$labelName}' deleted successfully."]);
        }

        return redirect()->route('boards.show', $board)->with('success', "Label '{$labelName}' deleted.");
    }
}

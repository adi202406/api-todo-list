<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Board;
use Illuminate\Http\Request;
use App\Http\Requests\CardRequest;
use App\Http\Resources\CardResource;
use App\Models\Workspace;

class CardController extends Controller
{
    public function store(CardRequest $request, Workspace $workspace, Board $board)
    {
        $validated = $request->validated();
        $validated['board_id'] = $board->id;
        
        // Set default position if not provided
        if (!isset($validated['position'])) {
            $lastPosition = Card::where('board_id', $board->id)->max('position') ?? 0;
            $validated['position'] = $lastPosition + 1;
        }

        $card = Card::create($validated);

        $card->users()->attach(auth()->id(), [
            'assigned_at' => now()
        ]);
        
        return new CardResource($card);
    }

    public function show(Board $board, Card $card)
    {
        return new CardResource($card);
    }

    public function update(CardRequest $request, Board $board, Card $card)
    {
        $card->update($request->validated());
        return new CardResource($card);
    }

    public function destroy(Board $board, Card $card)
    {
        $card->delete();
        return response()->json([
            'message' => 'Card deleted successfully'
        ]);
    }
}
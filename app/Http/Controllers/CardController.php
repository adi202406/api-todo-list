<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use App\Http\Requests\CardRequest;
use App\Http\Resources\CardResource;

class CardController extends Controller
{
      public function index()
    {
        $cards = Card::orderBy('position')->get();
        return CardResource::collection($cards);
    }

    public function store(CardRequest $request)
    {
        $card = Card::create($request->validated());
        return new CardResource($card);
    }

    public function show(Card $card)
    {
        return new CardResource($card);
    }

    public function update(CardRequest $request, Card $card)
    {
        $card->update($request->validated());
        return new CardResource($card);
    }

    public function destroy(Card $card)
    {
        $card->delete();
        return response()->json([
            'message' => 'Card deleted successfully'
        ], 200);
    }

    public function getByBoard($boardId)
    {
        $cards = Card::where('board_id', $boardId)
            ->orderBy('position')
            ->get();
            
        return CardResource::collection($cards);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use App\Http\Requests\CardAssignmentRequest;
use App\Http\Resources\CardAssignmentResource;

class CardAssignmentController extends Controller
{
     /**
     * Get all assignees for a card
     */
    public function index(Card $card): JsonResponse
    {
        return response()->json([
            'data' => UserResource::collection($card->assignees)
        ]);
    }

    /**
     * Assign a user to a card
     */
    public function store(Request $request, Card $card): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $card->assignees()->syncWithoutDetaching([$validated['user_id']]);

        return response()->json([
            'message' => 'User assigned to card successfully',
            'data' => UserResource::collection($card->assignees)
        ], 201);
    }

    /**
     * Remove a user assignment from a card
     */
    public function destroy(Card $card, User $user): JsonResponse
    {
        $card->assignees()->detach($user->id);

        return response()->json(null, 204);
    }
}

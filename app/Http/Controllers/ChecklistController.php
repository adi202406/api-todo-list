<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ChecklistRequest;
use App\Http\Resources\ChecklistResource;

class ChecklistController extends Controller
{
    /**
     * Display a listing of checklists
     */
    public function index(Request $request): JsonResponse
    {
        $checklists = Checklist::query()
            ->when($request->has('card_id'), function ($query) use ($request) {
                $query->where('card_id', $request->card_id);
            })
            ->orderBy('position')
            ->paginate();

        return response()->json(ChecklistResource::collection($checklists));
    }

    /**
     * Display a specific checklist
     */
    public function show(Checklist $checklist): JsonResponse
    {
        return response()->json([
            'data' => new ChecklistResource($checklist)
        ]);
    }

    /**
     * Create a new checklist
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'card_id' => 'required|exists:cards,id',
            'position' => 'sometimes|integer'
        ]);

        $checklist = Checklist::create($validated);

        return response()->json([
            'data' => new ChecklistResource($checklist)
        ], 201);
    }

    /**
     * Update a checklist
     */
    public function update(Request $request, Checklist $checklist): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'position' => 'sometimes|integer'
        ]);

        $checklist->update($validated);

        return response()->json([
            'data' => new ChecklistResource($checklist)
        ]);
    }

    /**
     * Delete a checklist
     */
    public function destroy(Checklist $checklist): JsonResponse
    {
        $checklist->delete();

        return response()->json(null, 204);
    }

    /**
     * Update checklist position
     */
    public function updatePosition(Checklist $checklist, int $position): JsonResponse
    {
        $checklist->position = $position;
        $checklist->save();

        return response()->json([
            'data' => new ChecklistResource($checklist)
        ]);
    }
}

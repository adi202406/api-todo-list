<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use Illuminate\Http\Request;
use App\Models\ChecklistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ChecklistItemRequest;
use App\Http\Resources\ChecklistItemResource;

class ChecklistItemController extends Controller
{
   /**
     * Get all items for a checklist
     */
    public function index(Checklist $checklist): JsonResponse
    {
        $items = $checklist->items()->orderBy('position')->get();
        
        return response()->json([
            'data' => ChecklistItemResource::collection($items)
        ]);
    }
    
    /**
     * Get a specific checklist item
     */
    public function show(Checklist $checklist, ChecklistItem $item): JsonResponse
    {
        // Validate that item belongs to checklist
        if ($item->checklist_id !== $checklist->id) {
            return response()->json([
                'message' => 'Checklist item not found in the specified checklist'
            ], 404);
        }
        
        return response()->json([
            'data' => new ChecklistItemResource($item)
        ]);
    }

    /**
     * Create a new checklist item
     */
    public function store(Request $request, Checklist $checklist): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'position' => 'sometimes|integer',
            'is_completed' => 'sometimes|boolean'
        ]);

        $validated['checklist_id'] = $checklist->id;
        
        if (isset($validated['is_completed']) && $validated['is_completed']) {
            $validated['completed_at'] = now();
            $validated['completed_by'] = Auth::id();
        }

        $item = ChecklistItem::create($validated);
        
        // Update parent checklist stats
        // $checklist->recalculateStats();

        return response()->json([
            'data' => new ChecklistItemResource($item)
        ], 201);
    }

    /**
     * Update a checklist item
     */
    public function update(Request $request, Checklist $checklist, ChecklistItem $item): JsonResponse
    {
        // Validate that item belongs to checklist
        if ($item->checklist_id !== $checklist->id) {
            return response()->json([
                'message' => 'Checklist item not found in the specified checklist'
            ], 404);
        }
        
        $validated = $request->validate([
            'content' => 'sometimes|string',
            'position' => 'sometimes|integer',
            'is_completed' => 'sometimes|boolean'
        ]);

        
        
        if (isset($validated['is_completed'])) {
            $validated['completed_at'] = $validated['is_completed'] ? now() : null;
            $validated['completed_by'] = $validated['is_completed'] ? Auth::id() : null;
        }

        $item->update($validated);
        // $checklist->recalculateStats();

        return response()->json([
            'data' => new ChecklistItemResource($item),
        ]);
    }

    /**
     * Delete a checklist item
     */
    public function destroy(Checklist $checklist, ChecklistItem $item): JsonResponse
    {
        // Validate that item belongs to checklist
        if ($item->checklist_id !== $checklist->id) {
            return response()->json([
                'message' => 'Checklist item not found in the specified checklist'
            ], 404);
        }
        
        $item->delete();
        // $checklist->recalculateStats();

        return response()->json(null, 204);
    }

    /**
     * Bulk update checklist items (primarily for reordering)
     */
    public function bulkUpdate(Request $request, Checklist $checklist): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:checklist_items,id',
            'items.*.position' => 'required|integer'
        ]);

        foreach ($validated['items'] as $itemData) {
            // Ensure the item belongs to this checklist
            ChecklistItem::where('id', $itemData['id'])
                ->where('checklist_id', $checklist->id)
                ->update(['position' => $itemData['position']]);
        }

        return response()->json([
            'message' => 'Items updated successfully',
            'data' => ChecklistItemResource::collection($checklist->items()->orderBy('position')->get())
        ]);
    }
}

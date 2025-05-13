<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Workspace;
use App\Http\Requests\BoardRequest;
use App\Http\Resources\BoardResource;

class BoardController extends Controller
{
    public function index(Workspace $workspace)
    {
        $boards = $workspace->boards()
            ->orderBy('position')
            ->withCount('cards')
            ->get();

        return BoardResource::collection($boards);
    }

    public function store(BoardRequest $request, Workspace $workspace)
    {
        $validated = $request->validated();
        $validated['workspace_id'] = $workspace->id;

        $board = Board::create($validated);

        return new BoardResource($board);
    }

    public function show(Workspace $workspace, Board $board)
    {
        return new BoardResource($board->load(['workspace', 'cards']));
    }

    public function update(BoardRequest $request, Workspace $workspace, Board $board)
    {
        $validated = $request->validated();
        $validated['workspace_id'] = $workspace->id;

        $board->update($validated);

        return new BoardResource($board);
    }

    public function destroy(Workspace $workspace, Board $board)
    {
        $board->delete();

        return response()->json(null, 204);
    }
}

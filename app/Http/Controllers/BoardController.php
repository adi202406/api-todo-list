<?php
namespace App\Http\Controllers;

use App\Models\Board;
use App\Http\Requests\BoardRequest;
use App\Http\Resources\BoardResource;

class BoardController extends Controller
{
    public function index()
    {
        $boards = Board::orderBy('position')->get();
        return BoardResource::collection($boards);
    }

    public function store(BoardRequest $request)
    {
        $board = Board::create($request->validated());
        return new BoardResource($board);
    }

    public function show(Board $board)
    {
        return new BoardResource($board);
    }

    public function update(BoardRequest $request, Board $board)
    {
        $board->update($request->validated());
        return new BoardResource($board);
    }

    public function destroy(Board $board)
    {
        $board->delete();
        return response()->json([
            'message' => 'Board deleted successfully'
        ], 200);
    }
}

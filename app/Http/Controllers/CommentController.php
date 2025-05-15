<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\CommentResource;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function index(Card $card): AnonymousResourceCollection
    {
        $comments = $card->comments()
            ->with('user')
            ->latest()
            ->paginate(10);

        return CommentResource::collection($comments);
    }

    public function store(StoreCommentRequest $request, Card $card): CommentResource
    {
        $comment = $card->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $request->input('content'),
        ]);

        return new CommentResource($comment->load('user'));
    }

    public function show(Comment $comment): CommentResource
    {
        return new CommentResource($comment->load('user'));
    }

    public function update(UpdateCommentRequest $request, Comment $comment): CommentResource
    {
        $comment->update($request->validated());

        return new CommentResource($comment->load('user'));
    }

    public function destroy(Comment $comment): JsonResponse
    {
        if (Gate::denies('delete', $comment)) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return response()->json(null, 204);
    }
}
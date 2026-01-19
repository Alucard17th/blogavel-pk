<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Controllers\Api\V1\Admin;

use Blogavel\Blogavel\Http\Resources\CommentResource;
use Blogavel\Blogavel\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = Comment::query()->orderByDesc('id');

        $status = $request->query('status');
        if (is_string($status) && $status !== '') {
            $query->where('status', $status);
        }

        $comments = $query->paginate(50);

        return CommentResource::collection($comments);
    }

    public function approve(Comment $comment)
    {
        $comment->status = 'approved';
        $comment->save();

        return new CommentResource($comment);
    }

    public function spam(Comment $comment)
    {
        $comment->status = 'spam';
        $comment->save();

        return new CommentResource($comment);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json(['deleted' => true]);
    }
}

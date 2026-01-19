<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Controllers\Admin;

use Blogavel\Blogavel\Models\Comment;
use Illuminate\Routing\Controller;

final class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::query()->orderByDesc('id')->paginate(50);

        return view('blogavel::admin.comments.index', [
            'comments' => $comments,
        ]);
    }

    public function approve(Comment $comment)
    {
        $comment->status = 'approved';
        $comment->save();

        return redirect()->route('blogavel.admin.comments.index');
    }

    public function spam(Comment $comment)
    {
        $comment->status = 'spam';
        $comment->save();

        return redirect()->route('blogavel.admin.comments.index');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->route('blogavel.admin.comments.index');
    }
}

<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Controllers\Api\V1;

use Blogavel\Blogavel\Http\Resources\CommentResource;
use Blogavel\Blogavel\Models\Comment;
use Blogavel\Blogavel\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class CommentController extends Controller
{
    public function index(Post $post)
    {
        abort_unless($post->status === 'published', 404);

        $comments = Comment::query()
            ->where('post_id', $post->id)
            ->where('status', 'approved')
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('status', 'approved')->with(['children' => function ($query) {
                    $query->where('status', 'approved');
                }]);
            }])
            ->orderBy('id')
            ->get();

        return CommentResource::collection($comments);
    }

    public function store(Request $request, Post $post)
    {
        abort_unless($post->status === 'published', 404);

        $data = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
            'parent_id' => ['nullable', 'integer', 'exists:blogavel_comments,id'],
            'guest_name' => ['nullable', 'string', 'max:255'],
            'guest_email' => ['nullable', 'email', 'max:255'],
        ]);

        $userId = $request->user()?->id;

        if ($userId === null) {
            if (! isset($data['guest_name']) || $data['guest_name'] === '') {
                $data['guest_name'] = 'Guest';
            }
        } else {
            $data['guest_name'] = null;
            $data['guest_email'] = null;
        }

        $parentId = $data['parent_id'] ?? null;
        if ($parentId !== null) {
            $parent = Comment::query()->whereKey($parentId)->first();
            if ($parent === null || (int) $parent->post_id !== (int) $post->id) {
                abort(422);
            }
        }

        $comment = Comment::create([
            'post_id' => $post->id,
            'parent_id' => $parentId,
            'user_id' => $userId,
            'guest_name' => $data['guest_name'] ?? null,
            'guest_email' => $data['guest_email'] ?? null,
            'content' => $data['content'],
            'status' => 'pending',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return (new CommentResource($comment))->response()->setStatusCode(201);
    }
}

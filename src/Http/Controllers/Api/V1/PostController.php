<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Controllers\Api\V1;

use Blogavel\Blogavel\Http\Resources\PostResource;
use Blogavel\Blogavel\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::query()
            ->with(['category', 'tags', 'featuredMedia', 'author'])
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(10);

        return PostResource::collection($posts);
    }

    public function show(Post $post)
    {
        abort_unless($post->status === 'published', 404);

        $post->load(['category', 'tags', 'featuredMedia', 'author']);

        return new PostResource($post);
    }
}

<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Controllers;

use Blogavel\Blogavel\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::query()
            ->with(['featuredMedia', 'author'])
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('blogavel::posts.index', [
            'posts' => $posts,
        ]);
    }

    public function show(Post $post)
    {
        abort_unless($post->status === 'published', 404);

        $post->load([
            'featuredMedia',
            'author',
            'comments' => function ($query) {
                $query
                    ->where('status', 'approved')
                    ->whereNull('parent_id')
                    ->with([
                        'children' => function ($query) {
                            $query
                                ->where('status', 'approved')
                                ->with([
                                    'children' => function ($query) {
                                        $query->where('status', 'approved');
                                    },
                                ]);
                        },
                    ])
                    ->orderBy('id');
            },
        ]);

        return view('blogavel::posts.show', [
            'post' => $post,
        ]);
    }
}

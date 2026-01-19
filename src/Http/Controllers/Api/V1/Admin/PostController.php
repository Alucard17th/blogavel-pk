<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Controllers\Api\V1\Admin;

use Blogavel\Blogavel\Http\Resources\PostResource;
use Blogavel\Blogavel\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

final class PostController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:blogavel_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blogavel_posts,slug'],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,scheduled,published'],
            'published_at' => ['nullable', 'date'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:blogavel_tags,id'],
        ]);

        if (! isset($data['slug']) || $data['slug'] === '') {
            $data['slug'] = Str::slug($data['title']);
        }

        $tagIds = $data['tags'] ?? [];
        unset($data['tags']);

        $post = Post::create($data);
        $post->tags()->sync($tagIds);

        $post->load(['category', 'tags', 'featuredMedia']);

        return (new PostResource($post))->response()->setStatusCode(201);
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:blogavel_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blogavel_posts,slug,'.$post->id],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,scheduled,published'],
            'published_at' => ['nullable', 'date'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:blogavel_tags,id'],
        ]);

        if (! isset($data['slug']) || $data['slug'] === '') {
            $data['slug'] = Str::slug($data['title']);
        }

        $tagIds = $data['tags'] ?? [];
        unset($data['tags']);

        $post->update($data);
        $post->tags()->sync($tagIds);

        $post->load(['category', 'tags', 'featuredMedia']);

        return new PostResource($post);
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json(['deleted' => true]);
    }
}

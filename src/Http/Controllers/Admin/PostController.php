<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Controllers\Admin;

use Blogavel\Blogavel\Models\Category;
use Blogavel\Blogavel\Models\Media;
use Blogavel\Blogavel\Models\Post;
use Blogavel\Blogavel\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class PostController extends Controller
{
    public function index()
    {
        $posts = Post::query()->orderByDesc('id')->paginate(20);

        return view('blogavel::admin.posts.index', [
            'posts' => $posts,
        ]);
    }

    public function create()
    {
        $categories = Category::query()->orderBy('name')->get();
        $tags = Tag::query()->orderBy('name')->get();

        return view('blogavel::admin.posts.create', [
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

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
            'featured_image' => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        $user = $request->user();
        if ($user !== null) {
            $data['author_id'] = $user->getAuthIdentifier();
        }

        if (! isset($data['slug']) || $data['slug'] === '') {
            $data['slug'] = Str::slug($data['title']);
        }

        $tagIds = $data['tags'] ?? [];
        unset($data['tags']);

        $featuredImage = $data['featured_image'] ?? null;
        unset($data['featured_image']);

        $post = Post::create($data);

        if ($featuredImage !== null) {
            $post->featured_media_id = $this->storeMedia($featuredImage)->id;
            $post->save();
        }

        if (count($tagIds) > 0) {
            $post->tags()->sync($tagIds);
        }

        return redirect()->route('blogavel.admin.posts.index');
    }

    public function edit(Post $post)
    {
        $categories = Category::query()->orderBy('name')->get();
        $tags = Tag::query()->orderBy('name')->get();

        return view('blogavel::admin.posts.edit', [
            'post' => $post,
            'categories' => $categories,
            'tags' => $tags,
        ]);
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
            'featured_image' => ['nullable', 'file', 'image', 'max:5120'],
            'remove_featured_image' => ['nullable', 'boolean'],
        ]);

        if (! isset($data['slug']) || $data['slug'] === '') {
            $data['slug'] = Str::slug($data['title']);
        }

        $tagIds = $data['tags'] ?? [];
        unset($data['tags']);

        $removeFeatured = $data['remove_featured_image'] ?? false;
        unset($data['remove_featured_image']);

        $featuredImage = $data['featured_image'] ?? null;
        unset($data['featured_image']);

        $post->update($data);
        $post->tags()->sync($tagIds);

        if ($removeFeatured) {
            $post->featured_media_id = null;
            $post->save();
        }

        if ($featuredImage !== null) {
            $post->featured_media_id = $this->storeMedia($featuredImage)->id;
            $post->save();
        }

        return redirect()->route('blogavel.admin.posts.edit', $post);
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('blogavel.admin.posts.index');
    }

    private function storeMedia(\Illuminate\Http\UploadedFile $file): Media
    {
        $disk = (string) config('blogavel.media_disk', 'public');
        $directory = (string) config('blogavel.media_directory', 'blogavel');

        $path = $file->store($directory, $disk);

        return Media::create([
            'disk' => $disk,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => (int) $file->getSize(),
        ]);
    }
}

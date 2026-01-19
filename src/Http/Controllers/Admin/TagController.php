<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Controllers\Admin;

use Blogavel\Blogavel\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

final class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::query()->orderBy('name')->paginate(100);

        return view('blogavel::admin.tags.index', [
            'tags' => $tags,
        ]);
    }

    public function create()
    {
        return view('blogavel::admin.tags.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blogavel_tags,slug'],
        ]);

        if (! isset($data['slug']) || $data['slug'] === '') {
            $data['slug'] = Str::slug($data['name']);
        }

        Tag::create($data);

        return redirect()->route('blogavel.admin.tags.index');
    }

    public function edit(Tag $tag)
    {
        return view('blogavel::admin.tags.edit', [
            'tag' => $tag,
        ]);
    }

    public function update(Request $request, Tag $tag)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blogavel_tags,slug,'.$tag->id],
        ]);

        if (! isset($data['slug']) || $data['slug'] === '') {
            $data['slug'] = Str::slug($data['name']);
        }

        $tag->update($data);

        return redirect()->route('blogavel.admin.tags.edit', $tag);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect()->route('blogavel.admin.tags.index');
    }
}

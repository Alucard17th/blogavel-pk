<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Controllers\Admin;

use Blogavel\Blogavel\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

final class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::query()->orderBy('name')->paginate(50);

        return view('blogavel::admin.categories.index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        $parents = Category::query()->orderBy('name')->get();

        return view('blogavel::admin.categories.create', [
            'parents' => $parents,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blogavel_categories,slug'],
            'parent_id' => ['nullable', 'integer', 'exists:blogavel_categories,id'],
        ]);

        if (! isset($data['slug']) || $data['slug'] === '') {
            $data['slug'] = Str::slug($data['name']);
        }

        Category::create($data);

        return redirect()->route('blogavel.admin.categories.index');
    }

    public function edit(Category $category)
    {
        $parents = Category::query()
            ->whereKeyNot($category->id)
            ->orderBy('name')
            ->get();

        return view('blogavel::admin.categories.edit', [
            'category' => $category,
            'parents' => $parents,
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blogavel_categories,slug,'.$category->id],
            'parent_id' => ['nullable', 'integer', 'exists:blogavel_categories,id'],
        ]);

        if (! isset($data['slug']) || $data['slug'] === '') {
            $data['slug'] = Str::slug($data['name']);
        }

        if (isset($data['parent_id']) && (int) $data['parent_id'] === (int) $category->id) {
            $data['parent_id'] = null;
        }

        $category->update($data);

        return redirect()->route('blogavel.admin.categories.edit', $category);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('blogavel.admin.categories.index');
    }
}

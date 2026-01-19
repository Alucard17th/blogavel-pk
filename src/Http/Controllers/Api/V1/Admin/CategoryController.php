<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Controllers\Api\V1\Admin;

use Blogavel\Blogavel\Http\Resources\CategoryResource;
use Blogavel\Blogavel\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

final class CategoryController extends Controller
{
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

        $category = Category::create($data);

        return (new CategoryResource($category))->response()->setStatusCode(201);
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

        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(['deleted' => true]);
    }
}

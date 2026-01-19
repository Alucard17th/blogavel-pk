<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Controllers\Api\V1;

use Blogavel\Blogavel\Http\Resources\CategoryResource;
use Blogavel\Blogavel\Models\Category;
use Illuminate\Routing\Controller;

final class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::query()->orderBy('name')->get();

        return CategoryResource::collection($categories);
    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }
}

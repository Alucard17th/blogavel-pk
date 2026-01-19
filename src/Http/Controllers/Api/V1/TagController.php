<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Controllers\Api\V1;

use Blogavel\Blogavel\Http\Resources\TagResource;
use Blogavel\Blogavel\Models\Tag;
use Illuminate\Routing\Controller;

final class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::query()->orderBy('name')->get();

        return TagResource::collection($tags);
    }

    public function show(Tag $tag)
    {
        return new TagResource($tag);
    }
}

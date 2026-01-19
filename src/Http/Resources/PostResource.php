<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PostResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'status' => $this->status,
            'published_at' => optional($this->published_at)?->toISOString(),
            'category' => $this->whenLoaded('category', fn () => new CategoryResource($this->category)),
            'tags' => $this->whenLoaded('tags', fn () => TagResource::collection($this->tags)),
            'featured_media' => $this->whenLoaded('featuredMedia', fn () => new MediaResource($this->featuredMedia)),
            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CommentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'post_id' => $this->post_id,
            'parent_id' => $this->parent_id,
            'author' => $this->authorName(),
            'content' => $this->content,
            'status' => $this->status,
            'created_at' => optional($this->created_at)?->toISOString(),
            'children' => $this->whenLoaded('children', fn () => CommentResource::collection($this->children)),
        ];
    }
}

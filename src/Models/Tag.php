<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class Tag extends Model
{
    use HasFactory;

    protected $table = 'blogavel_tags';

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $tag): void {
            if ($tag->slug === null || $tag->slug === '') {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'blogavel_post_tag', 'tag_id', 'post_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

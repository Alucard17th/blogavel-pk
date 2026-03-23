<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Blogavel\Blogavel\Models\Category;
use Blogavel\Blogavel\Models\Comment;
use Blogavel\Blogavel\Models\Tag;
use Blogavel\Blogavel\Models\Media;

final class Post extends Model
{
    use HasFactory;

    protected $table = 'blogavel_posts';

    protected $appends = [
        'featured_image_url',
    ];

    protected $fillable = [
        'category_id',
        'featured_media_id',
        'author_id',
        'views_count',
        'title',
        'slug',
        'content',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'views_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $post): void {
            if ($post->slug === null || $post->slug === '') {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        $media = $this->featuredMedia;

        if (! $media || $media->path === null || $media->path === '') {
            return null;
        }

        $disk = (string) ($media->disk ?: 'public');

        return Storage::disk($disk)->url((string) $media->path);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'blogavel_post_tag', 'post_id', 'tag_id');
    }

    public function featuredMedia()
    {
        return $this->belongsTo(Media::class, 'featured_media_id');
    }

    public function author()
    {
        $userModel = (string) config('auth.providers.users.model');

        return $this->belongsTo($userModel, 'author_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }
}

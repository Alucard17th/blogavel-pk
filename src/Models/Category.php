<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class Category extends Model
{
    use HasFactory;

    protected $table = 'blogavel_categories';

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $category): void {
            if ($category->slug === null || $category->slug === '') {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'category_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

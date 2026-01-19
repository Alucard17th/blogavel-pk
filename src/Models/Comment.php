<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Comment extends Model
{
    use HasFactory;

    protected $table = 'blogavel_comments';

    protected $fillable = [
        'post_id',
        'parent_id',
        'user_id',
        'guest_name',
        'guest_email',
        'content',
        'status',
        'ip',
        'user_agent',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function authorName(): string
    {
        if ($this->user_id !== null) {
            return 'User #'.$this->user_id;
        }

        return (string) ($this->guest_name ?: 'Guest');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}

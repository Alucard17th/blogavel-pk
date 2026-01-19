<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Media extends Model
{
    use HasFactory;

    protected $table = 'blogavel_media';

    protected $fillable = [
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'size' => 'int',
    ];
}

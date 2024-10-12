<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'description',
        'author',
        'source',
        'published_at',
        'url',
        'content',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}

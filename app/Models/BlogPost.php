<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'excerpt',
        'image',
        'author',
        'read_time',
        'published_at',
    ];

    protected $casts = [
        'title' => 'array',
        'excerpt' => 'array',
        'published_at' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

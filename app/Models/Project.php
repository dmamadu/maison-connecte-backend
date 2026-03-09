<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_type_id',
        'title',
        'description',
        'location',
        'year',
        'thumbnail',
        'client',
        'duration',
        'is_active',
        'order'
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
    ];

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class)->orderBy('order');
    }

    public function tags()
    {
        return $this->hasMany(ProjectTag::class);
    }

    public function services()
    {
        return $this->hasMany(ProjectService::class);
    }

    public function scopeByType($query, $typeSlug)
    {
        if ($typeSlug && $typeSlug !== 'all') {
            return $query->whereHas('projectType', function($q) use ($typeSlug) {
                $q->where('slug', $typeSlug);
            });
        }
        return $query;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

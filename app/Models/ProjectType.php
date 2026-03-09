<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'icon',
        'color',
        'is_active',
        'order'
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function activeProjects()
    {
        return $this->hasMany(Project::class)->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getProjectsCountAttribute()
    {
        return $this->projects()->count();
    }
}
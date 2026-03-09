<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'website',
        'industry',
        'description',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'partnership_start',
        'is_featured',
        'is_active',
        'order',
    ];

    protected $casts = [
        'partnership_start' => 'date',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    // Générer automatiquement le slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            if (empty($client->slug)) {
                $client->slug = Str::slug($client->name);
            }
        });

        static::updating(function ($client) {
            if ($client->isDirty('name')) {
                $client->slug = Str::slug($client->name);
            }
        });
    }

    // Relations
    public function projects()
    {
        return $this->hasMany(Project::class, 'client_name', 'name');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('is_active', true);
    }

    public function scopeOrderByPosition($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }
}
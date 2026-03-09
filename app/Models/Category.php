<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class Category extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
    ];

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
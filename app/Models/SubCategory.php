<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class SubCategory extends Model
{

    use HasFactory; // <- Important !
    protected $fillable = [
        'category_id',
        'slug',
        'name',
    ];

    protected $casts = [
        'name' => 'array',
    ];


     public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

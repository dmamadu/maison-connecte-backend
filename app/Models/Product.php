<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Product extends Model
{


     protected $fillable = [
        'category_id',
        'subcategory_id',
        'title',
        'description',
        'price',
        'image',
        'link',
        'highlights',
        'specs',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'price' => 'decimal:2',
        'highlights' => 'array',
        'specs' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function images()
{
    return $this->hasMany(\App\Models\ProductImage::class, 'product_id');
}
}

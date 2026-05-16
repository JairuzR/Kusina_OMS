<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'menu_category_id', 'name', 'slug', 'description',
        'price', 'cost_price', 'image', 'is_available',
        'is_featured', 'preparation_time', 'allergens',
        'calories', 'sort_order',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_featured'  => 'boolean',
        'allergens'    => 'array',
        'price'        => 'decimal:2',
        'cost_price'   => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
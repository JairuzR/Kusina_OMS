<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'menu_item_id', 'quantity',
        'unit_price', 'subtotal', 'status',
        'special_instructions', 'prepared_at', 'served_at',
    ];

    protected $casts = [
        'prepared_at' => 'datetime',
        'served_at'   => 'datetime',
        'unit_price'  => 'decimal:2',
        'subtotal'    => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
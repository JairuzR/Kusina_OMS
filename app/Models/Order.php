<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number', 'table_id', 'waiter_id', 'cashier_id',
        'status', 'type', 'guests', 'subtotal', 'tax_amount',
        'discount_amount', 'discount_type', 'total_amount',
        'notes', 'served_at', 'completed_at',
    ];

    protected $casts = [
        'served_at'    => 'datetime',
        'completed_at' => 'datetime',
        'subtotal'     => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function waiter()
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'yellow',
            'preparing' => 'blue',
            'ready'     => 'purple',
            'served'    => 'indigo',
            'billed'    => 'orange',
            'completed' => 'green',
            'cancelled' => 'red',
            default     => 'gray',
        };
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'cashier_id', 'payment_method',
        'amount_paid', 'change_amount', 'reference_number',
        'status', 'notes',
    ];

    protected $casts = [
        'amount_paid'   => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
}
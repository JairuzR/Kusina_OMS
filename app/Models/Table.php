<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    protected $fillable = [
        'name', 'capacity', 'status',
        'location', 'assigned_waiter_id', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function waiter()
    {
        return $this->belongsTo(User::class, 'assigned_waiter_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'available'      => 'green',
            'occupied'       => 'red',
            'reserved'       => 'yellow',
            'needs_cleaning' => 'gray',
            default          => 'gray',
        };
    }
}
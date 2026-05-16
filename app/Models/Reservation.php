<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'table_id', 'user_id', 'customer_name',
        'customer_phone', 'customer_email', 'party_size',
        'reservation_date', 'reservation_time', 'status',
        'notes', 'reminder_sent',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'reminder_sent'    => 'boolean',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
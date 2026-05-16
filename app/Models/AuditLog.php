<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActionBadgeAttribute(): string
    {
        return match ($this->action) {
            'created', 'POST' => 'green',
            'updated', 'PUT', 'PATCH' => 'yellow',
            'deleted', 'DELETE' => 'red',
            'login' => 'blue',
            'logout' => 'gray',
            default => 'gray',
        };
    }
}
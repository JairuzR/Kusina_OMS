<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    protected $fillable = [
        'provider',
        'feature',
        'model_used',
        'input_tokens',
        'output_tokens',
        'cost_estimate',
        'success',
        'error_message',
    ];

    protected $casts = [
        'success'       => 'boolean',
        'cost_estimate' => 'decimal:6',
    ];
}
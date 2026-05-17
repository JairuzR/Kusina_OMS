<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiReorderSuggestion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'inventory_item_id',
        'suggested_quantity',
        'urgency',
        'reasoning',
        'recommended_supplier',
        'key_insights',
        'estimated_days_until_stockout',
        'provider_used',
        'is_acted_on',
    ];

    protected $casts = [
        'key_insights'      => 'array',
        'is_acted_on'       => 'boolean',
        'suggested_quantity'=> 'decimal:2',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function getUrgencyColorAttribute(): string
    {
        return match ($this->urgency) {
            'critical' => 'red',
            'high'     => 'orange',
            'medium'   => 'yellow',
            'low'      => 'blue',
            default    => 'gray',
        };
    }
}
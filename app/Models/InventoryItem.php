<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'supplier_id', 'name', 'sku', 'unit',
        'quantity', 'min_quantity', 'cost_per_unit',
        'category', 'expiry_date', 'storage_location', 'is_active',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'expiry_date' => 'date',
        'quantity'    => 'decimal:2',
        'min_quantity'=> 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->min_quantity;
    }
}
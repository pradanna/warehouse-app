<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'item_id',
        'unit_id',
        'sku',
        'description',
        'current_stock',
        'min_stock',
        'max_stock',
        'modified_by'
    ];

    protected $casts = [
        'current_stock' => 'float',
        'min_stock' => 'float',
        'max_stock' => 'float',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function prices()
    {
        return $this->hasMany(InventoryPrice::class, 'inventory_id');
    }

    public function modifiedBy()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }
}

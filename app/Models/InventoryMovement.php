<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'inventory_id',
        'type',
        'quantity',
        'description',
        'movement_type',
        'movement_reference'
    ];

    protected $casts = [
        'quantity' => 'float'
    ];

    public function invetory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}

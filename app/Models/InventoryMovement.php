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
        'quantity_open',
        'quantity',
        'quantity_close',
        'description',
        'movement_type',
        'movement_reference',
        'author_id'
    ];

    protected $casts = [
        'quantity_open' => 'float',
        'quantity' => 'float',
        'quantity_close' => 'float'
    ];

    public function invetory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}

<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    // Menentukan kolom yang dapat diisi
    protected $fillable = [
        'purchase_id',
        'item_id',
        'unit_id',
        'quantity',
        'price',
        'total',
    ];

    protected $casts = [
        'quantity' => 'float',
        'price' => 'float',
        'total' => 'float'
    ];

    /**
     * Relasi dengan Purchase (many-to-one)
     * Setiap purchase_item berhubungan dengan satu purchase
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    /**
     * Relasi dengan Item (many-to-one)
     * Setiap purchase_item berhubungan dengan satu item
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}

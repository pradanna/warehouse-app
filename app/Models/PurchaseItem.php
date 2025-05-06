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
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Relasi dengan Item (many-to-one)
     * Setiap purchase_item berhubungan dengan satu item
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    // Menentukan kolom yang dapat diisi
    protected $fillable = [
        'inventory_id',
        'date',
        'quantity',
        'type',
        'description',
        'author_id',
    ];

    protected $casts = [
        'quantity' => 'float'
    ];

    /**
     * Relasi dengan Item (many-to-one)
     * Setiap inventory adjustment berhubungan dengan satu item
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    /**
     * Relasi dengan User (many-to-one)
     * Setiap inventory adjustment berhubungan dengan satu user (yang melakukan penyesuaian)
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}

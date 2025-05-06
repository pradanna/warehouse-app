<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'supplier_id',
        'date',
        'reference_number',
        'sub_total',
        'discount',
        'tax',
        'total',
        'description',
        'payment_type',
        'payment_status'
    ];

    protected $casts = [
        'sub_total' => 'float',
        'discount' => 'float',
        'tax' => 'float',
        'total' => 'float',
    ];

    /**
     * Relasi dengan Supplier (many-to-one)
     * Setiap purchase berhubungan dengan satu supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relasi dengan PurchaseItem (one-to-many)
     * Setiap purchase memiliki banyak purchase_item
     */
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}

<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    //
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'outlet_id',
        'date',
        'reference_number',
        'sub_total',
        'discount',
        'tax',
        'total',
        'description',
        'payment_type',
        'payment_status',
        'author_id'
    ];

    protected $casts = [
        'sub_total' => 'float',
        'discount' => 'float',
        'tax' => 'float',
        'total' => 'float',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class, 'sale_id');
    }

    public function payment()
    {
        return $this->hasOne(SalePayment::class, 'sale_id');
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class, 'sale_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function credit()
    {
        return $this->hasOne(Credit::class, 'sale_id');
    }
}

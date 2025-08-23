<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutletPastryItem extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'outlet_pastry_id',
        'name',
        'quantity',
        'price',
        'total',
    ];

    protected $casts = [
        'quantity' => 'float',
        'price' => 'float',
        'total' => 'float',
    ];

    public function outlet_pastry()
    {
        return $this->belongsTo(OutletPastry::class, 'outlet_pastry_id');
    }
}

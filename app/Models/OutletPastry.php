<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutletPastry extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'outlet_id',
        'date',
        'reference_number',
        'sub_total',
        'discount',
        'total',
        'author_id',
    ];

    protected $casts = [
        'sub_total' => 'float',
        'discount' => 'float',
        'total' => 'float',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function items()
    {
        return $this->hasMany(OutletPastryItem::class, 'outlet_pastry_id');
    }

     public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}

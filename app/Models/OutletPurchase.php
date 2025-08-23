<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutletPurchase extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'sale_id',
        'cash_flow_id',
        'outlet_id',
        'date',
        'cash',
        'digital',
        'amount',
    ];

    protected $casts = [
        'cash' => 'float',
        'digital' => 'float',
        'amount' => 'float',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function cash_flow()
    {
        return $this->belongsTo(CashFlow::class, 'cash_flow_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }
}

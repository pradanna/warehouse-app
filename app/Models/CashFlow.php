<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'outlet_id',
        'date',
        'type',
        'name',
        'cash',
        'digital',
        'amount',
        'description',
        'reference_type',
        'reference_key',
        'author_id',
    ];

    protected $casts = [
        'cash' => 'float',
        'digital' => 'float',
        'amount' => 'float',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function income()
    {
        return $this->hasOne(OutletIncome::class, 'cash_flow_id');
    }
}

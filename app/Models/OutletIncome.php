<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutletIncome extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'outlet_id',
        'cash_flow_id',
        'date',
        'name',
        'cash',
        'digital',
        'total',
        'by_mutation',
        'mutation_date',
        'description',
        'author_id',
    ];

    protected $casts = [
        'cash' => 'float',
        'digital' => 'float',
        'total' => 'float',
        'by_mutation' => 'float',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function cash_flow()
    {
        return $this->belongsTo(CashFlow::class, 'cash_flow_id');
    }
}

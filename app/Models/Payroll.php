<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'outlet_id',
        'outlet_expense_id',
        'date',
        'amount',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function outlet_expense()
    {
        return $this->belongsTo(OutletExpense::class, 'outlet_expense_id');
    }

    public function items()
    {
        return $this->hasMany(PayrollItem::class, 'payroll_id');
    }
}

<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundTransfer extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'outlet_id',
        'credit_cash_flow_id',
        'debit_cash_flow_id',
        'date',
        'transfer_to',
        'amount',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function credit_cash_flow()
    {
        return $this->belongsTo(CashFlow::class, 'credit_cash_flow_id');
    }

    public function debit_cash_flow()
    {
        return $this->belongsTo(CashFlow::class, 'debit_cash_flow_id');
    }
}

<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'purchase_id',
        'amount_due',
        'amount_paid',
        'amount_rest',
        'due_date'
    ];

    protected $casts = [
        'amount_due' => 'float',
        'amount_paid' => 'float',
        'amount_rest' => 'float'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }
}

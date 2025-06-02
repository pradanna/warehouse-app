<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'sale_id',
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

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }
}

<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePayment extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'sale_id',
        'date',
        'payment_type',
        'amount',
        'description',
        'evidence',
        'author_id'
    ];

    protected $casts = [
        'amount' => 'float'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    /*
    *
    */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

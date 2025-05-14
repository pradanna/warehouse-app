<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'address',
        'contact'
    ];

    // Relasi ke transaksi (1 ke banyak)
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'outlet_id');
    }
}

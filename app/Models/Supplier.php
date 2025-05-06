<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'address',
        'contact'
    ];

    /**
     * Akses untuk mendapatkan nama supplier dan informasi kontak
     */
    public function getSupplierInfoAttribute()
    {
        return $this->name . ' (' . ($this->contact_person ?? 'No Contact') . ')';
    }
}

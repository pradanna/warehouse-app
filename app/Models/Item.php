<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'category_id',
        'material_category_id',
        'name',
        'description',
    ];

    // Relasi ke model Category (banyak ke 1)
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function material_category()
    {
        return $this->belongsTo(MaterialCategory::class, 'material_category_id');
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }
}

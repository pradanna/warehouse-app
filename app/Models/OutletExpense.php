<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutletExpense extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'outlet_id',
        'expense_category_id',
        'date',
        'amount',
        'description',
        'author_id',
    ];

    protected $casts = [
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

    public function expense_category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}

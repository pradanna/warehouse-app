<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends SpatieRole
{
    //
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;
}

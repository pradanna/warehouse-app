<?php

namespace App\Http\Resources\Unit;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;

class UnitResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}

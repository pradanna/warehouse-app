<?php

namespace App\Http\Resources\Outlet;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OutletResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'contact' => $this->contact,
        ];
    }
}

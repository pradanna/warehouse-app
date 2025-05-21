<?php

namespace App\Http\Resources\Supplier;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;

class SupplierResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'contact' => $this->contact,
            'address' => $this->address,
        ];
    }
}

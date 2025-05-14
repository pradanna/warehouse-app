<?php

namespace App\Http\Resources\Inventory;

use App\Commons\Http\BaseApiCollection;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;

class InventoryCollection extends BaseApiCollection implements Responsable
{
    public function toArray(Request $request): array
    {
        return $this->collection->transform(function ($inventory) {
            return new InventoryResource($inventory);
        })->all();
    }
}

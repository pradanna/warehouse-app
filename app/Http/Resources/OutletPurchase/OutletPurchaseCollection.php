<?php

namespace App\Http\Resources\OutletPurchase;

use App\Commons\Http\BaseApiCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OutletPurchaseCollection extends BaseApiCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->transform(function ($outletPurchase) {
            return new OutletPurchaseResource($outletPurchase);
        })->all();
    }
}

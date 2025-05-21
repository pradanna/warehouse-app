<?php

namespace App\Http\Resources\Sale;

use App\Commons\Http\BaseApiCollection;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SaleCollection extends BaseApiCollection implements Responsable
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->transform(function ($sale) {
            return new SaleResource($sale);
        })->all();
    }
}

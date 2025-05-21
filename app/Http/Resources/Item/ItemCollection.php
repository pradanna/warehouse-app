<?php

namespace App\Http\Resources\Item;

use App\Commons\Http\BaseApiCollection;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;

class ItemCollection extends BaseApiCollection implements Responsable
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->transform(function ($item) {
            return new ItemResource($item);
        })->all();
    }
}

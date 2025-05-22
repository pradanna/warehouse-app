<?php

namespace App\Http\Resources\Inventory;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;

class InventoryResource extends BaseApiResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->whenLoaded('item', function () {
                return $this->item->name;
            }),
            'unit' => $this->whenLoaded('unit', function () {
                return $this->unit->name;
            }),
            'sku' => $this->sku,
            'description' => $this->description,
            'current_stock' => $this->current_stock,
            'min_stock' => $this->min_stock,
            'max_stock' => $this->max_stock,
            'prices' => $this->relationLoaded('prices') ?
                $this->prices->map(function ($price) {
                    return [
                        'id' => $price->id,
                        'outlet' => $price->relationLoaded('outlet') ? [
                            'id' => $price->outlet->id,
                            'name' => $price->outlet->name
                        ] : null,
                        'price' => $price->price
                    ];
                }) : [],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}

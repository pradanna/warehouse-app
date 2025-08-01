<?php

namespace App\Http\Resources\Item;

use App\Commons\Http\BaseApiResource;
use Illuminate\Http\Request;

class ItemResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        $response = [
            'id' => $this->id,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name
                ];
            }) ?? null,
            'name' => $this->name,
            'description' => $this->description,
        ];

        if ($this->relationLoaded('material_category')) {
            $response['material_category'] = $this->material_category ? [
                'id' => $this->material_category->id,
                'name' => $this->material_category->name
            ] : null;
        }

        return $response;
    }
}
